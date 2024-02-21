<?php

namespace App\Http\Controllers;

use App\Jobs\combinePDF;
use Illuminate\Http\Request;

class combinePDFController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $directory = 'C:\Users\momen\OneDrive\JPA Emirates\Mohamed Al Baz\القضايا\قضايا جارية\3052-2021 إعادة هيكلة ديرك أند سكال\المطالبات';
        combinePDF::dispatch($directory)->onQueue('pdf-merger');
    }
}
