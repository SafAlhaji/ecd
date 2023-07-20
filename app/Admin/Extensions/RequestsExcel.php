<?php
namespace App\Admin\Extensions;

use App\Models\Requests;
use App\Models\RequestType;
use App\Models\RequestStatus;
use Encore\Admin\Facades\Admin;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RequestsExcel extends ExcelExporter implements WithMapping, ShouldAutoSize, WithHeadings
{
    protected $fileName = 'requests.xlsx';
    public function __construct()
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "999");
    }
    public function headings(): array
    {
        return [
        'id' => 'ID',
        'snl' => 'Request No.',
        'request_type_id' => 'Request Type',
        'request_created_at' => 'Request Date',
        'customer_id' => 'Full Name',
        'phone_number' => 'Phone Number',
        'passport_number' => 'Document No.',
        'service_id' => 'Service',
        'service_type_id' => 'Service Location',
        'service_charge'  => 'Service Charge',
        'embassy_charge' => 'Provider Charge',
        'tax_amount' => 'Tax Amount',
        'amount' => 'Total',
        'request_status_id' => 'Status',
        'embassy_serial_number' => 'Enrollment no.',
        'renew_note' => ' Renewing Note',
        'embassy_id' => 'Provider',
        'branch_id' => 'Branch',
        'payment_status_id' => 'Payment Status',
        'payment_type_id' => 'Payment Type',
        'payment_ref'    => 'Payment Ref.',
        'staff_id' => 'User Name',
    ];
    }
    // public function collection()
    // {
    // }
    public function map($row): array
    {
        return [
            $row->id,
            $row->snl,
            RequestType::Requests_Types[$row->request_type_id] ?? '',
            $row->request_created_at,
            $row->customer->full_name ?? '',
            $row->customer->phone_number ?? '',
            $row->customer->passport_number ?? '',
            $row->service->title ?? '',
            $row->service_type->title ?? '',
            $row->service_charge,
            $row->embassy_charge,
            $row->tax_amount,
            $row->amount,
            RequestStatus::request_status[$row->request_status_id] ?? '',
            $row->embassy_serial_number,
            $row->renew_note,
            $row->embassy->title ?? '',
            $row->branch->title ?? '',
            Requests::PAYMENT_STATUS[$row->payment_status_id] ?? '',
            Requests::PAYMENT_TYPE[$row->payment_type_id] ?? '',
            $row->payment_ref,
            $row->username->name ?? 'Admin',
    ];
    }
}
