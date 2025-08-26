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
    $request->validate([
        'images.*' => 'required|image|mimes:jpg,jpeg,png',
        'genus' => 'required|string',
        'confidence' => 'required|numeric',
        'id_user' => 'required|integer',
    ]);

    // Cari / buat genus
    $genus = Genus::firstOrCreate(['name' => $request->genus]);

    // Buat history dulu
    $history = History::create([
        'id_user' => $request->id_user,
        'final_label' => null,
        'final_confidence' => null,
    ]);

    $attachments = [];
    foreach ($request->file('images') as $file) {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('scan', $filename, 'public');

        $attachments[] = ScanAttachment::create([
            'name' => $filename,
            'id_genus' => $genus->id_genus,
            'confidence' => $request->confidence,
            'id_history' => $history->id_history,
        ]);
    }

    return response()->json([
        'id_history' => $history->id_history,
        'id_user' => $history->id_user,
        'images' => collect($attachments)->map(fn($a) => [
            'id_attachment' => $a->id_attachment,
            'file_name' => $a->name,
            'file_url' => asset('storage/scan/' . $a->name),
            'confidence' => $a->confidence,
        ]),
        'genus_name' => $genus->name,
        'prevention' => $genus->prevention->description ?? null,
        'disease_risk' => $genus->diseaseRisk->description ?? null,
        'final_label' => $history->final_label,
        'final_confidence' => $history->final_confidence,
        'created_at' => $history->created_at,
    ]);
}

}
