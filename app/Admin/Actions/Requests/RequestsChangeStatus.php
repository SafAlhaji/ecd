<?php

namespace App\Admin\Actions\Requests;

use App\Models\RequestStatus;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RequestsChangeStatus extends BatchAction
{
    public $name = 'Change Status';

    public function handle(Collection $collection, Request $request)
    {
        $status_id = $request->get('request_status_id');
        foreach ($collection as $model) {
            $model->request_status_id = intval($status_id);
            $model->save();
        }

        return $this->response()->success('Success message...')->refresh();
    }
    public function form()
    {
        $this->select('request_status_id', 'Status')->options(RequestStatus::request_status)->rules('required');
    }
}
