<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = new JsonResponse();

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());

            $data = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];

            $response->setData($data);
        } elseif ($exception instanceof ValidationException) {
            $data = [];
            /** @var ConstraintViolation $violation */
            foreach ($exception->getViolations() as $violation) {
                $data['errors'][] = [
                    'code' => $violation->getCode(),
                    'message' => $violation->getMessage(),
                    'path' => $violation->getPropertyPath(),
                ];
            }

            $response->setData($data);
            $response->setStatusCode(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return;
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}