<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Genus;
use App\Models\ScanAttachment;
use App\Models\History;

class ClassificationController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png',
            'genus_name' => 'required|string',
            'confidence' => 'required|numeric',
            'user_id' => 'required|integer',
        ]);

        // Simpan file ke storage/app/public/scan
        $filename = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('scan', $filename, 'public');

        // Simpan atau cari genus
        $genus = Genus::firstOrCreate(['name' => $request->genus_name]);

        // Simpan scan attachment
        $attachment = ScanAttachment::create([
            'name' => $filename,
            'id_genus' => $genus->id_genus,
            'confidence' => $request->confidence
        ]);

        // Simpan history
        History::create([
            'id_user' => $request->user_id,
            'id_attachment' => $attachment->id_attachment
        ]);

        return response()->json([
            'message' => 'Classification saved successfully',
            'image_url' => asset('storage/scan/' . $filename),
            'attachment_id' => $attachment->id_attachment,
            'genus_id' => $genus->id_genus
        ]);
    }
}
