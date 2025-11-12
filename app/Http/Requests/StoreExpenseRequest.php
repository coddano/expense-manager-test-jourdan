<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //On vérifie que l'utilisateur a le droit de créer (via la policy)
        return $this->user()->can('create', Expense::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'amount' => 'required|numeric',
            'spent_at' => 'required|date',
            'category' => 'nullable|string|in:MEAL,TRAVEL,HOTEL,OTHER',
        ];
    }
}
