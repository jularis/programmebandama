<?php

namespace App\Http\Requests\Admin\Employee;

use App\Models\EmployeeDetail;
use App\Models\EmployeeDetails;
use Illuminate\Validation\Rule;
use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class UpdateRequest extends CoreRequest
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
        \Illuminate\Support\Facades\Validator::extend('check_superadmin', function ($attribute, $value, $parameters, $validator) {
            return !\App\Models\User::withoutGlobalScopes([\App\Scopes\ActiveScope::class, \App\Scopes\CooperativeScope::class])
                ->where('email', $value)
                ->exists();
        });

        $detailID = EmployeeDetail::where('user_id', $this->route('employee'))->first();
        $setting = cooperative();

        $exists = !Rule::exists('users')->where(function ($query) {
            return $query->where('is_superadmin', 0);
        });
         
        $rules = [ 
            'email' => 'required|max:100|unique:users,email,'.$this->route('employee').',id,cooperative_id,' . cooperative()->id.'|'.$exists,
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:250',
            'hourly_rate' => 'nullable|numeric',
            'department' => 'required',
            'designation' => 'required',
            'joining_date' => 'required',
            'last_date' => 'nullable|date_format:"Y-m-d"|after_or_equal:joining_date',
            'date_of_birth' => 'nullable|date_format:"Y-m-d"|before_or_equal:'.now($setting->timezone)->toDateString(),
            'probation_end_date' => 'nullable|date_format:"Y-m-d"|after_or_equal:joining_date',
            'notice_period_start_date' => 'nullable|required_with:notice_period_end_date|date_format:"Y-m-d"',
            'notice_period_end_date' => 'nullable|required_with:notice_period_start_date|date_format:"Y-m-d"|after_or_equal:notice_period_start_date',
            'internship_end_date' => 'nullable|date_format:"Y-m-d"|after_or_equal:joining_date',
            'contract_end_date' => 'nullable|date_format:"Y-m-d"|after_or_equal:joining_date',
        ];

         
        if (request()->password != '') {
            $rules['password'] = 'required|min:8|max:50';
        }

         

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }

    public function messages()
    {
        return [
            'email.check_superadmin' => __('superadmin.emailAlreadyExist'),
            'email.exists' => __('validation.notAllowed')
        ];
    }

}
