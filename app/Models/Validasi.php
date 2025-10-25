<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validasi extends Model
{
    use HasFactory;

    protected $table = 'validasi';
    protected $primaryKey = 'id';
    public $timestamps = false;
}