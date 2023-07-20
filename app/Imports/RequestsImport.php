<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Requests;
use App\Models\TransactionsHistory;
use Carbon\Carbon;
use File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RequestsImport implements ToModel, WithValidation, WithHeadingRow, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        $customer = Customer::firstOrCreate([
            'passport_number' => $row['passport_no'],
            'full_name' => $row['full_name'],
            'phone_number' => $row['phone_number'],
        ]);
        $request_created_at = date('d-m-Y', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row['request_date']));
        $delivery_date_time = $row['delivery_date_time'] ? date('d-m-Y', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row['delivery_date_time'])) : '';
        $request_status_id = $row['request_status_id'];
        $request = Requests::firstOrCreate([
            'snl' => $row['request_no'],
        ], [
            'service_id' => $row['service_id'],
            'service_type_id' => $row['service_type_id'],
            'request_created_at' => Carbon::parse($request_created_at)->format('Y-m-d'),
            'branch_id' => $row['branch_id'],
            'embassy_id' => $row['service_provider_id'],
            'amount' => $row['amount'],
            'request_status_id' => $request_status_id,
            'embassy_serial_number' => $row['embassy_serial_number'],
            'customer_id' => $customer->id,
            'profession_id' => $row['profession_id'],
            'service_charge' => $row['service_charge'],
            'embassy_charge' => $row['embassy_charge'],
            'tax_amount' => $row['tax_amount'],
            'renew_note' => $row['renew_note'],
            'request_type_id' => $row['request_type_id'],
            'payment_type_id' => $row['payment_type_id'],
            'payment_ref' => $row['payment_ref'],
            'payment_status_id' => $row['payment_status_id'],
            'delivery_date_time' => $delivery_date_time,
            'notes' => $row['notes'],
        ]);
        $path = public_path('uploads'.DIRECTORY_SEPARATOR.'requests_qrCode'.DIRECTORY_SEPARATOR);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // if ($request->qr_string == null) {
        // $qr_string = 'request_num' . $request->id . '_' .  Str::random(5);
        // $qr_image = 'requests_qrCode/' . $qr_string . '.png';

        // $link = env('APP_URL').'trackRequest?qr=';
        // $qr_link = $link . $qr_string;
        // $qr_code_image = base64_encode(QrCode::encoding('UTF-8')->format('png')->size(400)->color(0, 0, 0)->backgroundColor(255, 255, 255)->errorCorrection('H')->generate($qr_link, public_path('uploads/' . $qr_image)));
        // $request->qr_image = $qr_image;
        // $request->qr_string = $qr_string;
        // $request->save();
        $req_transaction = $request->transactions_history()->create([
                'branch_id' => $request->branch_id,
                'customer_id' => $request->customer_id,
                'title' => $request->service->title,
                'payment_type_id' => $request->payment_type_id,
                'amount' => $request->amount,
            ]);
        $req_transaction->snl = $request->branch_id ? Branch::find($request->branch_id)->get_transaction_code().$req_transaction->id : 'TRA00'.$req_transaction->id;
        $req_transaction->save();
        if (Requests::PAYMENT_TYPE_CASH == $request->payment_type_id || Requests::PAYMENT_TYPE_BANK == $request->payment_type_id) {
            $request->payment_status_id = Requests::PAYMENT_STATUS_PAID;
            $request->save();
            $req_transaction->paid_at = $request->created_at;
        }
        if (Requests::PAYMENT_TYPE_BANK == $request->payment_type_id) {
            $req_transaction->payment_ref = $request->payment_ref;
        }
        if (Requests::PAYMENT_TYPE_LATER == $request->payment_type_id) {
            $request->payment_status_id = Requests::PAYMENT_STATUS_NOT_PAID;
            $request->save();
            $request->customer->debit += $request->amount;
            $request->customer->save();
        }
        $req_transaction->transaction_type = TransactionsHistory::MONEY_IN;
        $req_transaction->tax_amount = $request->tax_amount;
        // $req_transaction->create_qr_code();
        $req_transaction->save();
        // }
        return;
    }

    public function chunkSize(): int
    {
        return 10;
    }

    public function rules(): array
    {
        return [
            '0' => Rule::unique('requests', 'snl'),
            '1' => Rule::exists('service', 'id'),

             // Above is alias for as it always validates in batches
             '*.1' => Rule::exists('service', 'id'),
             // '3' => 'date_format:Y-m-d',
             // '*.3' => 'date_format:Y-m-d',
            '4' => Rule::exists('branches', 'id'),

             // Above is alias for as it always validates in batches
             '*.4' => Rule::exists('branches', 'id'),
            '5' => Rule::exists('embessies', 'id'),

             // Above is alias for as it always validates in batches
             '*.5' => Rule::exists('embessies', 'id'),
            // '8' => Rule::exists('customer', 'id'),

            //  // Above is alias for as it always validates in batches
            //  '*.8' => Rule::exists('customer', 'id'),
            '10' => Rule::exists('profession', 'id'),

             // Above is alias for as it always validates in batches
             '*.10' => Rule::exists('profession', 'id'),
             // '19' => 'date_format:Y-m-d',
             // '*.19' => 'date_format:Y-m-d',
        ];
    }
}
