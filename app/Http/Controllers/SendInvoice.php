<?php

namespace App\Http\Controllers;
use App\Models\Invoices;
use Illuminate\Http\Request;

class SendInvoice extends Controller
{
    public function sendInvoice($id)
    {
        
        $invoice = Invoices::find($id);

        return view('send_invoice', ['invoice' => $invoice]);
    }
}
