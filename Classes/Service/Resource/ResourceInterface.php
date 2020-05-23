<?php

namespace Walther\JiraServiceDesk\Service\Resource;

use Walther\JiraServiceDesk\Service\Service;

/**
 * Interface ResouceInterface
 *
 * @package Walther\JiraServiceDesk\Service\Resource
 * @author  Carsten Walther
 */
interface ResourceInterface
{
    /**
     * ResourceInterface constructor.
     *
     * @param \Walther\JiraServiceDesk\Service\Service $service
     *
     * @return void
     */
    public function __construct(Service $service);

    /**
     * Function for logging to the system log.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function log(string $message = '');
}
