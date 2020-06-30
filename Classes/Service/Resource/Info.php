<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Class Info
 * @package Walther\JiraServiceDesk\Service
 */
class Info extends \Walther\JiraServiceDesk\Service\Resource\AbstractResource
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
    public function getInfo() : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource)
            ->request();
    }
}
