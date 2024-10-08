<?php

namespace App\Http\Requests;

use App\Exceptions\BestSellersValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class NYTBestSellersSearchRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;                                                                                                    // Hard return true until user authorization for API is added
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'author' => 'nullable|string',
            'isbn' => 'nullable|array',
            'isbn.*' => [                                                                                               // Elements inside isbn must be digits of length 10, or 13. Allow leading zeros.
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!is_numeric(trim($value)) || (strlen(trim($value)) !== 10 && strlen(trim($value)) !== 13)) {
                        $fail('An ISBN must contain 10 or 13 digits.');
                    }
                }
            ],
            'title' => 'nullable|string',
            'offset' => [                                                                                               // Integers provided as offset must be min 0, and multiples of 20
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (intval($value) % 20 !== 0) {
                        $fail('The offset must be a multiple of 20.');
                    }
                }
            ]
        ];
    }

    /**
     * Provide a better response than default handling
     *
     * @param Validator $validator
     * @return void
     * @throws BestSellersValidationException
     */
    protected function failedValidation(Validator $validator) : void
    {
        throw new BestSellersValidationException(
            $validator->errors()->messages(),
        );
    }
}
