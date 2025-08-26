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

        $attachments = [];
        foreach ($request->file('images') as $img) {
            // Simpan file dengan nama unik
            $filename = time() . '_' . uniqid() . '_' . $img->getClientOriginalName();
            $img->storeAs('scan', $filename, 'public');

            // Simpan ke tabel ScanAttachment
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

        // Simpan ke tabel History (pakai attachment pertama sebagai referensi)
        History::create([
            'id_user' => $request->user_id,
            'id_attachment' => $attachments[0]['id_attachment'],
            'final_label' => $genus->name,         // tambahan: simpan hasil label
            'final_confidence' => $request->confidence // tambahan: simpan rata-rata confidence
        ]);

        return response()->json([
            'message' => 'Classification (multi-view) saved successfully',
            'user_id' => $request->user_id,
            'genus_id' => $genus->id_genus,
            'genus_name' => $genus->name,
            'final_confidence' => $request->confidence,
            'attachments' => $attachments
        ]);
    }
}
