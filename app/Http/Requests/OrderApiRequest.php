<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // The middleware already validates the restaurant and merges it into the request.
        // So we just need to check if the restaurant is present.
        return $this->has('restaurant') || $this->attributes->has('restaurant');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'order_type' => ['required', Rule::in(['dinein', 'takeaway', 'delivery'])],
            'order_info' => ['nullable', 'string'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'source' => ['required', Rule::in(['whatsapp', 'in-app'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
