<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supporter extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_supporter",
        "nama",
        "alamat",
        "tgl_daftar",
        "no_telpon",
        "foto"
    ];
}
