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
            'images' => 'required|array|size:3', // harus 3 gambar
            'images.*' => 'image|mimes:jpg,jpeg,png',
            'genus_name' => 'required|string',
            'confidence' => 'required|numeric',
            'user_id' => 'required|integer',
        ]);

        // Cari atau buat genus berdasarkan nama
        $genus = Genus::firstOrCreate([
            'name' => ucfirst(strtolower($request->genus_name))
        ]);

        // Buat History terlebih dahulu
        $history = History::create([
            'id_user' => $request->user_id,
            'final_label' => $genus->name,
            'final_confidence' => $request->confidence
        ]);

        $attachments = [];
        foreach ($request->file('images') as $index => $img) {
            // Simpan file dengan nama unik
            $filename = time() . '_' . uniqid() . '_' . $img->getClientOriginalName();
            $img->storeAs('scan', $filename, 'public');

            // Simpan ke tabel ScanAttachment (dengan id_history sama)
            $attachment = ScanAttachment::create([
                'name' => $filename,
                'id_genus' => $genus->id_genus,
                'confidence' => $request->confidence,
                'id_history' => $history->id_history
            ]);

            // Simpan attachment pertama sebagai referensi id_attachment di history
            if ($index === 0) {
                $history->update(['id_attachment' => $attachment->id_attachment]);
            }

            $attachments[] = [
                'id_attachment' => $attachment->id_attachment,
                'file_name' => $attachment->name,
                'file_url' => asset('storage/scan/' . $attachment->name),
                'confidence' => $attachment->confidence
            ];
        }

        return response()->json([
            'message' => 'Classification (multi-view) saved successfully',
            'user_id' => $request->user_id,
            'genus_id' => $genus->id_genus,
            'genus_name' => $genus->name,
            'final_confidence' => $request->confidence,
            'id_history' => $history->id_history,
            'attachments' => $attachments
        ]);
    }
}
