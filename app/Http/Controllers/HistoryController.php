<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\History;

class HistoryController extends Controller
{
    public function index()
    {
        $data = History::with([
            'attachment.genus.prevention',
            'attachment.genus.disease',
            'attachment.genus.attachments' // pastikan relasi ini ada di model Genus
        ])->orderByDesc('created_at')->get();

        $result = $data->map(function ($item) {
            // Ambil semua attachment untuk genus yang sama
            $attachments = $item->attachment->genus->attachments ?? collect([$item->attachment]);
            // Urutkan berdasarkan id_attachment (atau urutan upload)
            $attachments = $attachments->sortBy('id_attachment')->values();

            // Ambil url gambar
            $images = $attachments->map(function ($att) {
                return [
                    'id_attachment' => $att->id_attachment,
                    'file_name' => $att->name,
                    'file_url' => asset('storage/scan/' . $att->name),
                    'confidence' => $att->confidence
                ];
            });

            return [
                'id_history' => $item->id_history,
                'id_user' => $item->id_user,
                'images' => $images, // array berisi 3 gambar
                'genus_name' => $item->attachment->genus->name ?? null,
                'prevention' => $item->attachment->genus->prevention->description ?? null,
                'disease_risk' => $item->attachment->genus->disease->description ?? null,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json($result);
    }
}
