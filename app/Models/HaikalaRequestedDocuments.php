<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HaikalaRequestedDocuments extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'name_ar',
        'name_en',
        'requested_documents_ar',
        'requested_documents_en',
        'mails',
        'is_sent'
    ];
}
