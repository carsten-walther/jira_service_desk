<?php

namespace Walther\JiraServiceDesk\Widgets;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Walther\JiraServiceDesk\Utility\AccessUtility;
use Walther\JiraServiceDesk\Widgets\Provider\InformationWidgetDataProvider;

/**
 * Class InformationWidget
 *
 * @package Walther\JiraServiceDesk\Widgets
 */
class InformationWidget implements WidgetInterface
{
    /**
     * @var \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Walther\JiraServiceDesk\Widgets\Provider\InformationWidgetDataProvider
     */
    private $dataProvider;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var \TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface|null
     */
    private $buttonProvider;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * InformationWidget constructor.
     *
     * @param \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface               $configuration
     * @param \Walther\JiraServiceDesk\Widgets\Provider\InformationWidgetDataProvider $dataProvider
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface                        $cache
     * @param \TYPO3\CMS\Fluid\View\StandaloneView                                    $view
     * @param \TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface                    $buttonProvider
     * @param array                                                                   $options
     */
    public function __construct(WidgetConfigurationInterface $configuration, InformationWidgetDataProvider $dataProvider, Cache $cache, StandaloneView $view, $buttonProvider = null, array $options = [])
    {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->cache = $cache;
        $this->view = $view;
        $this->buttonProvider = $buttonProvider;
        $this->options = array_merge(
            [
                'lifeTime' => 60*60*24*30
            ],
            $options
        );
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function renderWidgetContent() : string
    {
        $requests = [];
        $serviceDesk = [];
        $requestCounts = 0;

        if (AccessUtility::hasAccess()) {
            $requests = $this->getRequests();

            $requestCounts = [];
            if (is_array($requests->values)) {
                foreach ($requests->values as $key => $request) {
                    $requestCounts[$request->currentStatus->status]++;
                }
            }

            $serviceDesk = $this->getServiceDeskInformation();
        }

        $this->view->setTemplate('Widget/Information');
        $this->view->assignMultiple([
            'serviceDesk' => $serviceDesk,
            'requests' => $requests,
            'requestCounts' => $requestCounts,
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'hasAccess' => AccessUtility::hasAccess(),
            'configuration' => $this->configuration
        ]);
        return $this->view->render();
    }

    /**
     * getServiceDeskInformation
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getServiceDeskInformation()
    {
        $cacheHash = md5('getServiceDeskInformation');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->dataProvider->getServiceDeskInformation();
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->options['lifeTime']);

        return $items;
    }

    /**
     * getChartData
     *
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequests()
    {
        $cacheHash = md5('getRequests');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->dataProvider->getRequests();
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->options['lifeTime']);

        return $items;
    }
}
