<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|distinct|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'La lista prodotti è obbligatoria.',
            'products.array' => 'Il campo prodotti deve essere un array.',
            'products.min' => 'Deve essere presente almeno un prodotto.',
            'products.*.id.required' => 'L\'ID prodotto è obbligatorio.',
            'products.*.id.integer' => 'L\'ID prodotto deve essere un numero intero.',
            'products.*.id.distinct' => 'Non sono consentiti prodotti duplicati nello stesso ordine.',
            'products.*.id.exists' => 'Uno o più prodotti selezionati non esistono.',
            'products.*.quantity.required' => 'La quantità è obbligatoria per ogni prodotto.',
            'products.*.quantity.integer' => 'La quantità deve essere un numero intero.',
            'products.*.quantity.min' => 'La quantità minima per prodotto è 1.',
        ];
    }
}
