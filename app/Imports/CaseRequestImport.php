<?php

namespace App\Imports;

use App\Models\CaseRequest;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\NoReturn;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


class CaseRequestImport implements ToCollection, WithCalculatedFormulas, WithStartRow
{
    /**
     * @param Collection $collection
     */
    #[NoReturn] public function collection(Collection $collection): void
    {
        $headers = $collection->first()->merge($collection->get(2));
        $firstRequestNumber = $collection->get(1)->get(0);
        $firstDecisionNumber = $collection->get(3)->get(0);
        $collection->map(function (Collection $item, $index) use ($collection) {
            if (empty($item->first())) {
                for ($i = $index - 1; $i >= 0; $i--) {
                    $newSubCollection = $collection->get($i);
                    if (!empty($newSubCollection->first())) {
                        $newSubCollection->put(3, $newSubCollection->get(3) . ' ' . $item->get(3));
                        break;
                    }
                }
            }
            return $item;
        });
        $filteredCollection = $collection->reject(function ($item) {
            return empty($item->first());
        })->values();

        $filteredCollection->map(function (Collection $item, $index) use ($filteredCollection) {
            if ($item->first() == 'رقم القرار' || is_string($item->first())) {

                $newSubCollection = $filteredCollection->get($index - 1);

                $newSubCollection->put(6, $filteredCollection->get($index + 1)->get(0));
                $newSubCollection->put(7, $filteredCollection->get($index + 1)->get(1));
                $newSubCollection->put(8, $filteredCollection->get($index + 1)->get(2));
                $newSubCollection->put(9, $filteredCollection->get($index + 1)->get(3));
                $newSubCollection->put(10, $filteredCollection->get($index + 1)->get(4));
                $newSubCollection->put(11, $filteredCollection->get($index + 1)->get(5));
                $newSubCollection->forget(11)->forget(5)->forget(10);
            }
            return $item->values();
        });



        // Output the result for debugging
        $finalCollection = $collection->reject(function ($item) {
            return $item->count() < 9;
        })->values();
        $finalCollection = $finalCollection->transform(function (Collection $item) {
            return [
                'request_number' => $item->get(0),
                'request_text' =>$item->get(3),
                'request_type' =>$item->get(1),
                'request_date' =>Date::excelToDateTimeObject($item->get(2)),
                'request_by' =>$item->get(4),
                'decision_number' =>$item->get(6),
                'decision_date' =>Date::excelToDateTimeObject($item->get(8)),
                'decision_text' =>$item->get(7),
                'decision_by' =>$item->get(9),
                'file_path' => '=HYPERLINK("..\OneDrive\JPA Emirates\Mohamed Al Baz\القضايا\قضايا جارية\3052-2021 إعادة هيكلة ديرك أند سكال\الطلبات من النظام\\'.$item->get(0).'.pdf", "PDF File")',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        CaseRequest::insert($finalCollection->toArray());
    }

    /**
     * @inheritDoc
     */
    public function startRow(): int
    {
        return 2;
    }
}
