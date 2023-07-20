<?php

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Database\OperationLog as OperationLogModel;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class AuthController extends BaseAuthController
{
    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);
        // dd($credentials);
        if ($this->guard()->attempt($credentials, $remember)) {
            if (true) {
                $log = [
                    'user_id' => Admin::user()->id,
                    'path' => substr($request->path(), 0, 255),
                    'method' => 'LOGIN',
                    'ip' => $request->getClientIp(),
                    'input' => Admin::user()->username.' Login',
                ];

                try {
                    OperationLogModel::create($log);
                } catch (\Exception $exception) {
                    // pass
                }

                return $this->sendLoginResponse($request);
            } else {
                Admin::guard()->logout();

                return back()->withInput()->withErrors([
                    'check_status' => 'Your Account is Not Active',
                 ]);
            }
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }
}
