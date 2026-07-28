<?php

namespace App\Http\Requests;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Foundation\Http\FormRequest;

class RegionsRequest extends FormRequest
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
            'regions' => 'required|array|min:1',
            'regions.*' => 'required|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $regions = $this->input('regions', []);

            $validNames = City::whereIn('name', $regions)->pluck('name')
                ->merge(Governorate::whereIn('name', $regions)->pluck('name'))
                ->unique();

            $invalid = array_diff($regions, $validNames->toArray());

            if (!empty($invalid)) {
                $validator->errors()->add(
                    'regions',
                    'These regions are invalid: ' . implode(', ', $invalid)
                );
            }
        });
    }
}
