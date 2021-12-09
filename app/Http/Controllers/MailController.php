<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestMail;
use Illuminate\Support\Facades\mail;


class MailController extends Controller
{
    function mail()
    {
        $details = ['title'=>'Hello Mailer',
                    'body'=>'This mail For testing'];
                    Mail::to('uzair.am10@gmail.com')->send(new TestMail($details));
                    return 'Mail Sent...';
    }
}