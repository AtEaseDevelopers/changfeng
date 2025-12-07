<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;

class UpdateProductRequest extends FormRequest
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
        $id = $this->route('product');
        $rules = [
            'code' => 'required|string|max:255|unique:products,code,'.Crypt::decrypt($id),
            'name' => 'required|string|max:255|string|max:255',
            'price' => 'required|numeric|numeric',
            'status' => 'required',
            'created_at' => 'nullable|nullable',
            'updated_at' => 'nullable|nullable'
        ];
        
        return $rules;
    }
}
