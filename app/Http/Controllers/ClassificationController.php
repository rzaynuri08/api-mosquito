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
        // 🔹 Debug awal: log semua data yang dikirim
        Log::info('➡️ ClassificationController@store called', $request->all());

        try {
            // Validasi sederhana
            $request->validate([
                'user_id'    => 'required|integer',
                'genus_name' => 'required|string',
                'confidence' => 'required|numeric',
                'images.*'   => 'nullable|file|mimes:jpg,jpeg,png',
            ]);

            // 🔹 Buat record history
            $history = History::create([
                'id_user'          => $request->user_id,
                'final_label'      => $request->genus_name,
                'final_confidence' => $request->confidence,
            ]);

            // 🔹 Cari genus
            $genus = Genus::where('name', $request->genus_name)->first();

            $attachments = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('scan', $filename, 'public');

                    $attachment = ScanAttachment::create([
                        'id_history' => $history->id_history,
                        'id_genus'   => $genus ? $genus->id_genus : null,
                        'name'       => $filename,
                        'confidence' => $request->confidence,
                    ]);

                    $attachments[] = [
                        'id_attachment' => $attachment->id_attachment,
                        'file_name'     => $filename,
                        'file_url'      => Storage::url($path),
                        'confidence'    => $request->confidence,
                    ];
                }
            }

            // 🔹 Tambahkan log setelah insert sukses
            Log::info('✅ Data berhasil disimpan', [
                'history_id'  => $history->id_history,
                'attachments' => $attachments
            ]);

            return response()->json([
                'status'     => 'success',
                'id_history' => $history->id_history,
                'genus'      => $request->genus_name,
                'confidence' => $request->confidence,
                'images'     => $attachments
            ], 201);

        } catch (\Exception $e) {
            // 🔹 Log error jika ada
            Log::error('❌ Error in ClassificationController@store', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
