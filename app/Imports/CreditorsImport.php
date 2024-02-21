<?php

namespace App\Imports;

use App\Models\Creditors;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CreditorsImport implements ToModel, withStartRow, withCalculatedFormulas
{
    public function startRow(): int
    {
        return 2; // Adjust this based on where your data starts
    }

    public function model(array $row): Creditors
    {
        return new Creditors([
            'code' => $row[0],
            'creditor_name_ar' => $row[1],
            'creditor_name_en' => $row[2],
            'case_number' => $row[3],
            'execution_number' => $row[4],
            'execution_amount' => $row[5],
            'legal_representative' => $row[6],
            'claim_submission_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[7]),
            'claim_amount' => $row[8],
            'email' => $row[9],
            'email2' => $row[10],
            'email3' => $row[11],
            'email4' => $row[13],
            'notes' => $row[12],
        ]);
    }
}
