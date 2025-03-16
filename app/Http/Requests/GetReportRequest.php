<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetReportRequest extends FormRequest
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
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'item_ids' => ['required', 'array'],
            'item_ids.*' => ['string', 'exists:items,model'], // Assumes item IDs are integers and exist in the `items` table
        ];
    }
}
