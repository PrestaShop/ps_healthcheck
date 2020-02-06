<?php

namespace PrestaShop\Module\HealthCheck\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Runner\Runner;

class ChecksRunner
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(array $checks)
    {
        $this->runner = new Runner();

        foreach ($checks as $check) {
            $this->addCheck($check);
        }
    }

    /**
     * @return \Laminas\Diagnostics\Result\Collection
     */
    public function run()
    {
        return $this->runner->run();
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = [];
        /** @var CheckInterface $check */
        foreach ($this->runner->getChecks() as $check) {
            $messages[$check->getLabel()] = $this->runner->getLastResults()->offsetGet($check)->getMessage();
        }

        return $messages;
    }

    public function addCheck(CheckInterface $check)
    {
        $this->runner->addCheck($check);
    }
}
