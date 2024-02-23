<?php

namespace App\Http\Controllers;

use App\Mail\Haikala;
use App\Models\IflasEmailList;
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
        $data = IflasEmailList::all()->skip(33);
        foreach ($data as $item) {
            //return view('mails.iflas-mail', ['data' => $item['name']]);

            $mails = str_replace('\'','',$item['emails']);
            $mails = explode(';', $mails);

            if (!empty($mails[0])) {
                \Mail::to($mails)->cc('m.elbaz@jpaemirates.com')->queue(new Haikala($item['name']));
            }
        }
    }
}
