<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'request_text',
        'request_type',
        'request_date',
        'request_by',
        'decision_number',
        'decision_date',
        'decision_text',
        'decision_by',
        'file_path'
    ];
}
