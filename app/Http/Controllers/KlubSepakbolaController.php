<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\KlubSepakbola;

class KlubSepakbolaController extends Controller
{
    // Create Klub sepakbola
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_klub'      => 'required|string|max:10',
            'nama_klub'    => 'required|string|max:20',
            'tgl_berdri'   => 'required|date:Y-m-d',
            'kondisi_klub' => 'required|integer',
            'kota_klub'    => 'required|string',
            'peringkat'    => 'required|string|max:10',
            'harga_klub'   => 'required|integer',
        ]);
        // Validasi user input sebelum di proses
        if($validator->fails()) {
            return response()->
            json([
                'status'    => 400,
                'message'   => $validator->errors()
            ], 400);
        }
        try {
            $id_klub = $request->id_klub;
            // Cek ID klub sudah ada atau belum?
            if(!empty($data = KlubSepakbola::where('id_supporter', $id_klub)->first())) {
                return response()->json([
                    'status'=> 'NOT OK',
                    'message' => 'ID Klub is already used!'
                ] ,409);
            }
            KlubSepakbola::create([
                'id_klub'       => $id_klub,
                'nama_klub'     => $request->nama_klub,
                "tgl_berdri"    => $request->tgl_berdri,
                "kondisi_klub"  => $request->kondisi_klub,
                "kota_klub"     => $request->kota_klub,
                "peringkat"     => $request->peringkat,
                "harga_klub"    => $request->harga_klub
            ]);
            return response()->json([
                "status"     => "OK",
                "message"    => "Klub Sepakbola created successfully"
            ], 201);
        } catch (\Exception $err) {
            return response()->json([
                "status"    => "Error",
                "message"   => "Internal server error"
            ],500);
        }
    }

    // Read Klub sepakbola
    public function read(Request $request) {
        $data = [];
        $klub = new KlubSepakbola();
        $limit = 10;
        $page_offset = 0;
        $total_data = 0;
        $total_page = 1;
        $current_page = 1;
        if(!empty($request->input("page_offset"))) {
            if(is_numeric($request->input("page_offset"))) {
                $page_offset = $request->input("page_offset") <= 0 ? 0 : ($request->input("page_offset") - 1) * $limit;
            }
        }
        if(!empty($request->input("search")) && !empty($request->input("value"))) {
            $data = $klub->where($request->input("search"), $request->input('value'))->offset($page_offset)->limit($limit)->get();
            $total_data = $klub->where($request->input("search"), $request->input('value'))->count();
        }
        else {
            $data = $klub->offset($page_offset)->limit($limit)->get();
            $total_data = $klub->count();
        }
        if($total_data > $limit) {
            $total_page = ceil($total_data / $limit);
        }
        if($page_offset >= $limit) {
            $current_page = ($page_offset + $limit) / $limit;
        }
        return response()->json([
            'total_data'    => $total_data,
            'total_page'    => $total_page,
            'current_page'  => $current_page,
            'data'          => $data
        ], 200);
    }

    // Update Klub sepakbola
    // Note jika menggunakan method PUT dan menggunakan tipe Form Data sebagai input pastikan tambahin key _method = PUT di Form Datanya
    // Jadi waktu manggil API tetap pake POST tapi cuma ketambahan _method = PUT di Form Data
    // Laravel ngebug kalo nggak pake _method = PUT
    public function update(Request $request, $id_klub) {
        $validator = Validator::make($request->all(), [
            'nama_klub'    => 'required|string|max:20',
            'tgl_berdri'   => 'required|date:Y-m-d',
            'kondisi_klub' => 'required|integer',
            'kota_klub'    => 'required|string',
            'peringkat'    => 'required|string|max:10',
            'harga_klub'   => 'required|integer',
        ]);
        // Validasi user input sebelum di proses
        if($validator->fails()) {
            return response()->
            json([
                'status'    => 400,
                'message'   => $validator->errors()
            ], 400);
        }
        try {
            $klub_sepakbola = new KlubSepakbola();
            if(empty($klub = $klub_sepakbola->where('id_klub', $id_klub)->first())) {
                return response()->json([
                    'status'=> 'NOT OK',
                    'message' => 'Klub is not found!'
                ] ,404);
            }

            $update_data = [
                "nama_klub"     => $request->nama_klub,
                "tgl_berdri"    => $request->tgl_berdri,
                "kondisi_klub"  => $request->kondisi_klub,
                "kota_klub"     => $request->kota_klub,
                "peringkat"     => $request->peringkat,
                "harga_klub"    => $request->harga_klub,
            ];

            $klub_sepakbola->where('id_klub', $id_klub)->update($update_data);
            return response()->json([
                "status"     => "OK",
                "message"    => "Klub Sepakbola updated successfully"
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                "status"    => "Error",
                "message"   => "Internal server error"
            ], 500);
        }
    }

    // Delete Klub sepakbola
    public function delete(Request $request, $id_klub) {
        $klub = new KlubSepakbola();
        if(empty($data = $klub->where('id_klub', $id_klub)->first())) {
            return response()->json([
                'status'=> 'NOT OK',
                'message' => 'Klub not found!'
            ] ,404);
        }
        $klub->where('id_klub', $id_klub)->delete();
        return response()->json([
            'status'    => 'OK',
            'message'   => 'Klub is deleted successfully'
        ], 200);
    }
}
