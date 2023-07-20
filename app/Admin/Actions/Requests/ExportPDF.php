<?php

namespace App\Admin\Actions\Requests;

use App\Models\Batch;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\RequestStatus;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade as PDF;

class ExportPDF extends BatchAction
{
    public $name = 'Export PDF';

    public function handle(Collection $collection, Request $request)
    {
        // $data = $collection;
        // $view = view('pdf.request_table', compact('collection'));
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->loadHTML($view->render());
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        // $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        // $pdf->getDomPDF()->set_option('enable_php', true);
        // $pdf->getDomPDF()->set_option('enable_javascript', true);
        // $pdf->getDomPDF()->set_option('orientation', 'landscape');
        // $pdf->loadHTML($view->render())->save('uploads/request_table.pdf');
        $pdf = PDF::loadView('pdf.request_table', compact('collection'));
        $pdf->setPaper('a4', 'landscape');
        $pdf->save('uploads/request_table.pdf');
        return $this->response()->success('Success!')->download(url('uploads/request_table.pdf'));
    }
}
