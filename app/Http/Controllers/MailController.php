<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSend;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $to_email = 'r_test007@yopmail.com';
               
        $m_sub = 'Here are your login credentials';
        $m_msg = 'Welcome, Your account has been created';

         $mail_data = [
             'm_sub' => $m_sub,
             'm_msg' => $m_msg,
         ];

        $sendInvoiceMail = Mail::to($to_email);
        $sendInvoiceMail->send(new EmailSend($mail_data));
  
   
        return response()->json([
            'message' => 'Email has been sent.'
        ], Response::HTTP_OK);
    }
}
