<?php

namespace Walther\JiraServiceDesk\Widgets\Provider;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Resource\ServiceDesk;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Class InformationWidgetDataProvider
 *
 * @package Walther\JiraServiceDesk\Widgets\Provider
 */
class InformationWidgetDataProvider
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
     * @var \Walther\JiraServiceDesk\Service\Resource\ServiceDesk
     */
    protected $serviceDeskResource;

    /**
     * InformationWidgetDataProvider constructor.
     */
    public function __construct()
    {
        if (AccessUtility::hasAccess()) {
            $this->service = !$this->service ? GeneralUtility::makeInstance(Service::class) : $this->service;
            if ($this->service->initialize()) {
                if (!$this->serviceDeskResource) {
                    $this->serviceDeskResource = GeneralUtility::makeInstance(ServiceDesk::class, $this->service);
                } else {
                    $this->serviceDeskResource->setService($this->service);
                }
                if (!$this->requestResource) {
                    $this->requestResource = GeneralUtility::makeInstance(Request::class, $this->service);
                } else {
                    $this->requestResource->setService($this->service);
                }
            }
        }
    }

    /**
     * getServiceDeskInformation
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getServiceDeskInformation()
    {
        if (AccessUtility::hasAccess()) {
            return $this->serviceDeskResource->getServiceDeskById((int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'])->getBody();
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
    public function getRequests(int $page = 0, int $limit = 9999)
    {
        if (AccessUtility::hasAccess()) {
            $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];
            $requestStatus = Request::ALL_REQUESTS;
            return $this->requestResource->getCustomerRequests($serviceDeskId, 0, true, '', '', $requestStatus, '', '', $page, $limit)->getBody();
        }
    }
}
