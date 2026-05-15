<?php

namespace App\Http\Requests\OfflinePaymentSetting;

use App\Http\Requests\CoreRequest;

class StoreRequest extends CoreRequest
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
        $rules = [
            'description' => 'required',
        ];

        if (cooperative()) {
            $rules['name'] = 'required|unique:offline_payment_methods,name,null,id,cooperative_id,' . cooperative()->id;
        }
        else{
            $rules['name'] = 'required|unique:offline_payment_methods,name,null,id,cooperative_id,null';
        }

        return $rules;
    }

}
