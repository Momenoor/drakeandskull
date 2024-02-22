<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
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

        // Get the IMAP configuration from Laravel's configuration
        $config = config('imap');

        // Connect to the mailbox
        $cm = new ClientManager($config);
        $client = $cm->account();
        $client->connect();

        // Select the mailbox (folder) you want to retrieve emails from
        $folder = $client->getFolder('INBOX');

        // Get all unseen emails from the selected folder
        $emails = $folder->messages()->all()->limit(20)->get();
        // Loop through the emails and do something with them
        $data = [];
        foreach ($emails as $email) {
            // Access email properties

            $subject = $email->getSubject();
            $body = $email->getHTMLBody();
            $attachments = $email->getAttachments();
            $data[] = [$subject, $body, $attachments];

        }
        dd($data);
        // Disconnect from the mailbox even if an exception occurs
        if (isset($cm)) {
            $cm->disconnect();

        }
    }

    function send()
    {
        $receipt = 'momen.noor@gmail.com';


        try {
            \Mail::raw('test mail text', function ($mail) use ($receipt) {
                $mail->to($receipt);
            });
            echo 'mail sent';
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

    }
}
