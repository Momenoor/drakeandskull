<?php

namespace App\Http\Controllers;

use App\Exports\CaseRequestExport;
use Illuminate\Http\Request;

class CaseRequestController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return (new CaseRequestExport)->download('case_request.xlsx');


    }
}
