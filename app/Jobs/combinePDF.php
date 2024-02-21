<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class combinePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $directory, public string $outputPath = '/output')
    {
        $outputDirectory = storage_path($this->outputPath); // Adjust the path as needed

        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $subdirectories = File::directories($this->directory);

        foreach ($subdirectories as $subdirectory) {
            $outputPath = $this->getOutputPath($subdirectory);

            $pdfFiles = $this->getAllPdfFiles($subdirectory);

            $pdfMerger = \PDFMerger::init();

            foreach ($pdfFiles as $pdfFile) {
                $pdfMerger->addPathToPDF($pdfFile);
            }

            $pdfMerger->merge();
            $pdfMerger->save($outputPath);

            // Clean up temporary files if needed
            // $pdfMerger->cleanUp();
        }
    }

    /**
     * Get the output path based on the subdirectory.
     *
     * @param string $subdirectory
     * @return string
     */
    private function getOutputPath(string $subdirectory): string
    {
        $subdirectoryName = basename($subdirectory);
        return $this->outputPath . '/' . $subdirectoryName . '.pdf';
    }

    /**
     * Get all PDF files from the directory and its subdirectories.
     *
     * @param string $directory
     * @return array
     */
    private function getAllPdfFiles(string $directory): array
    {
        $pdfFiles = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'pdf') {
                $pdfFiles[] = $file->getPathname();
            }
        }

        return $pdfFiles;
    }
}
