<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\sendMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PdfController;
use App\Models\Invoices;
use App\Jobs\SendReminderEmailJob;

class MailController extends Controller
{
    public function sendemail($id){
       
        $invoice = Invoices::findOrFail($id);

        $pdfController = new PdfController();
        $pdfData = $pdfController->generatePdf($id);

        $toEmailAddress = 'meethamzaabbas945@gmail.com';
        $welcome = "Hey this is testing mail";

        Mail::to($toEmailAddress)->send(new sendMail($welcome, $pdfData, $invoice));
        $invoice->last_email_sent_at = now();
        $invoice->save();
        SendReminderEmailJob::dispatch($invoice->id)->delay(now()->addMinutes(10));

        return 'Email sent successfully';
    }
}
