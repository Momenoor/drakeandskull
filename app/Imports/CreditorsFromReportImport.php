<?php

namespace App\Imports;

use App\Models\CreditorsFromReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CreditorsFromReportImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Adjust this based on where your data starts
    }
    public function model(array $row): CreditorsFromReport
    {
        return new CreditorsFromReport([
            'code' => $row[0],
            'creditor_name_ar' => $row[1],
            'creditor_name_en' => $row[2],
        ]);
    }
}
