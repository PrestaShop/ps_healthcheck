<?php

namespace PrestaShop\Module\HealthCheck\Form;

use PrestaShop\Module\HealthCheck\Repository\HealthCheckConfigRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class HealthCheckConfigFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var HealthCheckConfigRepository
     */
    private $repository;

    public function __construct(
        HealthCheckConfigRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $healthCheckConfig = $this->repository->getLastHealthCheckConfig();

        if (null === $healthCheckConfig) {
            return [];
        }

        return ['health_check_config' => [
            'id_health_check_config' => $healthCheckConfig->getId(),
            'token' => $healthCheckConfig->getToken(),
            'ips' => $healthCheckConfig->getIps(),
        ]];
    }

    /**
     * @return array
     */
    public function setData(array $data)
    {
        $this->repository->update($data['health_check_config']);

        return [];
    }
}
