<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class forgotpassEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        $address = 'rohan.maliko99@gmail.com';
        $subject = 'Reset  Your Password';
        $name = 'admin';

        return $this->view('emails.forgot')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with([ 'test_message' => $this->data['OTP']]);
    }
}
