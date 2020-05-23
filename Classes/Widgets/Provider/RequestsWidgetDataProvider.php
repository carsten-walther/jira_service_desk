<?php

namespace Walther\JiraServiceDesk\Widgets\Provider;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Response;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Class RequestsWidgetDataProvider
 *
 * @package Walther\JiraServiceDesk\Widgets\Provider
 */
class RequestsWidgetDataProvider
{
    /**
     * @var \Walther\JiraServiceDesk\Service\Service
     */
    protected $service;

    /**
     * @var \Walther\JiraServiceDesk\Service\Resource\Request
     */
    protected $requestResource;

    /**
     * RequestsWidgetDataProvider constructor.
     */
    public function __construct()
    {
        if (AccessUtility::hasAccess()) {
            $this->service = !$this->service ? GeneralUtility::makeInstance(Service::class) : $this->service;
            if ($this->service->initialize()) {
                if (!$this->requestResource) {
                    $this->requestResource = GeneralUtility::makeInstance(Request::class, $this->service);
                } else {
                    $this->requestResource->setService($this->service);
                }
            }
        }
    }

    /**
     * getRequests
     *
     * @param int $page
     * @param int $limit
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequests(int $page = 0, int $limit = 10)
    {
        if (AccessUtility::hasAccess()) {
            $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];
            $requestStatus = Request::ALL_REQUESTS;
            return $this->requestResource->getCustomerRequests($serviceDeskId, 0, true, '', '', $requestStatus, '', '', $page, $limit)->getBody();
        }
    }
}
