<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ImageValidation extends FormRequest
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
            'email'=>'required|string|email',            
            'picture'=>'mimes:jpeg,jpg,png,gif|required|max:20000'
        ];
    }
}