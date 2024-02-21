<?php

namespace App\Jobs;

use App\Exports\CreditorsExport;
use App\Imports\CreditorsImport;
use App\Models\Creditors;
use App\Models\CreditorsFromReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel as BaseExcel;
use Maatwebsite\Excel\Facades\Excel;

class GetMatching implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function handle(): void
    {
        \DB::table('creditors')->truncate();
        Excel::import(new CreditorsImport, storage_path('app/creditors.xlsx'), readerType: \Maatwebsite\Excel\Excel::XLSX);

        $creditors = Creditors::query()->select('creditor_name_en', 'creditor_name_ar', 'code')->get();
        $creditorsFromReport = CreditorsFromReport::query()->select('creditor_name_en', 'creditor_name_ar', 'code')->get();

        foreach ($creditors as &$creditor) {
            if (empty($creditor['creditor_name_ar'])) {
                $creditor['creditor_name_ar'] = $this->fillMissingName(
                    $creditor['creditor_name_en'],
                    $creditorsFromReport,
                    'creditor_name_en',
                    'creditor_name_ar'
                // Specify the column to match against in CreditorsFromReport
                );
                Creditors::query()->where('code', $creditor['code'])->update([
                    'creditor_name_ar' => $creditor['creditor_name_ar'],
                ]);
            }

            if (empty($creditor['creditor_name_en'])) {
                $creditor['creditor_name_en'] = $this->fillMissingName(
                    $creditor['creditor_name_ar'],
                    $creditorsFromReport,
                    'creditor_name_ar',
                    'creditor_name_en'
                // Specify the column to match against in CreditorsFromReport
                );
                Creditors::query()->where('code', $creditor['code'])->update([
                    'creditor_name_en' => $creditor['creditor_name_en'],
                ]);
            }

            // Update the database with the corrected names

        }


    }

    private function fillMissingName($name, $creditorsFromReport, $columnNameToMatch, $columnNameToBeReturned): string|null
    {
        $closestMatch = null;
        $missingData = null;
        $minDistance = 5;


        foreach ($creditorsFromReport as $creditorFromReport) {
            $distance = levenshtein(strtolower($name), strtolower($creditorFromReport[$columnNameToMatch]));

            $maxLength = max(strlen($name), strlen($creditorFromReport[$columnNameToMatch]));
            if ($maxLength > 0) {
                $similarity = 1 - ($distance / $maxLength);

                if ($similarity >= 0.70) {
                    $closestMatch = $creditorFromReport[$columnNameToMatch];
                    $missingData = $creditorFromReport[$columnNameToBeReturned];
                }
            }
        }

        return $missingData;
    }
}
