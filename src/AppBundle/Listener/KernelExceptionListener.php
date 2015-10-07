<?php

namespace AppBundle\Listener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Intercept an exception related to FK constraint violation is thrown, add a flash
 * message and return to previous page.
 */
class KernelExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof ForeignKeyConstraintViolationException) {
            $request = $event->getRequest();
            $request->getSession()->getFlashBag()->add('danger', 'Cannot delete this element, it has relations.');
            $response = new RedirectResponse($request->headers->get('referer'));
            $event->setResponse($response);
        }
    }
}
