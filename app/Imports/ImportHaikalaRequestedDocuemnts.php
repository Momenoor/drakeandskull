<?php

namespace App\Imports;

use App\Models\HaikalaRequestedDocuments;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportHaikalaRequestedDocuemnts implements ToModel, WithHeadingRow
{
    const CERT = 'شهادة من واقع ملف التنفيذ';
    const TLC = 'الرخصة';
    const EID = 'الهوية';
    Const MATTER = 'تم قيد دعوى قضائية';

    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     * @return HaikalaRequestedDocuments
     */
    public function model(array $row): HaikalaRequestedDocuments
    {

        $requestedDocuments = ['ar' => [], 'en' => []];
        if (str_contains($row['almstndat'], self::CERT)) {
            $requestedDocuments['ar'][] = 'شهادة حديثة من واقع ملف التنفيذ لبيان مبلغ المطالبة والمبلغ المسدد والمبلغ المترصد';
            $requestedDocuments['en'][] = 'A recent certificate based on the execution file indicating the claimed amount, the paid amount, and the outstanding amount';
        }
        if (str_contains($row['almstndat'], self::TLC)) {
            $requestedDocuments['ar'][] = 'الرخصة التجارية';
            $requestedDocuments['en'][] = 'Trade License';
        }
        if (str_contains($row['almstndat'], self::EID,)) {
            $requestedDocuments['ar'][] = 'الهوية الإماراتية والاقامة';
            $requestedDocuments['en'][] = 'Emirates ID and Residence Visa';
        }
        if (str_contains($row['almstndat'], self::MATTER,)) {
            $requestedDocuments['ar'][] = 'بيان إذا تم قيد دعوى قضائية ضد الشركة من عدمه';
            $requestedDocuments['en'][] = 'Statement of whether a lawsuit has been filed against the company if any';
        }

        return new HaikalaRequestedDocuments(
            [
                'uid' => $row['m'],
                'name_ar' => $row['asm_aldayn_aarby'],
                'name_en' => $row['asm_aldayn'],
                'requested_documents_ar' => implode(';', $requestedDocuments['ar']),
                'requested_documents_en' => implode(';', $requestedDocuments['en']),
                'mails' => $row['alaymylat'],
                'is_sent' => false,
            ]
        );
    }
}
