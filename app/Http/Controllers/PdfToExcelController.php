<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\PdfToExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PdfToExcelController extends Controller
{
    public function convert()
    {
        return Excel::download(new PdfToExcelExport, 'converted_excel_file.xlsx');
    }
}
