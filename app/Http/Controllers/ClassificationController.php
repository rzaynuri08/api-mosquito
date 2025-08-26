<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genus;
use App\Models\ScanAttachment;
use App\Models\History;

class ClassificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png',
            'genus_name' => 'required|string',
            'confidence' => 'required|numeric',
            'user_id' => 'required|integer',
        ]);

        // Cari atau buat genus
        $genus = Genus::firstOrCreate(['name' => ucfirst(strtolower($request->genus_name))]);

        $attachments = [];
        foreach ($request->file('images') as $img) {
            $filename = time() . '_' . uniqid() . '_' . $img->getClientOriginalName();
            $img->storeAs('scan', $filename, 'public');

            $attachment = ScanAttachment::create([
                'name' => $filename,
                'id_genus' => $genus->id_genus,
                'confidence' => $request->confidence
            ]);

            $attachments[] = [
                'id_attachment' => $attachment->id_attachment,
                'file_name' => $attachment->name,
                'file_url' => asset('storage/scan/' . $attachment->name),
                'confidence' => $attachment->confidence
            ];
        }

        // Simpan history → pakai attachment pertama
        History::create([
            'id_user' => $request->user_id,
            'id_attachment' => $attachments[0]['id_attachment']
        ]);

        return response()->json([
            'message' => 'Classification (multi-view) saved successfully',
            'genus_id' => $genus->id_genus,
            'attachments' => $attachments
        ]);
    }
}
