<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Il nome del prodotto è obbligatorio.',
            'name.string' => 'Il nome del prodotto deve essere una stringa.',
            'name.max' => 'Il nome del prodotto non può superare i 255 caratteri.',
            'description.string' => 'La descrizione del prodotto deve essere una stringa.',
            'price.required' => 'Il prezzo del prodotto è obbligatorio.',
            'price.numeric' => 'Il prezzo del prodotto deve essere un numero.',
            'price.min' => 'Il prezzo del prodotto deve essere almeno 0.',
            'stock.required' => 'La quantità in stock è obbligatoria.',
            'stock.integer' => 'La quantità in stock deve essere un numero intero.',
            'stock.min' => 'La quantità in stock deve essere almeno 0.',
            'image.image' => 'L\'immagine del prodotto deve essere un file di immagine.',
            'image.max' => 'L\'immagine del prodotto non può superare i 2MB.'
        ];
    }
}
