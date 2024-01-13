<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlubSepakbola extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_klub",
        "nama_klub",
        "tgl_berdiri",
        "kota_klub",
        "peringkat",
        "harga_klub",
        "kondisi_klub"
    ];

    protected $table = "klub_sepakbola";
}
