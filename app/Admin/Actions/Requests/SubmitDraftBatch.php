<?php

namespace App\Admin\Actions\Requests;

use App\Models\Batch;
use App\Models\DraftBatch;
use App\Models\Requests;
use App\Models\RequestStatus;
use App\Models\Service;
use App\Models\ThirdParty;
use Encore\Admin\Actions\Action;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class SubmitDraftBatch extends Action
{
    protected $selector = '.submit-draft';

    public function handle(Request $request)
    {
        $draft_batch = DraftBatch::select('*');
        if (!Admin::user()->isAdministrator()) {
            $branches_ids = ThirdParty::find(Admin::user()->id)->branches()->get(['branches.id'])->toArray();
            $ids = [];
            foreach ($branches_ids as $branch_id) {
                $ids[] = $branch_id['id'];
            }
            $draft_batch = $draft_batch->whereHas('requests', function ($query) use ($ids) {
                $query->whereIn('branch_id', $ids);
            });
        }
        $filter_request = json_decode($request->filter_data);
        $array_search = ['service_id', 'embassy_id'];
        foreach ($array_search as $item_search) {
            if (isset($filter_request->$item_search)) {
                $draft_batch = $draft_batch->where($item_search, $filter_request->$item_search);
            }
        }
        $requests_ids = $draft_batch->pluck('request_id');
        $draft_batch->delete();
        if (count($requests_ids)) {
            $collection = Requests::findmany($requests_ids);
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
                return $this->response()->warning('You Select Multiple Requests with Multiple Service. Select 1 service ')->timeout(3000)->refresh();
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

                return $this->response()->success('Batch Created Successfully')->refresh();
            } else {
                return $this->response()->warning('You Select Multiple Requests Already Batched')->timeout(3000)->refresh();
            }
        }
    }

    public function form()
    {
        $this->date('batch_date', 'Deposit Date');
        $this->text('bank_ref', 'Bank Ref. No.')->rules('required');
        $this->hidden('filter_data')->value(json_encode(request()->query()));
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default submit-draft">Submit Draft Batch</a>
HTML;
    }
}
