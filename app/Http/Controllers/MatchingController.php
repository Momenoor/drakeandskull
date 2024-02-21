<?php

namespace App\Http\Controllers;

use App\Exports\CreditorsExport;
use App\Jobs\GetMatching;
use App\Models\Creditors;
use App\Models\CreditorsFromReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use \Maatwebsite\Excel\Excel as BaseExcel;

class MatchingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        GetMatching::dispatch();
        return (new CreditorsExport)->download('updated_data.xlsx');


    }

}
