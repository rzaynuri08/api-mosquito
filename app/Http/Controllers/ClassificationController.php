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
        // Validasi input
        $validated = $request->validate([
            'image_abdomen' => 'required|image|mimes:jpg,jpeg,png',
            'image_body' => 'required|image|mimes:jpg,jpeg,png',
            'image_head' => 'required|image|mimes:jpg,jpeg,png',
            'genus_name' => 'required|string',
            'confidence' => 'required|numeric',
            'user_id' => 'required|integer',
        ]);

        try {
            // Simpan file
            $filenameAbdomen = time() . '_abdomen_' . $request->file('image_abdomen')->getClientOriginalName();
            $filenameBody = time() . '_body_' . $request->file('image_body')->getClientOriginalName();
            $filenameHead = time() . '_head_' . $request->file('image_head')->getClientOriginalName();

            $pathAbdomen = $request->file('image_abdomen')->storeAs('scan', $filenameAbdomen, 'public');
            $pathBody = $request->file('image_body')->storeAs('scan', $filenameBody, 'public');
            $pathHead = $request->file('image_head')->storeAs('scan', $filenameHead, 'public');

            // Cari atau buat genus
            $genus = Genus::firstOrCreate(['name' => $validated['genus_name']]);

            // Simpan history dulu
            $history = History::create([
                'id_user' => $validated['user_id'],
                'final_label' => $validated['genus_name'],
                'final_confidence' => $validated['confidence'],
            ]);

            // Simpan attachment sambil bawa id_history
            $attachment = ScanAttachment::create([
                'id_history' => $history->id_history,
                'id_genus' => $genus->id_genus,
                'name' => $filenameAbdomen, // bisa diganti json untuk simpan 3 file
                'confidence' => $validated['confidence'],
                'path_abdomen' => $pathAbdomen,
                'path_body' => $pathBody,
                'path_head' => $pathHead
            ]);

            // // Update history dengan id_attachment (opsional, kalau mau tau lampirannya)
            // $history->update(['id_attachment' => $attachment->id_attachment]);

            return response()->json([
                'message' => 'Classification saved successfully',
                'genus_id' => $genus->id_genus,
                'attachment_id' => $attachment->id_attachment,
                'history_id' => $history->id_history,
                'image_urls' => [
                    'abdomen' => asset('storage/' . $pathAbdomen),
                    'body' => asset('storage/' . $pathBody),
                    'head' => asset('storage/' . $pathHead),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while saving classification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
