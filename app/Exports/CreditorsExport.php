<?php

namespace App\Exports;

use App\Models\Creditors;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class CreditorsExport implements FromQuery
{
    use Exportable;
    /**
    * @return Illuminate\Database\Eloquent\Builder
    */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return Creditors::query();
    }
}
