<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\History;

class HistoryController extends Controller
{
    public function index()
    {
        // Ambil semua history dengan relasi attachments -> genus -> prevention & disease
        $data = History::with([
            'attachments.genus.prevention',
            'attachments.genus.disease',
        ])->orderByDesc('created_at')->get();

        $result = $data->map(fn($item) => $this->formatHistory($item));

        return response()->json($result);
    }

    public function show($id)
    {
        $history = History::with([
            'attachments.genus.prevention',
            'attachments.genus.disease',
        ])->findOrFail($id);

        return response()->json($this->formatHistory($history));
    }

    /**
     * Format single history ke bentuk JSON response
     */
    private function formatHistory($item)
    {
        // Ambil semua attachments sesuai history
        $attachments = $item->attachments->sortBy('id_attachment')->values();

        // Format attachments
        $images = $attachments->map(function ($att) {
            return [
                'id_attachment' => $att->id_attachment,
                'file_name'     => $att->name,
                'file_url'      => asset('storage/scan/' . $att->name),
                'confidence'    => $att->confidence
            ];
        });

        // Ambil genus dari attachment pertama
        $firstAttachment = $attachments->first();

        return [
            'id_history'       => $item->id_history,
            'id_user'          => $item->id_user,
            'images'           => $images, // biasanya 3 gambar
            'genus_name'       => $firstAttachment->genus->name ?? null,
            'prevention'       => $firstAttachment->genus->prevention->description ?? null,
            'disease_risk'     => $firstAttachment->genus->disease->description ?? null,
            'final_label'      => $item->final_label,
            'final_confidence' => $item->final_confidence,
            'created_at'       => $item->created_at,
        ];
    }
}
