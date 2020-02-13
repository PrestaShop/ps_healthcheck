<?php

namespace PrestaShop\Module\HealthCheck\Listener;

use PrestaShop\Module\HealthCheck\Repository\HealthCheckConfigRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class HealthCheckListener
{
    /**
     * @var LegacyContext
     */
    private $context;
    /**
     * @var HealthCheckConfigRepository
     */
    private $healthCheckConfigRepository;

    public function __construct(LegacyContext $context, HealthCheckConfigRepository $healthCheckConfigRepository)
    {
        $this->context = $context;
        $this->healthCheckConfigRepository = $healthCheckConfigRepository;
    }

    /**
     * Check if the user is allowed to access the healthcheck route.
     *
     * @return bool|null or redirect
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ('_admin_healthcheck' !== $request->get('_route')) {
            return null;
        }

        $healthCheckConfig = $this->healthCheckConfigRepository->getLastHealthCheckConfig();

        if (
            !empty($healthCheckConfig->getIps()) &&
            !empty($healthCheckConfig->getToken()) &&
            true === IpUtils::checkIp(
                $request->getClientIp(),
                array_map('trim', explode(',', $healthCheckConfig->getIps()))
            ) &&
            $request->headers->has('X-HEALTHCHECK-TOKEN') &&
            0 === strcmp($healthCheckConfig->getToken(), $request->headers->get('X-HEALTHCHECK-TOKEN'))
        ) {
            return true;
        }

        //employee not logged in
        $event->stopPropagation();

        //if http request - add 403 error
        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()) {
            $event->setResponse(
                new JsonResponse(null, Response::HTTP_FORBIDDEN)
            );

            return null;
        }

        //redirect to admin home page
        $event->setResponse(
            new RedirectResponse($this->context->getAdminLink('', false))
        );
    }
}
