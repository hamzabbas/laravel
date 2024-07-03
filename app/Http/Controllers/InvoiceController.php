<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

use Illuminate\Support\Facades\DB;
use stdClass;


class InvoiceController extends Controller
{
 /**
     * Handle file upload and process invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateMyCode(Request $request)
    {
        $date = $request->input('date'); // Get the date from the url parameter
        $formattedDate = $request->input('formattedDate'); // Get the formatted date from the url parameter

        $year = substr($date, 0, 4); // Extract the year
        $shortYear = substr($year, 3); // Get the last one digits of the year
        $monthDay = substr($date, 5); // Get the month and day
        $newCode = $shortYear . '-' . str_replace('-', '', $monthDay); // Format the date

        // Get all the job IDs created on this date

        $my_codes = DB::table('invoices')->where('booking_date', $formattedDate)->pluck('my_code');
        // dd($my_codes);

        // Extract the sequence numbers
        $sequenceNumbers = $my_codes->map(function ($my_code) {
            $parts = explode('-', $my_code);
            return end($parts);
        });

        // Find the maximum sequence number
        $maxSequenceNumber = $sequenceNumbers->max();

        // Generate the new sequence number
        $sequenceNumber = $maxSequenceNumber + 1;

        // Check if the sequence number is within the allowed range
        if ($sequenceNumber > 9) {
            return response()->json(['error' => 'Maximum number of Invoices reached for this day.'], 400);
        }

        // Generate the job ID
        $my_code = $newCode . '-' . $sequenceNumber;

        return response()->json(['my_code' => $my_code]);
    }

    // Retrieve all invoices
    public function getAllInvoices()
    {
        // Retrieve all invoices
        $invoices = Invoices::all();

        // Return the index view with the retrieved data
        return view('dashboard', compact('invoices'));
    }

//uplaod CSV FILE.........

// public function upload(Request $request)
// {
//         // Check if a file was uploaded
//     // dd(request()->all());

//     if (request()->hasFile('csv_file')) {
//         // Get the uploaded file
//         $file = request()->file('csv_file');

//         // Validate the file
//         $validatedData = request()->validate([
//             'csv_file' => 'required|mimes:csv,txt,xls',
//         ]);

//         // Get the file path
//         $filePath = $file->getRealPath();

//         // Load the spreadsheet
//         $spreadsheet = IOFactory::load($filePath);


//         // Convert the spreadsheet to an array
//         $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

//         // Filter out empty rows
//         $data = array_filter($data, function ($row) {
//             return array_filter($row);
//         });

//         // Store the data in a PHP object
//         $object = new stdClass();
//         $object->headers = $data[1]; // First row as heading
//         $object->subHeaders = $data[2]; // Second row as sub heading
//         $object->titles = $data[3]; // Third row as title
//         $object->rows = array_slice($data, 3); // Data starts from the fourth row
//         // dd($object);

//         // Pass the object to the view
//         return view('dashboard', compact('object'));
//     }

//     // If no file was uploaded, redirect back with an error message
//     return redirect()->back()->with('error', 'Please upload a CSV file.');
// }

public function upload(Request $request)
{
    // Handle the file upload
    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    $data = array_map('str_getcsv', file($path));
    $csvData = array_slice($data, 3); // Skip the header row
   // dd($csvData);
    // Parse the CSV data
   
    foreach ($csvData as $row) {
        $reimbursable_car_hst = $row[30] ?? null; 
       

    if (!is_numeric($reimbursable_car_hst) && $reimbursable_car_hst !== null) {
       
        continue; 
    }
        Invoices::Create([
            'my_code' => $row[0] ,
            'id' => $row[1],
            'booking_date' => $row[2],
            'booking_payer' => $row[3],
            'booking_for' => $row[4],
            'booking_prov' => $row[5],
            'booking_locality_division' => $row[6],
            'booking_code' => $row[7] ,
            'booking_shift' => $row[8],
            'booking_type' => $row[9],
            'booking_from' => $row[10],
            'booking_to' => $row[11],
            'billable_hrs' => $row[12],
            'billable_interp' => $row[13],
            'billable_trip' => $row[14],
            'billable_total' => $row[15],
            'case_english_speaker' => $row[16],
            'case_spanish_speaker' => $row[17],
            'case_uci_number' => $row[18],
            'case_notes' => $row[19],
            'case_ctry' => $row[20],
            'expenses_paid_cost' => $row[21],
            'expenses_paid_hst' => $row[22],
            'reimbursable_meals_cost' => $row[23],
            'reimbursable_meals_hst' => $row[24],
            'reimbursable_others_cost' => $row[25],
            'reimbursable_others_hst' => $row[26],
            'reimbursable_car_kms' => $row[27],
            'reimbursable_car_cents_per_km' => $row[28],
            'reimbursable_car_cost' => $row[29],
            'reimbursable_car_hst' => $reimbursable_car_hst,
            'reimbursable_total_cost' => $row[31],
            'reimbursable_total_hst' => $row[32],
            'hourly_fee_per_hour' => $row[33],
            'hourly_fee_cost' => $row[34],
            'hourly_fee_hst' => $row[35],
            'receivables_cost' => $row[36],
            'receivables_hst' => $row[37],
            'receivables_billed' => $row[38],
            'receivables_balance' => $row[39],
            'payer_amount' => $row[40],
            'payer_mag_invoice_number' => $row[41],
            'payer_payment_number' => $row[42],
            'payer_date' => $row[43],
            'age' => $row[44],
            'paid_to' => $row[45],
        ]);
       
    }
  

    return redirect()->route('dashboard')->with('success', 'CSV file uploaded and processed successfully.');
}








    // Storing the invoice
    public function createInvoice(Request $request)
    {

        $invoice_data = $request->all();
        // $invoice_data['booking_date'] = $invoice_data['formatted_date'];
        // dd($invoice_data);

        // Validate the request data
        $validatedData = $request->validate([
            'my_code' => 'required',
            'booking_date' => 'required',
            'booking_payer' => 'required',
            'booking_for' => '',
            'booking_prov' => '',
            'booking_locality_division' => '',
            'booking_code' => '',
            'booking_shift' => '',
            'booking_type' => '',
            'booking_from' => '',
            'booking_to' => '',
            'billable_hrs' => '',
            'billable_interp' => '',
            'billable_trip' => '',
            'billable_total' => '',
            'case_english_speaker' => '',
            'case_spanish_speaker' => '',
            'case_uci_number' => '',
            'case_notes' => '',
            'case_ctry' => '',
            'expenses_paid_cost' => '',
            'expenses_paid_hst' => '',
            'reimbursable_meals_cost' => '',
            'reimbursable_meals_hst' => '',
            'reimbursable_others_cost' => '',
            'reimbursable_others_hst' => '',
            'reimbursable_car_kms' => '',
            'reimbursable_car_cents_per_km' => '',
            'reimbursable_car_cost' => '',
            'reimbursable_car_hst' => '',
            'reimbursable_total_cost' => '',
            'reimbursable_total_hst' => '',
            'hourly_fee_per_hour' => '',
            'hourly_fee_cost' => '',
            'hourly_fee_hst' => '',
            'receivables_cost' => '',
            'receivables_hst' => '',
            'receivables_billed' => '',
            'receivables_balance' => '',
            'payer_amount' => '',
            'payer_mag_invoice_number' => '',
            'payer_payment_number' => '',
            'payer_date' => '',
            'age' => '',
            'paid_to' => '',
        ]);


        $validatedData['booking_date'] = $invoice_data['formatted_date'];

        // dd($validatedData);
        // dd("Did not Run.");

        // Create a new invoice with the validated data
        try {
            $invoice = Invoices::create($validatedData);

            // If the creation is successful, redirect to the main route
            return redirect()->route('index')->with('success', 'Invoice created successfully');
        } catch (\Exception $e) {
            // If there's an error, redirect back with the error message
            // Get the full error message
            $fullMessage = $e->getMessage();

            // Use a regular expression to extract the main point
            preg_match("/Column '(.*?)' cannot be null/", $fullMessage, $matches);

            // The main point is in $matches[0] if a match was found
            $mainPoint = $matches[0] ?? 'Unknown error';

            // Redirect back with the main point of the error message
            return redirect()->back()->with('error', 'Failed to create invoice: ' . $mainPoint)->withInput($invoice_data);
        }
    }

    // Editing the invoice
    public function editInvoice($id)
    {
        // Retrieve the invoice with the specified ID
        $invoice = Invoices::find($id);

        // Return the edit invoice view with the specified invoice
        return view('edit_invoice', compact('invoice'));
    }

    // Updating the invoice
    public function updateInvoice(Request $request)
    {
        $id = $request->input('id');
        $invoice_data = $request->all();

        // Validate the request data
        $validatedData = $request->validate([
            'my_code' => 'required',
            'booking_date' => 'required',
            'booking_payer' => 'required',
            'booking_for' => '',
            'booking_prov' => '',
            'booking_locality_division' => '',
            'booking_code' => '',
            'booking_shift' => '',
            'booking_type' => '',
            'booking_from' => '',
            'booking_to' => '',
            'billable_hrs' => '',
            'billable_interp' => '',
            'billable_trip' => '',
            'billable_total' => '',
            'case_english_speaker' => '',
            'case_spanish_speaker' => '',
            'case_uci_number' => '',
            'case_notes' => '',
            'case_ctry' => '',
            'expenses_paid_cost' => '',
            'expenses_paid_hst' => '',
            'reimbursable_meals_cost' => '',
            'reimbursable_meals_hst' => '',
            'reimbursable_others_cost' => '',
            'reimbursable_others_hst' => '',
            'reimbursable_car_kms' => '',
            'reimbursable_car_cents_per_km' => '',
            'reimbursable_car_cost' => '',
            'reimbursable_car_hst' => '',
            'reimbursable_total_cost' => '',
            'reimbursable_total_hst' => '',
            'hourly_fee_per_hour' => '',
            'hourly_fee_cost' => '',
            'hourly_fee_hst' => '',
            'receivables_cost' => '',
            'receivables_hst' => '',
            'receivables_billed' => '',
            'receivables_balance' => '',
            'payer_amount' => '',
            'payer_mag_invoice_number' => '',
            'payer_payment_number' => '',
            'payer_date' => '',
            'age' => '',
            'paid_to' => '',
        ]);

        $validatedData['booking_date'] = $invoice_data['formatted_date'];

        // Update the invoice with the validated data
        try {
            $invoice = Invoices::find($id);
            $invoice->update($validatedData);

            // If the update is successful, redirect to the main route
            return redirect()->route('index')->with('success', 'Invoice updated successfully');
        } catch (\Exception $e) {
            // If there's an error, redirect back with the error message
            // Get the full error message
            $fullMessage = $e->getMessage();

            // Use a regular expression to extract the main point
            preg_match("/Column '(.*?)' cannot be null/", $fullMessage, $matches);

            // The main point is in $matches[0] if a match was found
            $mainPoint = $matches[0] ?? 'Unknown error';

            // Redirect back with the main point of the error message
            return redirect()->back()->with('error', 'Failed to update invoice: ' . $mainPoint)->withInput($invoice_data);
        }
    }

    // Deleting the invoice
    public function deleteInvoice($id)
    {
        // Retrieve the invoice with the specified ID
        $invoice = Invoices::find($id);

        // Delete the invoice
        $invoice->delete();

        // Redirect to the main route
        return redirect()->route('index')->with('success', 'Invoice deleted successfully');
    }







    // public function createInvoice(Request $request)
    // {
    //     // Validate the request data
    //     // $validatedData = $request->validate([
    //     //     'my_code' => 'required',
    //     //     'customer_name' => 'required',
    //     //     'customer_email' => 'required|email',
    //     //     'amount' => 'required|numeric',
    //     //     'due_date' => 'required|date',
    //     // ]);

    //     // Create a new invoice with the validated data
    //     $invoice = Invoice::create($validatedData);

    //     // Return the invoice show view with the specified invoice
    //     return view('show_invoice', compact('invoice'));
    // }

    // public function index()
    // {
    //     // Retrieve all invoices
    //     $invoices = Invoice::all();

    //     // Return the invoices view with the retrieved data
    //     return view('invoices.index', compact('invoices'));
    // }

    // public function create()
    // {
    //     // Return the create invoice view
    //     return view('invoices.create');
    // }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // Define your validation rules here
        ]);

        // Create a new invoice with the validated data
        $invoice = Invoice::create($validatedData);

        // Redirect to the invoice show page
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function show(Invoice $invoice)
    {
        // Return the invoice show view with the specified invoice
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        // Return the edit invoice view with the specified invoice
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // Define your validation rules here
        ]);

        // Update the invoice with the validated data
        $invoice->update($validatedData);

        // Redirect to the invoice show page
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function destroy(Invoice $invoice)
    {
        // Delete the invoice
        $invoice->delete();

        // Redirect to the invoices index page
        return redirect()->route('invoices.index');
    }
}
