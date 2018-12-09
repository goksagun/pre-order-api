<?php

namespace App\Validation;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

abstract class AbstractValidation
{
    protected function validate($input, $constraints)
    {
        $validator = Validation::createValidator();

        $violations = $validator->validate($input, $constraints);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }

    abstract public function run(Request $request): void;
}