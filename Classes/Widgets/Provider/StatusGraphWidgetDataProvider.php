<?php

namespace Walther\JiraServiceDesk\Widgets\Provider;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Class StatusGraphWidgetDataProvider
 *
 * @package Walther\JiraServiceDesk\Widgets\Provider
 */
class StatusGraphWidgetDataProvider implements ChartDataProviderInterface
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
     * @var \TYPO3\CMS\Core\Localization\LanguageService
     */
    private $languageService;

    /**
     * StatusGraphWidgetDataProvider constructor.
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;

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
     * getChartData
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChartData() : array
    {
        if (AccessUtility::hasAccess()) {
            $open = $this->getRequestsCountByStatus(Request::OPEN_REQUESTS);
            $closed = $this->getRequestsCountByStatus(Request::CLOSED_REQUESTS);

            return [
                'labels' => [
                    $this->languageService->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:label.requestStatus.OPEN_REQUESTS'),
                    $this->languageService->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:label.requestStatus.CLOSED_REQUESTS')
                ],
                'datasets' => [
                    [
                        'backgroundColor' => WidgetApi::getDefaultChartColors(),
                        'data' => [$open, $closed]
                    ]
                ],
            ];
        }
    }

    /**
     * getRequestsCountByStatus
     *
     * @param $requestStatus
     *
     * @return null|int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequestsCountByStatus($requestStatus) : ?int
    {
        if (AccessUtility::hasAccess()) {
            $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];
            $requests = $this->requestResource->getCustomerRequests($serviceDeskId, 0, true, '', '', $requestStatus, '', '', 0, 9999);
            return $requests->getBody()->size;
        }
    }
}
