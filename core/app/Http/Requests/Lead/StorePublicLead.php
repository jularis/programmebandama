<?php

namespace App\Http\Requests\Lead;

use App\Models\Cooperative;
use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class StorePublicLead extends CoreRequest
{
    use CustomFieldsRequestTrait;

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
        $cooperative = Cooperative::findOrFail($this->request->get('cooperative_id'));
        $rules = array();
        $rules['name'] = 'required';
        $rules['email'] = 'nullable|email:rfc|unique:leads,client_email,null,id,cooperative_id,' . $cooperative->id.'|unique:users,email,null,id,cooperative_id,' . $cooperative->id;

        $rules = $this->customFieldRules($rules);

        if(global_setting()->google_recaptcha_status == 'active' && global_setting()->ticket_form_google_captcha == 1 && (global_setting()->google_recaptcha_v2_status == 'active')){
            $rules['g-recaptcha-response'] = 'required';
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }

}
