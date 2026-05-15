<?php

namespace App\Http\Requests\SuperAdmin\Register;

use App\Models\Cooperative;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $cooperative = Cooperative::where('hash', request()->cooperative_hash)->firstOrFail();
        $global = global_setting();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|unique:users,email,null,id,cooperative_id,' . $cooperative->id,
            'password' => 'required|min:8',
        ];

        if ($global && $global->sign_up_terms == 'yes') {
            $rules['terms_and_conditions'] = 'required';
        }

        return $rules;
    }

}
