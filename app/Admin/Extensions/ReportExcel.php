<?php
namespace App\Admin\Extensions;

use App\Models\Batch;
use App\Models\Requests;
use App\Models\RequestStatus;
use Encore\Admin\Facades\Admin;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExcel implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $selected_columns=[];
    private $collection=[];

    public function __construct($collection, $selected_columns)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "999");
        $this->selected_columns = $selected_columns;
        $this->collection = $collection;
    }
    public function headings(): array
    {
        $excel_columns[0] = 'ID';
        if (in_array(Requests::Request_NO, $this->selected_columns)) {
            $excel_columns[] = 'Request_NO';
        }
        if (in_array(Requests::Request_Date, $this->selected_columns)) {
            $excel_columns[] = 'Request_Date';
        }
        if (in_array(Requests::SERVICE, $this->selected_columns)) {
            $excel_columns[] ='SERVICE';
        }
        $excel_columns[4] = 'Full Name';
        $excel_columns[5] = 'Phone Number';
        $excel_columns[6] = 'Document No.';
        if (in_array(Requests::Service_Location, $this->selected_columns)) {
            $excel_columns[] ='Service Location';
        }
        if (in_array(Requests::Status, $this->selected_columns)) {
            $excel_columns[] = 'Status';
        }
        if (in_array(Requests::Enrollment_No, $this->selected_columns)) {
            $excel_columns[] = 'Enrollment_No';
        }
        if (in_array(Requests::Embassy, $this->selected_columns)) {
            $excel_columns[] = 'Provider';
        }
        if (in_array(Requests::Batch_Ref_No, $this->selected_columns)) {
            $excel_columns[] = 'Batch_Ref_No';
        }
        if (in_array(Requests::service_charge, $this->selected_columns)) {
            $excel_columns[] = 'Service Charge';
        }
        if (in_array(Requests::embassy_charge, $this->selected_columns)) {
            $excel_columns[] = 'Provider Charge';
        }
        if (in_array(Requests::TAX_AMOUNT, $this->selected_columns)) {
            $excel_columns[] = 'TAX AMOUNT';
        }
        if (in_array(Requests::Total, $this->selected_columns)) {
            $excel_columns[] = 'Total';
        }
        if (in_array(Requests::USERNAME, $this->selected_columns)) {
            $excel_columns[] = 'USERNAME';
        }
        return $excel_columns;
    }
    public function collection()
    {
        $excel_collection=[];
        $requests = $this->collection;//Requests::findmany($this->collection)->lazy();
        foreach ($requests as $key => $req) {
            $excel_collection[$key][] = $req->snl;
            if (in_array(Requests::Request_NO, $this->selected_columns)) {
                $excel_collection[$key][] = $req->snl;
            }
            if (in_array(Requests::Request_Date, $this->selected_columns)) {
                $excel_collection[$key][] = $req->request_created_at;
            }
            if (in_array(Requests::SERVICE, $this->selected_columns)) {
                $excel_collection[$key][] = $req->service->title;
            }
            $excel_collection[$key][] = $req->customer->full_name;
            $excel_collection[$key][] = $req->customer->phone_number;
            $excel_collection[$key][] = $req->customer->passport_number;
            if (in_array(Requests::Service_Location, $this->selected_columns)) {
                $excel_collection[$key][] = $req->service_type->title;
            }
            if (in_array(Requests::Status, $this->selected_columns)) {
                $excel_collection[$key][] = RequestStatus::request_status[$req->request_status_id] ?? '';
            }
            if (in_array(Requests::Enrollment_No, $this->selected_columns)) {
                $excel_collection[$key][] = $req->embassy_serial_number;
            }
            if (in_array(Requests::Embassy, $this->selected_columns)) {
                $excel_collection[$key][] = $req->embassy->title;
            }
            if (in_array(Requests::Batch_Ref_No, $this->selected_columns)) {
                $excel_collection[$key][] = $req->batch ? $req->batch->title : '';
            }
            if (in_array(Requests::service_charge, $this->selected_columns)) {
                $excel_collection[$key][] = $req->service_charge;
            }
            if (in_array(Requests::embassy_charge, $this->selected_columns)) {
                $excel_collection[$key][] = $req->embassy_charge;
            }
            if (in_array(Requests::TAX_AMOUNT, $this->selected_columns)) {
                $excel_collection[$key][] = $req->tax_amount;
            }
            if (in_array(Requests::Total, $this->selected_columns)) {
                $excel_collection[$key][] = $req->amount;
            }
            if (in_array(Requests::USERNAME, $this->selected_columns)) {
                $excel_collection[$key][] = $req->username->username ?? 'Admin';
            }
        }
        $amount = $requests->sum('amount');
        $embassy_charge = $requests->sum('embassy_charge');
        $service_charge = $requests->sum('service_charge');
        $total_requests = $requests->count();
        $excel_collection[] = ["Requests Count:".$total_requests] ;
        $excel_collection[] = ["Service Charge:".$service_charge] ;
        $excel_collection[] = ["Provider Charge:".$embassy_charge] ;
        $excel_collection[] = ["Total:".$amount] ;
        return collect($excel_collection);
    }
}
