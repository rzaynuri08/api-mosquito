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
            'attachment.genus.disease'
        ])->orderByDesc('created_at')->get();

        $result = $data->map(function ($item) {
            return [
                'id_history' => $item->id_history,
                'id_user' => $item->id_user,
                'image_name' => $item->attachment->name ?? null,
                'confidence' => $item->attachment->confidence ?? null,
                'genus_name' => $item->attachment->genus->name ?? null,
                'prevention' => $item->attachment->genus->prevention->description ?? null,
                'disease_risk' => $item->attachment->genus->disease->description ?? null,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json($result);
    }
}
