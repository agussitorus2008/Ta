<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewNilaiFinal extends Model
{
    use HasFactory;

    protected $table  = "view_rekapitulasi_nilai_to";

    protected $fillable = [
        'username',
        'average_to'
    ];
}
