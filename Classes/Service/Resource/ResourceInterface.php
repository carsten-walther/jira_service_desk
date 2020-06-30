<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Interface ResouceInterface
 *
 * @package Walther\JiraServiceDesk\Service\Resource
 * @author Carsten Walther
 */
interface ResourceInterface
{
    /**
     * ResourceInterface constructor.
     *
     * @param \Walther\JiraServiceDesk\Service\Service $service
     * @return void
     */
    public function __construct(\Walther\JiraServiceDesk\Service\Service $service);

    /**
     * Function for logging to the system log.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function log(string $message = '');
}
