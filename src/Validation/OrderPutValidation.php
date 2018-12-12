<?php

namespace App\Validation;

use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPutValidation extends AbstractValidation
{
    public function run(Request $request): void
    {
        $constraints = new Assert\Collection(
            [
                'type' => [
                    new Assert\Choice(Order::TYPES),
                ],
                'firstName' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
                'lastName' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
                'email' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'phone' => [
                    new Assert\NotBlank(),
                    // Use simple regex pattern for validating mobile phone number starting by 05 or 5 (https://regexr.com/44ob5)
                    new Assert\Regex(['pattern' => Order::PHONE_REGEX_PATTERN, 'message' => Order::PHONE_REGEX_MESSAGE]),
                ],
                'status' => [
                    new Assert\Choice(Order::STATUSES),
                ],
            ]
        );

        $this->validate($request->request->all(), $constraints);
    }
}