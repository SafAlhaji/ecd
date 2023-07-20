<?php

namespace App\Admin\Actions\Requests;

use App\Models\Batch;
use App\Models\RequestStatus;
use App\Models\Service;
use App\Models\ThirdParty;
use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchRequests extends BatchAction
{
    public $name = 'batch Requests';

    public function handle(Collection $collection, Request $request)
    {
        $services = [];
        $service_ids = $collection->groupBy('service_id');
        foreach ($service_ids as $key => $value) {
            $service_title[] = explode('_', Service::find($key)->title);
        }
        $titles = [];
        foreach ($service_title as $title) {
            if (!in_array($title[0], $titles)) {
                $titles[] = $title[0];
            }
        }
        if (count($titles) > 1) {
            return $this->response()->warning('You Select Multiple Requests with Multiple Service. Select 1 service ')->timeout(6000);
        }
        $old_service_batch = Batch::query()->where('title', 'like', '%'.$titles[0].'%')->get()->last();
        if ($old_service_batch) {
            $search_batch = explode('_', $old_service_batch->title);
            $batch_service_number = intval($search_batch[1]) + 1;
        } else {
            $batch_service_number = 1;
        }
        $batch_title = 'Ref_00'.$batch_service_number.'_'.$titles[0];
        $batch_array = $collection->pluck('batch_id')->all();
        if (count(array_keys($batch_array, null)) == count($batch_array)) {
            $batch = Batch::create([
                    'title' => $batch_title,
                    'bank_ref' => $request->get('bank_ref'),
                    'batch_date' => $request->get('batch_date'),
                    'branch_id' => ThirdParty::find(Admin::user()->id)->branches()->first(['branches.id'])->id ?? null,
                    'admin_user_id' => Admin::user()->id,
                ]);
            foreach ($collection as $model) {
                if (null == $model->batch_id) {
                    $model->batch_id = $batch->id;
                    $model->request_status_id = RequestStatus::Preparing_to_Send_Embassy;
                    $model->save();
                }
            }

            return $this->response()->success('Success message...')->refresh();
        } else {
            return $this->response()->warning('You Select Multiple Requests Already Batched')->timeout(6000);
        }
    }

    public function form()
    {
        $this->date('batch_date', 'Deposit Date');
        $this->text('bank_ref', 'Bank Ref. No.')->rules('required');
    }
}
