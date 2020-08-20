<?php

use AgileCore\Models\Mail;
use AgileCore\Utils\Dbg;

require '../src/boot.php';

foreach (Mail::getPendingMails() as $mail) {
    Dbg::logs("Processing queue for mail " . $mail->id  . " intended for " . $mail->target . "..");
    $mail->sendFromQueue();
}
