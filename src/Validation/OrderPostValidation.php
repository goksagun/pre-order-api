<?php

namespace App\Validation;

use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPostValidation extends AbstractValidation
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
                    new Assert\Regex(['pattern' => '/^(5|05).*$/', 'message' => 'This value should be a mobile phone.']),
                ],
            ]
        );

        $this->validate($request->request->all(), $constraints);
    }
}