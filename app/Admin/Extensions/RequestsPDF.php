<?php
namespace App\Admin\Extensions;

use App\Models\Requests;
use App\Models\RequestStatus;
use Encore\Admin\Facades\Admin;
use Maatwebsite\Excel\Concerns\WithMapping;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Barryvdh\DomPDF\Facade as PDF;

class RequestsPDF extends AbstractExporter
{
    public function export()
    {
        // $data = $this->getData();
        $view = view('pdf.test_request_receipt');
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view->render());
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->getDomPDF()->set_option('enable_javascript', true);
        $pdf->getDomPDF()->set_option('orientation', 'landscape');
        $pdf->loadHTML($view->render())->save('uploads/Request_Receipt/request_receipt_0.pdf');
        // $pdf = PDF::loadView('pdf.test_request_receipt');
        $pdf = PDF::loadView('pdf.invoice', $data);
        return response()->download('uploads/Request_Receipt/request_receipt_0.pdf');
    }
}
