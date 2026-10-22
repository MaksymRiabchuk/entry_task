<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function productData(): array
    {
        $data = $this->validated();
        return [
            'name' => $data['name'],
            'quantity' => (int)$data['quantity'],
            'price' => (float)$data['price'],
        ];
    }
}
