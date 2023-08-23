<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // la clé est le nom de l'événement et la valeur est la méthode à appeler
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            $session = $event->getRequest()->getSession();
            $session->getFlashBag()->add('error', 'Vous n\'avez pas les droits pour effectuer cette action.');
            $event->setResponse(new RedirectResponse($this->router->generate('task_list')));
        }
    }
}
