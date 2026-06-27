<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // max in kilobytes
            'cover' => 'file|mimes:png,jpg|max:5120',
            'name' => 'required|string:max:255',
            'isIndividu' => 'required|boolean',
            'categories.*' => 'string',
            'deadline' => 'required',
            'maxMembers' => 'required|numeric',
            'price' => 'required|numeric',
            'techStacks.*' => 'required|string|max:255',
            'description' => 'required|string',
            'guideBookLink' => 'required|string|url',
            'criteria' => 'required',
            'criteria.*.name' => 'string',
            'criteria.*.percentage' => 'numeric'
        ];
    }
}
