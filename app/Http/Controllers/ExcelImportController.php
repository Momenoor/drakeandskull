<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportController extends Controller
{
    public function showForm(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        // Fetch available import classes in the App\Imports namespace
        $importClasses = collect(glob(app_path('Imports/*.php')))
            ->map(function ($file) {
                return pathinfo($file)['filename'];
            });

        return view('import-form', compact('importClasses'));
    }

    public function import(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'class' => 'required|string', // Add validation for the import class
        ]);

        $path = realpath($request->file('file')->getPathname());

        $importClass = 'App\Imports\\' . $request->get('class');

        if (!class_exists($importClass)) {
            return redirect()->back()->withErrors(['class' => 'Invalid import class.']);
        }

        Excel::import(new $importClass, $path, readerType: \Maatwebsite\Excel\Excel::XLSX);

        return redirect()->back()->with('success', 'Data imported successfully.');
    }
}
