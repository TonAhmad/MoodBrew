<?php

namespace App\Http\Requests;

use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * MenuItemRequest - Validasi untuk create/update menu item
 */
class MenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'min:2'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => [
                'required',
                Rule::in([
                    MenuItem::CATEGORY_COFFEE,
                    MenuItem::CATEGORY_NON_COFFEE,
                    MenuItem::CATEGORY_PASTRY,
                    MenuItem::CATEGORY_MAIN_COURSE,
                ]),
            ],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            // Flavor profile fields
            'sweetness' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'bitterness' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'strength' => ['nullable', Rule::in(['light', 'medium', 'strong'])],
            'flavor_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu wajib diisi.',
            'name.min' => 'Nama menu minimal 2 karakter.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'stock_quantity.integer' => 'Stok harus berupa angka bulat.',
            'stock_quantity.min' => 'Stok tidak boleh negatif.',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox to boolean
        if ($this->has('is_available')) {
            $this->merge([
                'is_available' => $this->is_available === 'on' || $this->is_available === '1' || $this->is_available === true,
            ]);
        } else {
            $this->merge([
                'is_available' => false,
            ]);
        }
    }
}
