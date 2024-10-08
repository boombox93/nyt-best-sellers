<?php

namespace App\Exceptions;

use Exception;

class BestSellersValidationException extends Exception
{
    private $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;

        parent::__construct(
            'Validation error encountered.',
            422
        );
    }

    /**
     * Render the exception errors, messages, status, and success
     *
     * @return array
     */
    public function render() : array
    {
        return [
            'success' => false,
            'status' => 422,                                                                                            // Internal failure
            'message' => 'An invalid parameter was provided.',
            'error' => $this->errors,
        ];
    }
}
