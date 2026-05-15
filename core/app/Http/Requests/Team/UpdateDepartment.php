<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\CoreRequest;

class UpdateDepartment extends CoreRequest
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
            'department' => 'required|unique:departments,department,'.$this->route('department').',id,cooperative_id,' . auth()->user()->cooperative_id
        ];
    }

}
