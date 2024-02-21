<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\AuthFailedException;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\ImapBadRequestException;
use Webklex\PHPIMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;
use Webklex\PHPIMAP\Exceptions\ResponseException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     * @throws MaskNotFoundException
     */
    public function index()
    {
        $config = config('imap.accounts.default');

// Connect to the mailbox
        $cm = new ClientManager($config);

        $client = $cm->account();

// Select the mailbox (folder) you want to retrieve emails from

            $folder = $cm->getFolder('INBOX');


// Get all unseen emails from the selected folder
        $emails = $folder->messages()->all()->get();

// Loop through the emails and do something with them
        $data = [];
        foreach ($emails as $email) {
            // Access email properties
            $subject = $email->getSubject();
            $body = $email->getHTMLBody();
            $attachments = $email->getAttachments();
            $data[] = [$subject, $body, $attachments];

        }

// Disconnect from the mailbox
        $cm->disconnect();

        dd($data);
    }
}
