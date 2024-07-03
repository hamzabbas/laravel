<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Models\Invoices;
 

class PdfController extends Controller
{
     public function generatePdf($id)
     {
      set_time_limit(300);
      $invoice = Invoices::findOrFail($id);
        $data = ['invoice' => $invoice];
        $html = view('send_invoice', $data)->render();
        $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');
        
        return $pdf->output();
     }
     public function test($id){
      set_time_limit(300);
      $invoice = Invoices::findOrFail($id);
      $data = ['invoice' => $invoice];
      $html = view('send_invoice', $data)->render();
  
      $options = new Options();
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
  
      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'landscape');
      $dompdf->render();
      
      return $dompdf->stream('invoice.pdf', array("Attachment" => false));
     }
     public function testview($id){
            $invoice = Invoices::find($id);
            return view('send_invoice', ['invoice' => $invoice]);
     }
}
