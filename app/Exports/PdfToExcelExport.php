<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\PdfToText\Exceptions\PdfNotFound;

class PdfToExcelExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     * @throws PdfNotFound
     */
    public function collection(): \Illuminate\Support\Collection
    {
        // Replace this with your logic to extract text from the PDF
        $textFromPdf = $this->getTextFromPdf('eissa.pdf');

        // Convert the text to an array or collection as needed
        $data = $this->processText($textFromPdf);

        return collect($data);
    }

    /**
     * @throws PdfNotFound
     */
    private function getTextFromPdf($pdfPath): string
    {
        $pdfPath = "attachments/{$pdfPath}";
        //$pdftotextPath = 'C:\path\to\pdftotext'; // Replace with the actual path

        return (new \Spatie\PdfToText\Pdf('pdftotext'))
            ->setPdf($pdfPath)
            //->setBinary($pdftotextPath)
            ->text();
    }

    private function processText($text): array
    {
        // Implement your logic to process the text and convert it to an array or collection
        // Example: explode the text into lines and columns
        return explode("\n", $text);
    }
}
