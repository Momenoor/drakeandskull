<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IflasEmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emails',
    ];
}
