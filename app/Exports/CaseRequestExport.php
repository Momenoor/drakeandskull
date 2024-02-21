<?php

namespace App\Exports;

use App\Models\CaseRequest;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CaseRequestExport implements FromQuery, WithHeadingRow, WithCalculatedFormulas, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return CaseRequest::query();
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            '#',
            'رقم الطلب',
            'نص الطلب',
            'نوع الطلب',
            'تاريخ الطلب',
            'مقدم الطلب',
            'رقم القرار',
            'تاريخ القرار',
            'نص القرار',
            'القاضي',
            'مسار الملف',
            'تاريخ الانشاء',
            'تاريخ التحديث',
        ];
    }
}
