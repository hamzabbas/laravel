@extends('welcome')

@section('content')
    <div class="row mt-2">
        <div class="col">
            <h2>Edit Invoice</h2>
        </div>
        <!-- Alerts -->
        <div class="container">
            @if (session()->has('Add'))
                <div id="alert-message" class=" alert alert-success fade show" role="alert">
                    <span>{{ session()->get('Add') }}</span>
                    <span class="float-end" aria-hidden="true" role="button">&times;</span>
                </div>
                <script>
                    setTimeout(function() {
                        $('#alert-message').fadeOut('fast');
                    }, 3000); // 3 seconds
                </script>
            @endif
            @if (session('success'))
                <div id="alert-message" class="alert alert-success fade show" role="alert">
                    <span>{{ session('success') }}</span>
                    <span class="float-end" aria-hidden="true" role="button">&times;</span>
                </div>
                <script>
                    setTimeout(function() {
                        $('#alert-message').fadeOut('fast');
                    }, 3000); // 3 seconds
                </script>
            @endif
            @if (session('error'))
                <div id="alert-message" class="alert alert-danger fade show" role="alert">
                    <span>{{ session('error') }}</span>
                    <span class="float-end" aria-hidden="true" role="button">&times;</span>
                </div>
                <script>
                    setTimeout(function() {
                        $('#alert-message').fadeOut('fast');
                    }, 3000); // 3 seconds
                </script>
            @endif
        </div>

        <div class="col-lg-12 col-md-12">

            <div id="error-message" class="alert alert-danger justify-between" style="display: none;"></div>
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ route('edit_invoice') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{-- 1 --}}
                        <div class="row mt-2">
                            <div class="col">
                                <label for="my_code" class="control-label">My Code</label>
                                <input type="hidden" name="id" value="{{ $invoice->id }}">
                                <input type="text" class="form-control" placeholder="4-mmdd-#" id="my_code"
                                    name="my_code" value="{{ $invoice->my_code }}" readonly>
                            </div>
                        </div>

                        {{-- 2 --}}
                        <div class="">

                            <div class="row mt-4">
                                <h4 class="">BOOKING INFORMATION</h4>
                            </div>

                            {{-- 3 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="booking_date" class="control-label">Date</label>
                                    @php
                                        $date = DateTime::createFromFormat('M/d/D/Y', $invoice->booking_date);
                                        $new_date = $date->format('Y-m-d');
                                    @endphp
                                    <input class="form-control" name="booking_date" id="booking_date" type="date"
                                        value="{{ $new_date }}" required>
                                    <input type="hidden" value="{{ $invoice->booking_date }}" id="formatted_date"
                                        name="formatted_date">
                                </div>

                                <div class="col">
                                    <label for="booking_payer" class="control-label">Payer</label>
                                    {{-- <input class="form-control" name="booking_payer" id="booking_payer" type="text"
                                        value="{{$invoice->booking_date}}" > --}}
                                    <select class="form-select" name="booking_payer" id="booking_payer"
                                        aria-label="Default select example" required>
                                        <option disabled {{ $invoice->booking_payer == '' ? 'selected' : '' }}>Select Payer
                                        </option>
                                        <option value="CBSA" {{ $invoice->booking_payer == 'CBSA' ? 'selected' : '' }}>
                                            CBSA</option>
                                        <option value="MAG" {{ $invoice->booking_payer == 'MAG' ? 'selected' : '' }}>MAG
                                        </option>
                                        <option value="IRB" {{ $invoice->booking_payer == 'IRB' ? 'selected' : '' }}>IRB
                                        </option>
                                        <option value="IRCC" {{ $invoice->booking_payer == 'IRCC' ? 'selected' : '' }}>
                                            IRCC</option>
                                        <option value="CIC" {{ $invoice->booking_payer == 'CIC' ? 'selected' : '' }}>CIC
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- 4 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="booking_for" class="control-label">For</label>
                                    <input class="form-control" name="booking_for" id="booking_for" type="text"
                                        value="{{ $invoice->booking_for }}">
                                </div>

                                <div class="col">
                                    <label for="booking_prov" class="control-label">Prov</label>
                                    {{-- <input class="form-control" name="booking_prov" id="booking_prov" type="text"
                                        value="{{$invoice->booking_date}}" > --}}
                                    <select class="form-select" name="booking_prov" id="booking_prov"
                                        aria-label="Default select example" required>
                                        <option disabled {{ $invoice->booking_prov == '' ? 'selected' : '' }}>Select Prov
                                        </option>
                                        <option value="All" {{ $invoice->booking_prov == 'All' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="ON" {{ $invoice->booking_prov == 'ON' ? 'selected' : '' }}>ON
                                        </option>
                                        <option value="QC" {{ $invoice->booking_prov == 'QC' ? 'selected' : '' }}>QC
                                        </option>
                                    </select>
                                </div>
                                <div class="col">
                                <label for="booking_locality_division" class="control-label">Locality-Division <small class="text-sm/[10px]">(Hint:Toronto-Armoury/4810 )</small></label>
                                <input type="text" class="form-control" value="{{ $invoice->booking_locality_division }}" name="booking_locality_division" id="booking_locality_division" autocomplete="off" placeholder="Select or add Locality-Division" />
                                <div id="locality-options" class="dropdown-menu" style="display: none;">
                                    <!-- Options will be dynamically added here -->
                                </div>
                            </div>
<script>


document.addEventListener('DOMContentLoaded', (event) => {
    const input = document.getElementById('booking_locality_division');
    const dropdown = document.getElementById('locality-options');
    const bookingCodeInput = document.getElementById('booking_code');
    const bookingPayerInput = document.getElementById('booking_payer');
    let options = [];

    // Fetch existing locality options on page load
    fetch('/get-localities')
        .then(response => response.json())
        .then(data => {
            options = data.map(item => `${item.locality}/${item.code}`);
            populateDropdown();
        });

    input.addEventListener('focus', () => {
        dropdown.style.display = 'block';
    });

    input.addEventListener('blur', () => {
        setTimeout(() => { dropdown.style.display = 'none'; }, 100); // Delay to allow click event to register
    });

    input.addEventListener('input', () => {
        populateDropdown(input.value);
    });

    dropdown.addEventListener('click', (event) => {
        if (event.target.classList.contains('dropdown-item')) {
            const selectedOption = event.target.dataset.value;
            const lastSlashIndex = selectedOption.lastIndexOf('/');
            const locality = selectedOption.substring(0, lastSlashIndex);
            const code = selectedOption.substring(lastSlashIndex + 1);

            input.value = locality; // Set the locality value in the input field
            if (bookingPayerInput.value == 'MAG') {
                bookingCodeInput.value = code; // Set the code value in the code input field
            }
        }
    });

    function populateDropdown(filter = '') {
        dropdown.innerHTML = '';
        const filteredOptions = options.filter(option => option.toLowerCase().includes(filter.toLowerCase()));
        filteredOptions.forEach(option => {
            const [locality, code] = option.split('/');
            const item = document.createElement('div');
            item.classList.add('dropdown-item');
            item.innerText = locality; // Display full locality name
            item.dataset.value = option; // Store the full value as a data attribute
            dropdown.appendChild(item);
        });

        if (filter) {
            const newOptionItem = document.createElement('div');
            newOptionItem.classList.add('dropdown-item');
            newOptionItem.innerHTML = `<strong>Add "${filter}"</strong>`;
            newOptionItem.addEventListener('click', () => {
                const [locality, code] = filter.split('/');
                if (filter && !options.includes(filter)) {
                    // Retrieve CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // AJAX request to store the new option
                    fetch('/store-locality', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ locality: locality, code: code })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            options.push(`${data.data.locality}/${data.data.code}`);
                            input.value = locality;
                            populateDropdown(); // Refresh the dropdown

                            if (bookingPayerInput.value == 'MAG' && code) {
                                bookingCodeInput.value = code;
                            }

                            console.log('Locality saved successfully!');
                        } else {
                            console.error('Failed to save locality.');
                        }
                    }).catch(error => console.error('Error:', error));
                }
            });
            dropdown.appendChild(newOptionItem);
        }
    }

    input.addEventListener('change', () => {
        let booking_locality_division = input.value;
        let option = options.find(opt => opt.startsWith(booking_locality_division));
        if (option && bookingPayerInput.value == 'MAG') {
            let code = option.split('/')[1];
            bookingCodeInput.value = code;
        }
    });
});
</script>
                                <!-- <div class="col">
                                    <label for="booking_locality_division" class="control-label">Locality-Division</label>
                                    {{-- <input class="form-control" name="booking_locality_division"
                                        id="booking_locality_division" type="text" value="{{$invoice->booking_date}}" > --}}

                                    <select class="form-select" name="booking_locality_division"
                                        id="booking_locality_division" aria-label="Default select example">
                                        <option disabled {{ $invoice->booking_locality_division == '' ? 'selected' : '' }}>
                                            Select Locality-Division</option>
                                        <option value="All"
                                            {{ $invoice->booking_locality_division == 'All' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="Toronto-Armoury/4810"
                                            {{ $invoice->booking_locality_division == 'Toronto-Armoury/4810' ? 'selected' : '' }}>
                                            Toronto-Armoury</option>
                                        <option value="Toronto-Sheppard/4811"
                                            {{ $invoice->booking_locality_division == 'Toronto-Sheppard/4811' ? 'selected' : '' }}>
                                            Toronto-Sheppard</option>
                                        <option value="Halton-Milton/1211"
                                            {{ $invoice->booking_locality_division == 'Halton-Milton/1211' ? 'selected' : '' }}>
                                            Halton-Milton</option>
                                        <option value="Halton-Burlington/1213"
                                            {{ $invoice->booking_locality_division == 'Halton-Burlington/1213' ? 'selected' : '' }}>
                                            Halton-Burlington</option>
                                        <option value="Barrie/3821"
                                            {{ $invoice->booking_locality_division == 'Barrie/3821' ? 'selected' : '' }}>
                                            Barrie</option>
                                        <option value="Barrie-Orilia/3845"
                                            {{ $invoice->booking_locality_division == 'Barrie-Orilia/3845' ? 'selected' : '' }}>
                                            Barrie-Orilia</option>
                                    </select>
                                </div> -->

                            </div>

                            {{-- 5 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="booking_code" class="control-label">Code</label>
                                    <input class="form-control" name="booking_code" id="booking_code" type="text"
                                        value="{{ $invoice->booking_code }}" readonly>
                                </div>

                                <div class="col">
                                    <label for="booking_shift" class="control-label">Shift</label>
                                    {{-- <input class="form-control" name="booking_shift" id="booking_shift" type="text"
                                        value="{{$invoice->booking_date}}" > --}}
                                    <select class="form-select" name="booking_shift" id="booking_shift"
                                        aria-label="Default select example" required>
                                        <option disabled {{ $invoice->booking_shift == '' ? 'selected' : '' }}>Select Shift
                                        </option>
                                        <option value="AM" {{ $invoice->booking_shift == 'AM' ? 'selected' : '' }}>AM
                                            - Morning before 1:00 PM</option>
                                        <option value="PM" {{ $invoice->booking_shift == 'PM' ? 'selected' : '' }}>PM
                                            - At or after 12:00 noon</option>
                                        <option value="FD" {{ $invoice->booking_shift == 'FD' ? 'selected' : '' }}>FD
                                            - Full Day</option>
                                        <option value="D" {{ $invoice->booking_shift == 'D' ? 'selected' : '' }}>D -
                                            Day 6:00 AM to 6:00 PM</option>
                                        <option value="N" {{ $invoice->booking_shift == 'N' ? 'selected' : '' }}>N -
                                            Night 6:00 PM to 6:00 AM</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label for="booking_type" class="control-label">Type</label>
                                    {{-- <input class="form-control" name="booking_type" id="booking_type" type="text"
                                        value="{{$invoice->booking_date}}" > --}}
                                    <select class="form-select" name="booking_type" id="booking_type"
                                        aria-label="Default select example">
                                        <option disabled {{ $invoice->booking_type == '' ? 'selected' : '' }}>Select Type
                                        </option>
                                        <option value="P" {{ $invoice->booking_type == 'P' ? 'selected' : '' }}>P -
                                            In-person</option>
                                        <option value="V" {{ $invoice->booking_type == 'V' ? 'selected' : '' }}>V -
                                            Video Conference</option>
                                        <option value="T" {{ $invoice->booking_type == 'T' ? 'selected' : '' }}>T -
                                            Telephone</option>
                                    </select>
                                </div>

                            </div>

                            {{-- 6 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="booking_from" class="control-label">From</label>
                                    <input class="form-control" name="booking_from" id="booking_from" type="time"
                                        value="{{ $invoice->booking_from }}" required>
                                </div>

                                <div class="col">
                                    <label for="booking_to" class="control-label">To</label>
                                    <input class="form-control" name="booking_to" id="booking_to" type="time"
                                        value="{{ $invoice->booking_to }}" required>
                                </div>

                            </div>

                        </div>


                        {{-- 7 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">BILLABLE HOURS</h4>
                            </div>

                            {{-- 8 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="billable_hrs" class="control-label">Hrs</label>
                                    <input class="form-control" name="billable_hrs" id="billable_hrs" type="text"
                                        value="{{ $invoice->billable_hrs }}" readonly>

                                    <input type="hidden" id="billable_hrs_decimal" name="billable_hrs_decimal">
                                </div>

                                <div class="col">
                                    <label for="billable_interp" class="control-label">Interp</label>
                                    <input class="form-control" name="billable_interp" id="billable_interp"
                                        type="text" value="{{ $invoice->billable_interp }}" readonly>
                                </div>
                            </div>

                            {{-- 9 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="billable_trip" class="control-label">Trip</label>
                                    <input class="form-control" name="billable_trip" id="billable_trip" type="text"
                                        value="{{ $invoice->billable_trip }}">
                                </div>

                                <div class="col">
                                    <label for="billable_total" class="control-label">Total</label>
                                    <input class="form-control" name="billable_total" id="billable_total" type="text"
                                        value="{{ $invoice->billable_total }}" readonly>
                                </div>
                            </div>


                        </div>


                        {{-- 10 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">INFORMATION OF THE CASE INTERPRETED FOR</h4>
                            </div>

                            {{-- 11 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="case_english_speaker" class="control-label">English Speaker</label>
                                    <input class="form-control" name="case_english_speaker" id="case_english_speaker"
                                        type="text" value="{{ $invoice->case_english_speaker }}">
                                </div>

                                <div class="col">
                                    <label for="case_spanish_speaker" class="control-label">Spanish speaker</label>
                                    <input class="form-control" name="case_spanish_speaker" id="case_spanish_speaker"
                                        type="text" value="{{ $invoice->case_spanish_speaker }}">
                                </div>
                            </div>

                            {{-- 12 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="case_uci_number" class="control-label">Case/UCI No.</label>
                                    <input class="form-control" name="case_uci_number" id="case_uci_number"
                                        type="text" value="{{ $invoice->case_uci_number }}">
                                </div>

                                <div class="col">
                                    <label for="case_ctry" class="control-label">Ctry</label>
                                    <input class="form-control" name="case_ctry" id="case_ctry" type="text"
                                        value="{{ $invoice->case_ctry }}">
                                </div>
                            </div>

                            {{-- 13 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="case_notes" class="control-label">Notes</label>
                                    <textarea class="form-control" name="case_notes" id="case_notes" rows="5" type="text">{{ old('case_notes', $invoice->case_notes) }}</textarea>
                                </div>


                            </div>
                        </div>

                        {{-- 14 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class=""> EXPENSES PAID</h4>
                            </div>

                            {{-- 15 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="expenses_paid_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="expenses_paid_cost" id="expenses_paid_cost"
                                        type="text" value="{{ $invoice->expenses_paid_cost }}">
                                </div>

                                <div class="col">
                                    <label for="expenses_paid_hst" class="control-label">HST</label>
                                    <input class="form-control" name="expenses_paid_hst" id="expenses_paid_hst"
                                        type="text" value="{{ $invoice->expenses_paid_hst }}">
                                </div>
                            </div>

                        </div>

                        {{-- 16 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">REIMBURSABLE EXPENSES</h4>
                            </div>

                            {{-- 17 --}}
                            <div class="row mt-4">
                                <h5 class="">Meals</h5>
                            </div>

                            {{-- 18 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="reimbursable_meals_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="reimbursable_meals_cost"
                                        id="reimbursable_meals_cost" type="text"
                                        value="{{ $invoice->reimbursable_meals_cost }}">
                                </div>

                                <div class="col">
                                    <label for="reimbursable_meals_hst" class="control-label">HST</label>
                                    <input class="form-control" name="reimbursable_meals_hst" id="reimbursable_meals_hst"
                                        type="text" value="{{ $invoice->reimbursable_meals_hst }}">
                                </div>
                            </div>

                            {{-- 19 --}}
                            <div class="row mt-4">
                                <h5 class="">Others</h5>
                            </div>

                            {{-- 20 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="reimbursable_others_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="reimbursable_others_cost"
                                        id="reimbursable_others_cost" type="text"
                                        value="{{ $invoice->reimbursable_others_cost }}">
                                </div>

                                <div class="col">
                                    <label for="reimbursable_others_hst" class="control-label">HST</label>
                                    <input class="form-control" name="reimbursable_others_hst"
                                        id="reimbursable_others_hst" type="text"
                                        value="{{ $invoice->reimbursable_others_hst }}">
                                </div>
                            </div>

                            {{-- 21 --}}
                            <div class="row mt-4">
                                <h5 class="">Personal Cars</h5>
                            </div>

                            {{-- 22 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="reimbursable_car_kms" class="control-label">KM/s</label>
                                    <input class="form-control" name="reimbursable_car_kms" id="reimbursable_car_kms"
                                        type="text" value="{{ $invoice->reimbursable_car_kms }}">
                                </div>

                                <div class="col">
                                    <label for="reimbursable_car_cents_per_km" class="control-label">¢/km</label>
                                    <input class="form-control" name="reimbursable_car_cents_per_km"
                                        id="reimbursable_car_cents_per_km" type="text"
                                        value="{{ $invoice->reimbursable_car_cents_per_km }}">
                                </div>
                            </div>

                            {{-- 23 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="reimbursable_car_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="reimbursable_car_cost" id="reimbursable_car_cost"
                                        type="text" value="{{ $invoice->reimbursable_car_cost }}">
                                </div>

                                <div class="col">
                                    <label for="reimbursable_car_hst" class="control-label">HST</label>
                                    <input class="form-control" name="reimbursable_car_hst" id="reimbursable_car_hst"
                                        type="text" value="{{ $invoice->reimbursable_car_hst }}">
                                </div>
                            </div>

                            {{-- 24 --}}
                            <div class="row mt-4">
                                <h5 class="">Total</h5>
                            </div>

                            {{-- 25 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="reimbursable_total_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="reimbursable_total_cost"
                                        id="reimbursable_total_cost" type="text"
                                        value="{{ $invoice->reimbursable_total_cost }}" readonly>
                                </div>

                                <div class="col">
                                    <label for="reimbursable_total_hst" class="control-label">HST</label>
                                    <input class="form-control" name="reimbursable_total_hst" id="reimbursable_total_hst"
                                        type="text" value="{{ $invoice->reimbursable_total_hst }}" readonly>
                                </div>
                            </div>

                        </div>

                        {{-- 26 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">HOURLY FEE</h4>
                            </div>

                            {{-- 27 --}}
                            <div class="row mt-2">

                                <div class="col">
                                    <label for="hourly_fee_per_hour" class="control-label">$/Hr</label>
                                    <input class="form-control" name="hourly_fee_per_hour" id="hourly_fee_per_hour"
                                        type="text" value="{{ $invoice->hourly_fee_per_hour }}">
                                </div>

                                <div class="col">
                                    <label for="hourly_fee_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="hourly_fee_cost" id="hourly_fee_cost"
                                        type="text" value="{{ $invoice->hourly_fee_cost }}" readonly>
                                </div>

                                <div class="col">
                                    <label for="hourly_fee_hst" class="control-label">HST</label>
                                    <input class="form-control" name="hourly_fee_hst" id="hourly_fee_hst" type="text"
                                        value="{{ $invoice->hourly_fee_hst }}" readonly>
                                </div>
                            </div>

                        </div>

                        {{-- 28 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">RECEIVABLES</h4>
                            </div>

                            {{-- 29 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="receivables_cost" class="control-label">Cost</label>
                                    <input class="form-control" name="receivables_cost" id="receivables_cost"
                                        type="text" value="{{ $invoice->receivables_cost }}" readonly>
                                </div>

                                <div class="col">
                                    <label for="receivables_hst" class="control-label">HST</label>
                                    <input class="form-control" name="receivables_hst" id="receivables_hst"
                                        type="text" value="{{ $invoice->receivables_hst }}" readonly>
                                </div>
                            </div>

                            {{-- 30 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="receivables_billed" class="control-label">Billed</label>
                                    <input class="form-control" name="receivables_billed" id="receivables_billed"
                                        type="text" value="{{ $invoice->receivables_billed }}" readonly>
                                </div>

                                <div class="col">
                                    <label for="receivables_balance" class="control-label">Balance</label>
                                    <input class="form-control" name="receivables_balance" id="receivables_balance"
                                        type="text" value="{{ $invoice->receivables_balance }}" readonly>
                                </div>
                            </div>

                        </div>

                        {{-- 31 --}}
                        <div class="">
                            <div class="row mt-4">
                                <h4 class="">PAYER'S DATA</h4>
                            </div>

                            {{-- 32 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="payer_amount" class="control-label">Amount</label>
                                    <input class="form-control" name="payer_amount" id="payer_amount" type="text"
                                        value="{{ $invoice->payer_amount }}">
                                </div>

                                <div class="col">
                                    <label for="payer_mag_invoice_number" class="control-label">MAG Inv. No</label>
                                    <input class="form-control" name="payer_mag_invoice_number"
                                        id="payer_mag_invoice_number" type="text"
                                        value="{{ $invoice->payer_mag_invoice_number }}" readonly>
                                </div>
                            </div>

                            {{-- 33 --}}
                            <div class="row mt-2">
                                <div class="col">
                                    <label for="payer_payment_number" class="control-label">Paymnt No.</label>
                                    <input class="form-control" name="payer_payment_number" id="payer_payment_number"
                                        type="text" value="{{ $invoice->payer_payment_number }}">
                                </div>

                                <div class="col">
                                    <label for="payer_date" class="control-label">Date</label>
                                    <input class="form-control" name="payer_date" id="payer_date" type="date"
                                        value="{{ $invoice->payer_date }}">
                                </div>
                            </div>

                        </div>

                        {{-- 34 --}}
                        <div class="row mt-2">
                            <div class="col">
                                <label for="age" class="control-label">Age</label>
                                <input class="form-control" name="age" id="age" type="text"
                                    value="{{ $invoice->age }}">
                            </div>

                            <div class="col">
                                <label for="paid_to" class="control-label">Paid To</label>
                                {{-- <input class="form-control" name="paid_to" id="paid_to" type="text" value="{{$invoice->booking_date}}"
                                    > --}}
                                <select class="form-select" name="paid_to" id="paid_to"
                                    aria-label="Default select example">
                                    <option disabled {{ $invoice->paid_to == '' ? 'selected' : '' }}>Select Paid To
                                    </option>
                                    <option value="1" {{ $invoice->paid_to == '1' ? 'selected' : '' }}>VB</option>
                                    <option value="2" {{ $invoice->paid_to == '2' ? 'selected' : '' }}>ASETS Ltd
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="d-flex justify-content-center mt-5">
                            <button type="submit" class="btn btn-primary">Save Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        $(document).ready(function() {
            let min_billing_hours = 0;
            let max_billing_hours = 0;
            let min_billing_rate = 0;
            let min_booking_time = "";
            let max_booking_time = "";

            $('#alert-message span').click(function() {
                $('#alert-message').hide();
            });

            $('#booking_payer').change(function() {
                let booking_payer = $(this).val();
                let booking_shift = $('#booking_shift').val();
                let booking_prov = $('#booking_prov').val();
                let booking_from = $('#booking_from').val();
                let booking_to = $('#booking_to').val();
                let billable_hrs = $('#billable_hrs').val();

                $('#booking_for').val(booking_payer);
                if (booking_payer == 'MAG') {
                    let code = $('#booking_locality_division').val()?.split('/')[1];
                    $('#booking_code').val(code);

                    // Enable the MAG Invoice Number field
                    $('#payer_mag_invoice_number').removeAttr('readonly');

                } else {
                    $('#booking_code').val('');

                    // Disable the MAG Invoice Number field
                    $('#payer_mag_invoice_number').attr('readonly', 'readonly');
                    $('#payer_mag_invoice_number').val('');
                }

                // Setting Interp value
                $('#billable_interp').val(calculate_interp());

                // Calculate the billable total
                calculate_billable_total();

                // Set the hourly fee
                set_hourly_fee();
            });

            $('#booking_prov').change(function() {
                // Setting Interp value
                $('#billable_interp').val(calculate_interp());

                // Calculate the billable total
                calculate_billable_total();

                // Trigger the change event on the cost fields to calculate the HSTs
                $('#expenses_paid_cost').change();
                $('#reimbursable_meals_cost').change();
                $('#reimbursable_others_cost').change();

                // Set the hourly fee
                set_hourly_fee();

            });

            $('#booking_locality_division').change(function() {
                let booking_locality_division = $(this).val();
                if ($('#booking_payer').val() == 'MAG') {
                    let code = booking_locality_division.split('/')[1];
                    $('#booking_code').val(code);
                }
            });

            $('#booking_date').change(function() {
                let booking_date = $(this).val();
                let date = new Date(booking_date);

                let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                    "Nov", "Dec"
                ];
                let dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

                let day = String(date.getDate()).padStart(2, '0');
                let monthIndex = date.getMonth();
                let year = date.getFullYear();
                let dayIndex = date.getDay();

                let formattedDate = monthNames[monthIndex] + '/' + day + '/' + dayNames[dayIndex] + '/' +
                    year;
                $('#formatted_date').val(formattedDate);

                // Send a request to the server to generate the My Code
                $.get('/generate_my_code', {
                    date: booking_date,
                    formattedDate: formattedDate
                }, function(data) {
                    // Set the my_code in a field
                    $('#my_code').val(data.my_code);
                }).fail(function(xhr) {
                    $('#booking_date').val('');
                    $('#my_code').val('');
                    // Show the error message with a close button
                    $('#error-message')
                        .html(xhr.responseJSON.error +
                            '<span class="float-end" aria-hidden="true" role="button">&times;</span> '
                        )
                        .show();

                    // Hide the error message when the close button is clicked
                    $('#error-message span').click(function() {
                        $('#error-message').hide();
                    });
                });

                // Set the hourly fee
                set_hourly_fee();

                // Calculate Age
                calculate_age();
            });

            $('#booking_shift').change(function() {
                let booking_shift = $(this).val();
                let payer = $('#booking_payer').val();

                if (booking_shift === 'AM') {
                    min_billing_hours = 3;
                    max_billing_hours = 4;
                    min_billing_rate = 0;
                    min_booking_time = "";
                    max_booking_time = "13:00";
                } else if (booking_shift === 'PM') {
                    min_billing_hours = 3;
                    max_billing_hours = 0;
                    min_billing_rate = 0;
                    min_booking_time = "12:00";
                    max_booking_time = "";
                } else if (booking_shift === 'FD') {
                    min_billing_hours = 6;
                    max_billing_hours = 7;
                    min_billing_rate = 0;
                    min_booking_time = "";
                    max_booking_time = "";
                } else if (booking_shift === 'D') {
                    min_billing_hours = 3;
                    max_billing_hours = 0;
                    min_billing_rate = 50;
                    min_booking_time = "06:00";
                    max_booking_time = "18:00";
                } else if (booking_shift === 'N') {
                    min_billing_hours = 3;
                    max_billing_hours = 0;
                    min_billing_rate = 75;
                    min_booking_time = "18:00";
                    max_booking_time = "06:00";
                }

                // Setting Interp value
                $('#billable_interp').val(calculate_interp());
                // Calculate the billable total
                calculate_billable_total();
            });

            $('#booking_code').change(function() {
                // Set the hourly fee
                set_hourly_fee();
            })

            $('#booking_type').change(function() {
                // Set the hourly fee
                set_hourly_fee();
            })

            $('#booking_from').change(function() {
                let booking_to = $('#booking_to').val();
                let booking_from = $(this).val();
                let from, to;
                if (booking_from === '' || booking_to === '') {
                    $('#billable_hrs').val("");
                    $('#billable_hrs_decimal').val("");
                    return;
                } else if (booking_from > booking_to) {
                    to = new Date('2021-01-02 ' + booking_to);
                } else {
                    to = new Date('2021-01-01 ' + booking_to);
                }
                from = new Date('2021-01-01 ' + booking_from);
                let diff = (to - from) / 1000 / 60 / 60;

                // Convert the decimal hours to a time string
                let hours = Math.floor(diff);
                let minutes = Math.round((diff - hours) * 60);
                let timeString = ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2);

                // Store the difference in the billable hours field
                $('#billable_hrs').val(timeString);
                $('#billable_hrs_decimal').val(diff);

                // Setting Interp value
                $('#billable_interp').val(calculate_interp());

                // Calculate the billable total
                calculate_billable_total();

                // Set the hourly fee
                set_hourly_fee();

            });

            $('#booking_to').change(function() {
                let booking_from = $('#booking_from').val();
                let booking_to = $(this).val();
                let from, to;
                if (booking_from === '' || booking_to === '') {
                    $('#billable_hrs').val("");
                    $('#billable_hrs_decimal').val("");
                    return;
                } else if (booking_from > booking_to) {
                    to = new Date('2021-01-02 ' + booking_to);
                } else {
                    to = new Date('2021-01-01 ' + booking_to);
                }
                from = new Date('2021-01-01 ' + booking_from);
                let diff = (to - from) / 1000 / 60 / 60;

                // Convert the decimal hours to a time string
                let hours = Math.floor(diff);
                let minutes = Math.round((diff - hours) * 60);
                let timeString = ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2);

                // Store the difference in the billable hours field
                $('#billable_hrs').val(timeString);
                $('#billable_hrs_decimal').val(diff);

                // Setting Interp value
                $('#billable_interp').val(calculate_interp());

                // Calculate the billable total
                calculate_billable_total();

                // Set the hourly fee
                set_hourly_fee();

            });

            $('#billable_hrs').change(function() {
                // Setting Interp value
                $('#billable_interp').val(calculate_interp());
                // Calculate the billable total
                calculate_billable_total();
            });

            $('#billable_interp').change(function() {
                calculate_billable_total();
            });

            $('#billable_trip').change(function() {
                calculate_billable_total();
            });

            $('#billable_total').change(function() {
                let value = parseFloat($(this).val());
                let hourly_fee_per_hour = parseFloat($('#hourly_fee_per_hour').val());

                if (value > 0 && hourly_fee_per_hour > 0) {
                    let cost = value * hourly_fee_per_hour;
                    $('#hourly_fee_cost').val(cost.toFixed(2)).change();
                    let hst = calculate_HST(cost);
                    $('#hourly_fee_hst').val(hst).change();
                } else {
                    $('#hourly_fee_cost').val('0.00').change();
                    $('#hourly_fee_hst').val('0.00').change();
                }
            })

            $('#expenses_paid_cost').change(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    value = value.toFixed(2);
                    $(this).val(value);
                } else {
                    value = 0.00;
                }

                //calculate HST
                let hst = calculate_HST(value);
                $('#expenses_paid_hst').val(hst);
            });

            $('#reimbursable_meals_cost').change(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    value = value.toFixed(2);
                    $(this).val(value);
                } else {
                    value = 0.00;
                }

                //calculate HST
                let hst = calculate_HST(value);
                $('#reimbursable_meals_hst').val(hst);

                // Calculate the total cost
                calculate_reimbursable_total_cost();

                // Calculate the total HST
                calculate_reimbursable_total_hst();

            });

            $('#reimbursable_meals_hst').change(function() {
                calculate_reimbursable_total_hst();
            });

            $('#reimbursable_others_cost').change(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    value = value.toFixed(2);
                    $(this).val(value);
                } else {
                    value = 0.00;
                }

                //calculate HST
                let hst = calculate_HST(value);
                $('#reimbursable_others_hst').val(hst);

                // Calculate the total cost
                calculate_reimbursable_total_cost();

                // Calculate the total HST
                calculate_reimbursable_total_hst();


            });

            $('#reimbursable_others_hst').change(function() {
                calculate_reimbursable_total_hst();
            });

            $('#reimbursable_car_cost').change(function() {
                calculate_reimbursable_total_cost();
            });

            $('#reimbursable_car_hst').change(function() {
                calculate_reimbursable_total_hst();
            });

            $('#reimbursable_car_kms').keypress(function(e) {
                let keyCode = e.which;
                /*
                    8 - (backspace)
                    0 - (null)
                    48 - 57 - (0-9)Numbers
                */

                if ((keyCode != 8) && (keyCode < 48 || keyCode > 57)) {
                    return false;
                }
            });

            $('#reimbursable_car_kms').change(function() {
                let value = $(this).val();
                if (value > 0) {
                    $('#reimbursable_car_cents_per_km').val('0.40')
                } else {
                    $('#reimbursable_car_cents_per_km').val('0.00')
                }

                //calculate the cost
                let cents_per_km = parseFloat($('#reimbursable_car_cents_per_km').val());
                let cost = value * cents_per_km;
                $('#reimbursable_car_cost').val(cost.toFixed(2)).change();
            });

            $('#reimbursable_car_cents_per_km').change(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    value = value.toFixed(2);
                    $(this).val(value);
                } else {
                    value = 0.00;
                }

                //calculate the cost
                let kms = parseFloat($('#reimbursable_car_kms').val());
                if (kms > 0) {
                    let cost = kms * value;
                    $('#reimbursable_car_cost').val(cost.toFixed(2)).change();
                } else {
                    $('#reimbursable_car_cost').val('0.00').change();
                }
            });

            $('#reimbursable_total_cost').change(function() {
                calculate_receivables_cost();
            });

            $('#reimbursable_total_hst').change(function() {
                calculate_receivables_hst();
            });

            $('#hourly_fee_per_hour').change(function() {
                let value = parseFloat($(this).val());
                let billable_total = parseFloat($('#billable_total').val());

                if (value > 0 && billable_total > 0.0) {
                    let cost = value * billable_total;
                    $('#hourly_fee_cost').val(cost.toFixed(2)).change();
                    let hst = calculate_HST(cost);
                    $('#hourly_fee_hst').val(hst).change();
                } else {
                    $('#hourly_fee_cost').val('0.00').change();
                    $('#hourly_fee_hst').val('0.00').change();
                }
            });

            $('#hourly_fee_cost').change(function() {
                calculate_receivables_cost();
            });

            $('#hourly_fee_hst').change(function() {
                calculate_receivables_hst();
            });

            $('#receivables_cost').change(function() {
                calculate_receivables_billed();
            });

            $('#receivables_hst').change(function() {
                calculate_receivables_billed();
            });

            $('#receivables_billed').change(function() {
                calculate_receivables_balance();
            });

            $('#payer_amount').change(function() {
                calculate_receivables_balance();
            });

            $('#payer_date').change(function() {
                calculate_age();
            });

            // PUBLIC FUNCTIONS

            function compare_time(time1, time2, operator) {
                let t1, t2;
                t1 = new Date('2021-01-01 ' + time1);
                t2 = new Date('2021-01-01 ' + time2);
                // console.log(t1, operator, t2);
                switch (operator) {
                    case '<':
                        return t1 < t2;
                    case '<=':
                        return t1 <= t2;
                    case '>':
                        return t1 > t2;
                    case '>=':
                        return t1 >= t2;
                    case '==':
                        return t1 == t2;
                    case '!=':
                        return t1 != t2;
                    default:
                        return false;
                }
            }

            function round_up_to_next_quarter(time) {
                let parts = time.split(':');
                let hours = parseInt(parts[0]);
                let minutes = parseInt(parts[1]);

                if (minutes >= 1) {
                    // Round up to the next quarter hour
                    let remainder = minutes % 15;
                    if (remainder > 0) {
                        minutes += 15 - remainder;
                        if (minutes === 60) {
                            hours++;
                            minutes = 0;
                        }
                    }
                }

                // Convert the time to decimal format
                let decimalTime = hours + minutes / 60;
                return decimalTime.toFixed(2);
            }

            function round_up_to_next_whole_hour(time) {
                let parts = time.split(':');
                let hours = parseInt(parts[0]);
                let minutes = parseInt(parts[1]);

                if (minutes > 10) {
                    hours++;
                    minutes = 0;
                } else {
                    minutes = 0;
                }

                let decimalTime = hours + minutes / 60;
                return decimalTime.toFixed(2);
            }

            function calculate_interp() {
                let interp = 0.0;
                let payer = $('#booking_payer').val();
                let booking_shift = $('#booking_shift').val();
                let booking_prov = $('#booking_prov').val();
                let booking_from = $('#booking_from').val();
                let booking_to = $('#booking_to').val();
                let billable_hrs = $('#billable_hrs').val();
                let billable_hrs_decimal = $('#billable_hrs_decimal').val();
                interp = billable_hrs_decimal;
                if (payer === '' ||
                    booking_shift === '' ||
                    booking_prov === '' ||
                    booking_from === '' ||
                    booking_to === '' ||
                    billable_hrs === '') {
                    return 0.0;
                } else if (billable_hrs_decimal < 0.0) {
                    return 0.0;
                } else {
                    if (payer === "CBSA" && booking_prov === "ON") {
                        if (booking_shift === "FD" && compare_time(booking_to, "12:00", "<=")) {
                            if (compare_time(billable_hrs, "7:00", "<=")) {
                                interp = 7.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "7:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "AM" && compare_time(booking_to, "12:00", "<=")) {
                            if (compare_time(billable_hrs, "4:00", "<=")) {
                                interp = 4.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "4:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "PM") {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        }
                    } else if (payer === "CBSA" && booking_prov === "QC") {
                        if (booking_shift === "FD" && compare_time(booking_to, "18:00", "<=")) {
                            if (compare_time(billable_hrs, "6:00", "<=")) {
                                interp = 6.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "6:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "D" &&
                            compare_time(booking_from, "06:00", ">=") &&
                            compare_time(booking_from, "18:00", "<=") &&
                            compare_time(booking_to, "18:00", "<=") &&
                            compare_time(booking_to, "06:00", ">=")) {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "D" &&
                            (
                                compare_time(booking_from, "18:00", ">=") ||
                                compare_time(booking_from, "06:00", "<=")
                            ) && (
                                compare_time(booking_to, "06:00", "<=") ||
                                compare_time(booking_to, "18:00", ">=")
                            )) {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        }

                    } else if (payer === "MAG") {
                        if (booking_shift === "FD" && compare_time(booking_to, "13:00", "<=")) {
                            if (compare_time(billable_hrs, "6:00", "<=")) {
                                interp = 6.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "6:00", ">")) {
                                interp = round_up_to_next_whole_hour(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "AM" && compare_time(booking_to, "13:00", "<=")) {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_whole_hour(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "AM" && compare_time(booking_to, "13:00", ">")) {
                            if (compare_time(billable_hrs, "6:00", "<=")) {
                                interp = 6.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "6:00", ">")) {
                                interp = round_up_to_next_whole_hour(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "PM" && compare_time(booking_to, "12:00", ">=")) {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_whole_hour(billable_hrs);
                                return interp;
                            }
                        }
                    } else if (payer === "IRB" || payer === "IRCC" || payer === "CIC") {
                        if (booking_shift === "FD" && compare_time(booking_from, "12:00", "<=")) {
                            if (compare_time(billable_hrs, "7:00", "<=")) {
                                interp = 7.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "7:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "AM" && compare_time(booking_to, "12:00", "<=")) {
                            if (compare_time(billable_hrs, "4:00", "<=")) {
                                interp = 4.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "4:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        } else if (booking_shift === "PM" && compare_time(booking_from, "12:00", ">=")) {
                            if (compare_time(billable_hrs, "3:00", "<=")) {
                                interp = 3.00;
                                return interp;
                            } else if (compare_time(billable_hrs, "3:00", ">")) {
                                interp = round_up_to_next_quarter(billable_hrs);
                                return interp;
                            }
                        }
                    } else {
                        return billable_hrs_decimal;
                    }
                }
                return interp;
            }

            function calculate_billable_total() {
                let billable_interp = $('#billable_interp').val();
                let billable_trip = $('#billable_trip').val();
                if (billable_trip === '') {
                    billable_trip = 0.0;
                }
                if (billable_interp === '') {
                    billable_interp = 0.0;
                }
                let billable_total = parseFloat(billable_interp) + parseFloat(billable_trip);
                $('#billable_total').val(billable_total.toFixed(2)).change();
            }

            function calculate_HST(value) {
                let prov = $('#booking_prov').val();
                if (prov === "QC") {
                    let hst = value * 0.1497;
                    return hst.toFixed(2);
                } else if (prov === "ON") {
                    let hst = value * 0.13;
                    return hst.toFixed(2);
                } else {
                    let hst = value * 0.13;
                    return hst.toFixed(2);
                }
            }

            function calculate_reimbursable_total_cost() {
                let reimbersable_car_cost = parseFloat($('#reimbursable_car_cost').val());
                let reimbersable_meals_cost = parseFloat($('#reimbursable_meals_cost').val());
                let reimbersable_others_cost = parseFloat($('#reimbursable_others_cost').val());

                if (isNaN(reimbersable_car_cost) || reimbersable_car_cost === "") {
                    reimbersable_car_cost = 0.0;
                }
                if (isNaN(reimbersable_meals_cost) || reimbersable_meals_cost === "") {
                    reimbersable_meals_cost = 0.0;
                }
                if (isNaN(reimbersable_others_cost) || reimbersable_others_cost === "") {
                    reimbersable_others_cost = 0.0;
                }

                let total = reimbersable_car_cost + reimbersable_meals_cost + reimbersable_others_cost;
                $('#reimbursable_total_cost').val(total.toFixed(2)).change();

            }

            function calculate_reimbursable_total_hst() {
                let reimbersable_car_hst = parseFloat($('#reimbursable_car_hst').val());
                let reimbersable_meals_hst = parseFloat($('#reimbursable_meals_hst').val());
                let reimbersable_others_hst = parseFloat($('#reimbursable_others_hst').val());

                if (isNaN(reimbersable_car_hst) || reimbersable_car_hst === "") {
                    reimbersable_car_hst = 0.0;
                }
                if (isNaN(reimbersable_meals_hst) || reimbersable_meals_hst === "") {
                    reimbersable_meals_hst = 0.0;
                }
                if (isNaN(reimbersable_others_hst) || reimbersable_others_hst === "") {
                    reimbersable_others_hst = 0.0;
                }

                let total = reimbersable_car_hst + reimbersable_meals_hst + reimbersable_others_hst;
                $('#reimbursable_total_hst').val(total.toFixed(2)).change();
            }

            function set_hourly_fee() {
                let payer = $('#booking_payer').val();
                let booking_prov = $('#booking_prov').val();
                let booking_date = $('#formatted_date').val();
                let booking_day = booking_date.split('/')[2];
                let booking_from = $('#booking_from').val();
                let booking_to = $('#booking_to').val();
                let booking_code = $('#booking_code').val();
                let booking_type = $('#booking_type').val();
                let hourly_fee_per_hour = $('#hourly_fee_per_hour').val();

                if (payer === "CBSA" && booking_prov === "ON") {
                    if (booking_day !== "Sat" && booking_day !== "Sun") {
                        hourly_fee_per_hour = 50;
                    } else {
                        hourly_fee_per_hour = 75;
                    }
                } else if (payer === "CBSA" && booking_prov === "QC") {
                    if (booking_day !== "Sat" && booking_day !== "Sun") {
                        if (compare_time(booking_from, "06:00", ">=") && compare_time(booking_to, "18:00",
                                "<=")) {
                            hourly_fee_per_hour = 50;
                        } else {
                            hourly_fee_per_hour = 75;
                        }
                    } else {
                        hourly_fee_per_hour = 75;
                    }
                } else if (payer === "MAG" && booking_code !== "") {
                    if (booking_type === "P") {
                        hourly_fee_per_hour = 70;
                    } else {
                        hourly_fee_per_hour = 60;
                    }
                } else if (payer === "IRB" || payer === "IRCC" || payer === "CIC") {
                    if (booking_day !== "Sat" && booking_day !== "Sun") {
                        hourly_fee_per_hour = 50;
                    } else {
                        hourly_fee_per_hour = 75;
                    }
                }
                $('#hourly_fee_per_hour').val(hourly_fee_per_hour);
            }

            function calculate_receivables_cost() {
                let reimbursable_total_cost = parseFloat($('#reimbursable_total_cost').val());
                let hourly_fee_cost = parseFloat($('#hourly_fee_cost').val());
                let receivables_cost = reimbursable_total_cost + hourly_fee_cost;
                if (isNaN(receivables_cost)) {
                    receivables_cost = 0.0;
                }
                $('#receivables_cost').val(receivables_cost.toFixed(2)).change();
            }

            function calculate_receivables_hst() {
                let reimbursable_total_hst = parseFloat($('#reimbursable_total_hst').val());
                let hourly_fee_hst = parseFloat($('#hourly_fee_hst').val());
                let receivables_hst = reimbursable_total_hst + hourly_fee_hst;
                if (isNaN(receivables_hst)) {
                    receivables_hst = 0.0;
                }
                $('#receivables_hst').val(receivables_hst.toFixed(2)).change();
            }

            function calculate_receivables_billed() {
                let receivables_cost = parseFloat($('#receivables_cost').val());
                let receivables_hst = parseFloat($('#receivables_hst').val());
                let receivables_billed = receivables_cost + receivables_hst;
                if (isNaN(receivables_billed)) {
                    receivables_billed = 0.0;
                }
                $('#receivables_billed').val(receivables_billed.toFixed(2)).change();
            }

            function calculate_receivables_balance() {
                let receivables_billed = parseFloat($('#receivables_billed').val());
                let payer_amount = parseFloat($('#payer_amount').val());
                if (isNaN(payer_amount)) {
                    payer_amount = 0.0;
                }
                if (isNaN(receivables_billed) || receivables_billed <= 0.0) {
                    receivables_billed = 0.0;
                }
                let receivables_balance = payer_amount - receivables_billed;
                $('#receivables_balance').val(receivables_balance.toFixed(2));
            }

            function calculate_age() {
                let booking_date = $('#booking_date').val();
                let payer_date = $('#payer_date').val();
                if (booking_date === "") {
                    return;
                }
                if (payer_date !== "") {
                    let date = new Date(booking_date);
                    let today = new Date(payer_date);
                    let diff = today - date;
                    let age = diff / (1000 * 60 * 60 * 24);
                    age = Math.round(age);
                    $('#age').val(age);
                } else {
                    let date = new Date(booking_date);
                    let today = new Date();
                    let diff = today - date;
                    let age = diff / (1000 * 60 * 60 * 24);
                    age = Math.round(age);
                    $('#age').val(age);
                }
            }
        });
    </script>
@endsection
