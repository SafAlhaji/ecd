<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DraftBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'req_id' => 'required|'.Rule::exists('requests', 'id')->whereNull('batch_id').'|unique:draft_batch,request_id',
        ];
    }

    public function messages()
    {
        return [
            'req_id.exists' => 'Request Not Exists or Already Batched',
            'req_id.unique' => 'Request Already in Draft',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => implode(', ', $validator->errors()->all())], 200));
    }
}
