<?php

namespace App\Infrastructure\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class DebugHeadersListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $response->headers->set(
            'X-Debug-Time',
            (string)(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000
        );

        $response->headers->set(
            'X-Debug-Memory',
            (string)memory_get_peak_usage(true) / 1024
        );
    }
}
