<?php

namespace Walther\JiraServiceDesk\Service\Resource;

use Walther\JiraServiceDesk\Service\Response;
use Walther\JiraServiceDesk\Service\Service;

/**
 * Class Info
 *
 * @package Walther\JiraServiceDesk\Service
 */
class Info extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'info';

    /**
     * Get info
     *
     * This method retrieves information about the Jira Service Desk instance such as software version, builds, and related links.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-info-get
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInfo() : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource)
            ->request();
    }
}
