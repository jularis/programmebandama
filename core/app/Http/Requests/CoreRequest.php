<?php

namespace App\Http\Requests;

use App\Http\Helpers\Reply;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CoreRequest extends FormRequest
{

    protected function formatErrors(Validator  $validator)
    {
        return Reply::formErrors($validator);
    }

}
