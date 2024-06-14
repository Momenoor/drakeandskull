<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditorsFromReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'creditor_name_ar',
        'creditor_name_en',
        'email',
    ];
}
