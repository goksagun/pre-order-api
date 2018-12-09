<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    protected $code = 0;
    protected $message = 'Validation failed!';
    protected $violations;

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     */
    public function setViolations(ConstraintViolationListInterface $violations): void
    {
        $this->violations = $violations;
    }
}