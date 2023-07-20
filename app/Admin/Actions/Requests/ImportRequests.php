<?php

namespace App\Admin\Actions\Requests;

use Illuminate\Http\Request;
use App\Imports\RequestsImport;
use Encore\Admin\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class ImportRequests extends Action
{
    protected $selector = '.import-requests';

    public function handle(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new RequestsImport, $request->file('file'));
        return $this->response()->success('Success message...')->refresh();
    }
    public function form()
    {
        $this->file('file', 'Please select file');//->rules('reqiured');
    }
    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-requests">import data</a>
HTML;
    }
}
