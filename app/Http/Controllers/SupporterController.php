<?php

namespace App\Http\Controllers;

use App\Models\Supporters;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Supporter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class SupporterController extends Controller {

    // Create Supporter
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_supporter'  => 'required|string|max:10',
            'nama'          => 'required|string|max:20',
            'alamat'        => 'required|string|max:50',
            'no_telpon'     => 'required|string|max:15',
            'tgl_daftar'    => 'required|date:Y-m-d',
            'foto'          => 'required|mimes:png,jpg,jpeg|max:2048'
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
            // Upload gambar ke server
            $name = now()->timestamp.".{$request->foto->getClientOriginalName()}";
            $path = $request->file('foto')->storeAs('files', $name, 'public');
            $id_supporter = $request->id_supporter;
            // Cek ID supporter sudah ada atau belum?
            if(!empty($data =Supporter::where('id_supporter', $id_supporter)->first())) {
                return response()->json([
                    'status'=> 'NOT OK',
                    'message' => 'ID Supporter is already used!'
                ] ,409);
            }
            Supporter::create([
                'id_supporter'  => $id_supporter,
                'foto'          => "/storage/{$path}",
                "nama"          => $request->nama,
                "alamat"        => $request->alamat,
                "tgl_daftar"    => $request->tgl_daftar,
                "no_telpon"     => $request->no_telpon
            ]);
            return response()->json([
                "status"     => "OK",
                "message"    => "Supporter created successfully"
            ], 201);
        } catch (\Exception $err) {
            return response()->json([
                "status"    => "Error",
                "message"   => "Internal server error"
            ],500);
        }
    }

    // Read Supporter
    public function read(Request $request) {
        $data = [];
        $supporter = new Supporter();
        $limit = 10;
        $page_offset = 0;
        $total_data = 0;
        $total_page = 1;
        $current_page = 1;
        if(!empty($request->input("page_offset"))) {
            if(is_numeric($request->input("page_offset"))) {
                $page_offset = $request->input("page_offset") <= 0 ? 0 : ($request->input("offset") - 1) * $limit;
            }
        }
        if(!empty($request->input("search")) && !empty($request->input("value"))) {
            $data = $supporter->where($request->input("search"), $request->input('value'))->offset($page_offset)->limit($limit)->get();
            $total_data = $supporter->where($request->input("search"), $request->input('value'))->count();
        }
        else {
            $data = $supporter->offset($page_offset)->limit($limit)->get();
            $total_data = $supporter->count();
        }
        if($total_data >= $limit) {
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

    // Update Supporter
    // Note jika menggunakan method PUT dan menggunakan tipe Form Data sebagai input pastikan tambahin key _method = PUT di Form Datanya
    // Jadi waktu manggil API tetap pake POST tapi cuma ketambahan _method = PUT di Form Data
    // Laravel ngebug kalo nggak pake _method = PUT
    public function update(Request $request, $id_supporter) {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:20',
            'alamat'        => 'required|string|max:50',
            'no_telpon'     => 'required|string|max:15',
            'tgl_daftar'    => 'required|date:Y-m-d',
            'foto'          => 'sometimes|mimes:png,jpg,jpeg|max:2048'
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
            // Cek data apakah ada data supporter?
            $supporter = new Supporter();
            // Kalo ada ambil dan assign ke variable $data
            if(empty($data = $supporter->where('id_supporter', $id_supporter)->first())) {
                return response()->json([
                    'status'=> 'NOT OK',
                    'message' => 'Supporter not found!'
                ] ,404);
            }
            $update_data = [
                'id_supporter'  => $id_supporter,
                "nama"          => $request->nama,
                "alamat"        => $request->alamat,
                "tgl_daftar"    => $request->tgl_daftar,
                "no_telpon"     => $request->no_telpon
            ];
            // Update foto jika ditemukan dalam input user
            if(!empty($request->foto)) {
                // Delete image lama jika ada
                $image_path = public_path($data->foto);
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
                // Upload gambar ke server
                $name = now()->timestamp.".{$request->foto->getClientOriginalName()}";
                $path = $request->file('foto')->storeAs('files', $name, 'public');
                $update_data['foto'] = "/storage/${path}";
            }
            $supporter->where('id_supporter', $id_supporter)->update($update_data);
            return response()->json([
                "status"     => "ok",
                "message"    => "Supporter updated successfully"
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                "status"    => "Error",
                "message"   => "Internal server error"
            ],500);
        }
    }

    // Delete Supporter
    public function delete(Request $request, $id_supporter) {
        $supporter = new Supporter();
        if(empty($data = $supporter->where('id_supporter', $id_supporter)->first())) {
            return response()->json([
                'status'=> 'NOT OK',
                'message' => 'Supporter not found!'
            ] ,404);
        }
        // Hapus gambar dari server saat user di hapus
        $image_path = public_path($data->foto);
        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        $supporter->where('id_supporter', $id_supporter)->delete();
        return response()->json([
            'status'    => 'OK',
            'message'   => 'Supporter is deleted successfully'
        ], 200);
    }
}