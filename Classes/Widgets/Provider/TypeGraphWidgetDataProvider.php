<?php

namespace Walther\JiraServiceDesk\Widgets\Provider;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Resource\ServiceDesk;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Class TypeGraphWidgetDataProvider
 *
 * @package Walther\JiraServiceDesk\Widgets\Provider
 */
class TypeGraphWidgetDataProvider implements ChartDataProviderInterface
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
     * TypeGraphWidgetDataProvider constructor.
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

            $labels = [];
            $data = [];

            $counter = 0;
            foreach ($this->getRequests()->values as $value) {
                $labels[$counter] = $value->requestType->name;
                ++$data[$counter];
                $counter++;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'backgroundColor' => WidgetApi::getDefaultChartColors(),
                        'data' => $data
                    ]
                ]
            ];
        }
    }

    /**
     * getRequests
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequests()
    {
        if (AccessUtility::hasAccess()) {
            $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];
            return $this->requestResource->getCustomerRequests($serviceDeskId, 0, true, '', '', Request::ALL_REQUESTS, '', '', 0, 9999)->getBody();
        }
    }
}
