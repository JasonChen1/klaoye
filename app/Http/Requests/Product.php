<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;

class Product extends FormRequest
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
            "name"=>'string|required',
            "code" => 'required|string|unique:products',
            "price" => 'required|numeric',
            "status" => 'nullable|integer',
            "stock" => 'required|integer',
            "size" => 'nullable|string',
            "description"=> 'nullable|string',
            'images' => 'array|nullable',
            'images.*'=>'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:10240',
        ];
    }

    public function response(array $errors)
    {
        return Response::json($errors);    
    }
}
