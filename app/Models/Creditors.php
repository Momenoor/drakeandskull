<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditors extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'creditor_name_ar',
        'creditor_name_en',
        'case_number',
        'execution_number',
        'execution_amount',
        'legal_representative',
        'claim_submission_date',
        'claim_amount',
        'email',
        'email2',
        'email3',
        'email4',
        'notes',
    ];
}
