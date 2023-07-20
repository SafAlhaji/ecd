<?php
namespace App\Admin\Extensions;

use App\Models\Requests;
use App\Models\RequestType;
use App\Models\RequestStatus;
use Encore\Admin\Facades\Admin;
use App\Models\TransactionsHistory;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsExcel implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $collection=[];

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    protected $fileName = 'tax_report.xlsx';
    public function headings(): array
    {
        return [
        'id' => 'ID',
        'snl' => 'Transaction No.',
        'date' => 'Date',
        'amount' => 'Amount',
        'tax_amount' => 'Tax Amount',
    ];
    }
    public function collection()
    {
        $excel_collection=[];
        $trans_data = $this->collection;
        foreach ($trans_data as $key => $trans) {
            $excel_collection[$key][] = $trans->id;
            if ($trans->request) {
                $excel_collection[$key][] = $trans->request->snl;
                $excel_collection[$key][] = $trans->request->request_created_at;
                $excel_collection[$key][] = $trans->request->amount;
            } else {
                $excel_collection[$key][] = $trans->snl;
                $excel_collection[$key][] = date_format(date_create($trans->created_at), 'Y-m-d');
                $excel_collection[$key][] = $trans->amount - $trans->tax_amount;
            }
            $excel_collection[$key][] = $trans->tax_amount;
        }
        $total_requests_get = $trans_data->where('transaction_type', \App\Models\TransactionsHistory::MONEY_IN);
        $total_expenses = $trans_data->where('transaction_type', \App\Models\TransactionsHistory::MONEY_OUT);
        $total_requests_amount = $total_requests_get->sum('amount');
        $total_requests_tax = $total_requests_get->sum('tax_amount');
        $total_expensess_amount = $total_expenses->sum('amount');
        $total_expensess_tax = $total_expenses->sum('tax_amount');
        $net_tax = $total_requests_tax - $total_expensess_tax;
        $excel_collection[] = ["Total Requests Amount:".$total_requests_amount] ;
        $excel_collection[] = ["Total Requests Tax:".$total_requests_tax] ;
        $excel_collection[] = ["Total Expenses Amount:".$total_expensess_amount] ;
        $excel_collection[] = ["Total Expenses Tax:".$total_expensess_tax] ;
        $excel_collection[] = ["Net Tax:".$net_tax] ;
        return collect($excel_collection);
    }
}
