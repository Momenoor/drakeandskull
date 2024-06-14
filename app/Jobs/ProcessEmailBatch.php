<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Imap\Message\ImapMessage;
use Maatwebsite\Excel\Facades\Excel;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;

class ProcessEmailBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $batchSize;
    protected $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batchSize, $page)
    {
        $this->batchSize = $batchSize;
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $config = config('imap');
        $cm = new ClientManager($config);
        $client = $cm->account();
        $inbox = $client->getFolder('INBOX');

        // Calculate offset and limit for pagination
        $offset = ($this->page - 1) * $this->batchSize;
        $messages = $inbox->messages()->range($offset + 1, $offset + $this->batchSize)->get();

        // Prepare Excel data
        $excelData = [];
        foreach ($messages as $message) {
            $excelData[] = [
                'Subject' => $message->getSubject(),
                'From' => $message->getFrom(),
                'Date' => $message->getDate()->format('Y-m-d H:i:s'),
                'Body' => $message->getBodyText(),
            ];
        }

        // Generate Excel file
        $excelFilePath = storage_path('emails_batch_' . $this->page . '.xlsx'); // File name includes page number
        Excel::download(collect($excelData), $excelFilePath);
    }
}
