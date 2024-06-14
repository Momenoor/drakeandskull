<?php

namespace App\Http\Controllers;

use App\Mail\Haikala;
use App\Mail\HaikalaRequestDocumentsMail;
use App\Models\HaikalaRequestedDocuments;
use App\Models\IflasEmailList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     * @throws MaskNotFoundException
     */
    public function index()
    {
        $found = false;
        $senderEmail = null;
        // Get the IMAP configuration from Laravel's configuration
        $config = config('imap');

        $cm = new ClientManager($config);
        $client = $cm->account();
        $clientElBaz = $cm->account('elbaz');


        $records = HaikalaRequestedDocuments::query()->where('requested_documents_ar', '!=', '')->where('is_sent', 0)->get();

        foreach ($records as $record) {
            $mails = trim($record->mails, ';');
            $mails = explode(';', $mails);
            $mails = Arr::wrap($mails);
            $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
            if(empty($mails[0])){
                continue;
            }
            foreach ($mails as &$toMail) {
                preg_match($pattern, $toMail, $matches);
                $toMail = $matches[0];
            }

            $name = [
                'ar' => $record->name_ar ?: $record->name_en,
                'en' => $record->name_en ?: $record->name_ar,
            ];
            // Select the mailbox (folder) you want to retrieve emails from
            foreach ($mails as $mail) {

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
                $reply = Mail::send(new HaikalaRequestDocumentsMail(
                    name: $name,
                    requestedDocuments: [
                        'ar' => explode(';', $record->requested_documents_ar),
                        'en' => explode(';', $record->requested_documents_en),
                    ],
                    toMails: $mails,
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


                // Mark the email as seen (your code to handle this)
            }
        }

        // Disconnect from the mailbox
        $client->disconnect();
    }

    function send()
    {
        $data = IflasEmailList::query()->whereNotNull('emails')->get()->skip(42);
        \Mail::to(['momen.noor@gmail.com'])->queue(new Haikala('Momen Noor'));
        foreach ($data as $item) {
            return view('mails.iflas-mail', ['data' => $item['name']]);

            $mails = str_replace(['\'', ' '], '', trim($item['emails']));
            $mails = explode(';', trim($mails));

            if (!empty($mails[0])) {
                \Mail::to($mails)->queue(new Haikala($item['name']));
            }
        }
    }

    function pdf()
    {
        $found = false;
        $records = HaikalaRequestedDocuments::query()->where('requested_documents_ar', '!=', '')->where('is_sent', 1)->get();
        $config = config('imap');

        $cm = new ClientManager($config);
        $client = $cm->account();

        foreach ($records as $record) {
            $mails = trim($record->mails, ';');
            $mails = explode(';', $mails);
            $mails = Arr::wrap($mails);
            $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
            foreach ($mails as &$toMail) {
                preg_match($pattern, $toMail, $matches);
                $toMail = $matches[0];
            }

            // Select the mailbox (folder) you want to retrieve emails from
            foreach ($mails as $mail) {

                $email = $client->getFolder('Sent')->query()->to($mail)->fetchOrderDesc()->limit(1)->get()->first();
                if ($email) {
                    $found = true;
                    $senderEmail = $mail;
                    break;
                }
            }

            if ($found) {

                $pdf = Pdf::loadView('mails.pdf-mail', [
                    'content' => $email->getHTMLBody(),
                ])->setOptions(['isHtml5ParserEnabled' => true])->setOptions(['isPhpEnabled' => true]);
                //$pdf->getDomPDF()->getCanvas()->getFontMetrics()->setFont('calibri.ttf');

                $pdf->save(public_path(  $senderEmail . '_email.pdf'));
                $found = false;

            }
        }
    }
}
