<?php

namespace App\Imports;

use App\Models\IflasEmailList;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class IflasEmailListImport implements ToModel, WithStartRow
{

    public function startRow(): int
    {
        return 2; // Adjust this based on where your data starts
    }

    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        if (!empty($row[1])) {
            $record['name'] = $row[1];
            $record['emails'] = collect($row)->skip(3)->reject(function ($value) {
                return is_null($value);
            })->implode(';');
            return new IflasEmailList($record);
        }

    }
}
