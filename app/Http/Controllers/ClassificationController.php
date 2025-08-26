<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Genus;
use App\Models\ScanAttachment;
use App\Models\History;

class ClassificationController extends Controller
{
    public function store(Request $request)
    {
        // 🔹 Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png',
            'genus' => 'required|string',
            'confidence' => 'required|numeric',
            'id_user' => 'required|integer',
        ]);

        // 🔹 Simpan file gambar
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename, 'public');

        // 🔹 Cari atau buat genus
        $genus = Genus::firstOrCreate(['name' => $request->genus]);

        // 🔹 Buat dulu history (tanpa id_attachment)
        $history = History::create([
            'id_user' => $request->id_user,
            'final_label' => $genus->name,
            'final_confidence' => $request->confidence,
        ]);

        // 🔹 Simpan attachment dengan id_history yang baru dibuat
        $attachment = ScanAttachment::create([
            'name' => $filename,
            'id_genus' => $genus->id_genus,
            'confidence' => $request->confidence,
            'id_history' => $history->id_history, // <--- penting
        ]);

        // 🔹 Update kembali history dengan id_attachment yang barusan
        $history->update([
            'id_attachment' => $attachment->id_attachment,
        ]);

        return response()->json([
            'message' => 'Klasifikasi berhasil disimpan',
            'history' => $history->load('attachments', 'attachments.genus'),
        ]);
    }
}
