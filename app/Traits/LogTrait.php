<?php

namespace App\Traits;

use App\Models\SmsGateway;
use Encore\Admin\Facades\Admin;
use App\Models\OrganizationDetails;
use Encore\Admin\Auth\Database\OperationLog;
use Illuminate\Http\Request;

/**
 * It's main purpose is to manage media files on both public and storage.
 */
trait LogTrait
{
    public function create_log($input, $method)
    {
        $log = [
                'user_id' => Admin::user()->id,
                'path'    => substr(request()->path(), 0, 255),
                'method'  => $method,//request()->method(),
                'ip'      => request()->getClientIp(),
                'input'   =>  Admin::user()->name.' '.$input,
            ];

        try {
            OperationLog::create($log);
        } catch (\Exception $exception) {
            // pass
        }
    }
}
