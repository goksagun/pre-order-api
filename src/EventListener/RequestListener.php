<?php

namespace App\EventListener;

use App\Utils\StringHelper;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class RequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        if (StringHelper::startsWith($request->getRequestUri(), '/_')) {
            return;
        }

        if ($request->headers->get('content-type') !== 'application/json') {
            throw new UnsupportedMediaTypeHttpException('Unsupported Media Type');
        }

        if (count($request->request->all())) {
            return;
        }

        $content = $request->getContent();

        if (empty($content)) {
            $content = '{}';
        }

        $data = json_decode($content, true);

        if (is_array($data)) {
            $request->request = new ParameterBag($data);
        } else {
            throw new BadRequestHttpException();
        }
    }
}