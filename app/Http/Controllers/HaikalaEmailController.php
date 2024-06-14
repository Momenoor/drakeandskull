<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEmailBatch;
use App\Mail\HaikalaIndormCreditors;
use App\Models\CreditorsFromReport;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\AuthFailedException;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\ImapBadRequestException;
use Webklex\PHPIMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;
use Webklex\PHPIMAP\Exceptions\ResponseException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

class HaikalaEmailController extends Controller
{

    function getAllEmailToExcel()
    {
        $config = config('imap');
        $cm = new ClientManager($config);
        $client = $cm->account();
        $inbox = $client->getFolder('INBOX');
        $totalEmails = $inbox->messages()->all()->get()->count();

        // Set the batch size
        $batchSize = 100; // Adjust as needed

        // Calculate the total number of batches
        $totalBatches = ceil($totalEmails / $batchSize);

        // Process emails in batches
        for ($page = 1; $page <= $totalBatches; $page++) {
            ProcessEmailBatch::dispatch($batchSize, $page)->onQueue('email_processing');
        }
        return "Email processing job dispatched!";
    }

    /**
     * @throws RuntimeException
     * @throws ImapServerErrorException
     * @throws MaskNotFoundException
     * @throws GetMessagesFailedException
     * @throws ConnectionFailedException
     * @throws AuthFailedException
     * @throws BindingResolutionException
     * @throws ResponseException
     * @throws FolderFetchingException
     * @throws ImapBadRequestException
     */
    function mailRequestId()
    {
        $found = false;
        $senderEmail = null;
        // Get the IMAP configuration from Laravel's configuration
        $config = config('imap');

        $cm = new ClientManager($config);
        $client = $cm->account();
        $clientElBaz = $cm->account('elbaz');


        $records = CreditorsFromReport::query()->where('is_sent', 0)->get();

        foreach ($records as $record) {

            $emails = trim($record->mails, ';');
            $emails = explode(';', $emails);
            $emails = Arr::wrap($emails);
            $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
            if (empty($emails[0])) {
                continue;
            }
            foreach ($emails as &$toMail) {
                preg_match($pattern, $toMail, $matches);
                $toMail = $matches[0] ?? null;
            }
            if (!empty($emails)) {

                $name = [
                    'ar' => $record->name_ar ?: $record->name_en,
                    'en' => $record->name_en ?: $record->name_ar,
                ];
                // Select the mailbox (folder) you want to retrieve emails from
                foreach ($emails as $mail) {

                    $email = $client->getFolder('INBOX')->query()->from($mail)->fetchOrderDesc()->limit(1)->get()->first();
                    if ($email) {
                        $found = true;
                        $senderEmail = $mail;
                        break;
                    }
                    $email = $clientElBaz->getFolder('INBOX')->query()->from($mail)->fetchOrderDesc()->limit(1)->get()->first();
                    if ($email) {
                        $found = true;
                        $senderEmail = $mail;
                        break;
                    }

                }

                // Check if there's an email from the specified sender
                if ($found) {

                    // Send reply
                    $reply = Mail::send(new HaikalaIndormCreditors(
                        name: $name,
                        toMails: $emails,
                        subject: 'Re: ' . $email->getSubject(),
                        oldContent: $email->getHTMLBody(),
                        oldHeaders: [
                            'from' => $senderEmail,
                            'to' => $email->getTo()->first()->full,
                            'date' => $email->getDate()->first()->format('l, F j, Y g:i A'),
                        ]
                    ));
                    $sentFolder = $client->getFolder('Sent');

                    $newSentItem = $reply->getSymfonySentMessage()->toString();

                    $sentFolder->appendMessage($newSentItem, ['\Seen'], now()->format("d-M-Y h:i:s O"));
                    $record->is_sent = true;
                    $record->save();
                } else {
                    $reply = Mail::send(new HaikalaIndormCreditors(
                        name: $name,
                        toMails: $emails,
                        subject: 'القضية رقم 3052/2021 إجراءات إعادة الهيكلة',
                        oldContent: null,
                        oldHeaders: []
                    ));
                    $sentFolder = $client->getFolder('Sent');

                    $newSentItem = $reply->getSymfonySentMessage()->toString();

                    $sentFolder->appendMessage($newSentItem, ['\Seen'], now()->format("d-M-Y h:i:s O"));
                    $record->is_sent = true;
                    $record->save();
                }
            }
        }
        return response()->make('All mails are sent', 200);
    }
}
