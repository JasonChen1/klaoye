<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;

class Testimonial extends FormRequest
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
            "product_id"=>'required',
            "url" => 'required|string',
            "rating" => 'nullable|integer',
            "name" => 'required|string',
            "personal_des" => 'nullable|string',
            "description"=> 'nullable|string',
        ];
    }

    public function response(array $errors)
    {
        return Response::json($errors);    
    }
}
