<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\ScanAttachment;

class HistoryController extends Controller
{
    public function index()
    {
        $data = History::with([
            'attachment.genus.prevention',
            'attachment.genus.disease',
        ])->orderByDesc('created_at')->get();

        $result = $data->map(function ($item) {
            // Ambil semua attachment sesuai id_history
            $attachments = ScanAttachment::where('id_history', $item->id_history)
                ->orderBy('id_attachment')
                ->get();

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
