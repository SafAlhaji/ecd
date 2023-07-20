<?php
namespace App\Admin\Extensions;

use App\Models\Batch;
use App\Models\Requests;
use App\Models\RequestStatus;
use Encore\Admin\Facades\Admin;
use Maatwebsite\Excel\Concerns\WithMapping;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BatchExcel implements FromCollection, ShouldAutoSize
{
    private $selected_columns=[];
    private $collection_batch=[];

    public function __construct($collection_batch, $selected_columns)
    {
        $this->selected_columns = $selected_columns;
        $this->collection_batch = $collection_batch;
    }
    public function headings(): array
    {
        return $this->selected_columns;
    }
    public function collection()
    {
        $excel_collection=[];
        foreach ($this->collection_batch->requests as $key => $req) {
            if (in_array(Batch::Request_NO, $this->selected_columns)) {
                $excel_collection[$key][] = $req->snl;
            }
            if (in_array(Batch::FULL_NAME, $this->selected_columns)) {
                $excel_collection[$key][] = $req->customer->full_name;
            }
            if (in_array(Batch::PASSPORT_NO, $this->selected_columns)) {
                $excel_collection[$key][] = $req->customer->passport_number;
            }
            if (in_array(Batch::SERVICE, $this->selected_columns)) {
                $excel_collection[$key][] = $req->service->title;
            }
            if (in_array(Batch::PHONE_NO, $this->selected_columns)) {
                $excel_collection[$key][] = $req->customer->phone_number;
            }
            if (in_array(Batch::Enrollment_No, $this->selected_columns)) {
                $excel_collection[$key][] = $req->embassy_serial_number;
            }
            if (in_array(Batch::RENEW_NOTE, $this->selected_columns)) {
                $excel_collection[$key][] = $req->renew_note;
            }
            if (in_array(Batch::Status, $this->selected_columns)) {
                $excel_collection[$key][] = RequestStatus::request_status[$req->request_status_id];
            }
        }
        return collect($excel_collection);
    }
}
