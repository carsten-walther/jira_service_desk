<?php

namespace Walther\JiraServiceDesk\Widgets;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\EventDataInterface;
use TYPO3\CMS\Dashboard\Widgets\RequireJsModuleInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Walther\JiraServiceDesk\Utility\AccessUtility;
use Walther\JiraServiceDesk\Widgets\Provider\TypeGraphWidgetDataProvider;

/**
 * Class TypeGraphWidget
 *
 * @package Walther\JiraServiceDesk\Widgets
 */
class TypeGraphWidget implements WidgetInterface, EventDataInterface, AdditionalCssInterface, RequireJsModuleInterface
{
    /**
     * @var \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Walther\JiraServiceDesk\Widgets\Provider\TypeGraphWidgetDataProvider
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
     * TypeGraphWidget constructor.
     *
     * @param \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface             $configuration
     * @param \Walther\JiraServiceDesk\Widgets\Provider\TypeGraphWidgetDataProvider $dataProvider
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface                      $cache
     * @param \TYPO3\CMS\Fluid\View\StandaloneView                                  $view
     * @param \TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface                  $buttonProvider
     * @param array                                                                 $options
     */
    public function __construct(WidgetConfigurationInterface $configuration, TypeGraphWidgetDataProvider $dataProvider, Cache $cache, StandaloneView $view, $buttonProvider = null, array $options = [])
    {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->cache = $cache;
        $this->view = $view;
        $this->buttonProvider = $buttonProvider;
        $this->options = array_merge([
            'lifeTime' => 60 * 60 * 24
        ], $options);
    }

    /**
     * renderWidgetContent
     *
     * @return string
     */
    public function renderWidgetContent() : string
    {
        $this->view->setTemplate('Widget/TypeGraph');
        $this->view->assignMultiple([
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'hasAccess' => AccessUtility::hasAccess(),
            'configuration' => $this->configuration
        ]);
        return $this->view->render();
    }

    /**
     * getEventData
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEventData() : array
    {
        if (AccessUtility::hasAccess()) {
            return [
                'graphConfig' => [
                    'type' => 'doughnut',
                    'options' => [
                        'maintainAspectRatio' => FALSE,
                        'legend' => [
                            'display' => TRUE,
                            'position' => 'bottom'
                        ],
                        'cutoutPercentage' => 60
                    ],
                    'data' => $this->getChartData(),
                ],
            ];
        }

        return [];
    }

    /**
     * getCssFiles
     *
     * @return array
     */
    public function getCssFiles() : array
    {
        return [
            'EXT:dashboard/Resources/Public/Css/Contrib/chart.css'
        ];
    }

    /**
     * getRequireJsModules
     *
     * @return array
     */
    public function getRequireJsModules() : array
    {
        return [
            'TYPO3/CMS/Dashboard/Contrib/chartjs',
            'TYPO3/CMS/Dashboard/ChartInitializer',
        ];
    }

    /**
     * getChartData
     *
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getChartData()
    {
        $cacheHash = md5('getChartDataTypeGraph');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->dataProvider->getChartData();
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->options['lifeTime']);

        return $items;
    }
}
