<?php

namespace Walther\JiraServiceDesk\Widgets;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Walther\JiraServiceDesk\Widgets\Provider\RequestsWidgetDataProvider;

/**
 * Class RequestsWidget
 *
 * @package Walther\JiraServiceDesk\Widgets
 */
class RequestsWidget implements WidgetInterface
{
    /**
     * @var \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Walther\JiraServiceDesk\Widgets\Provider\RequestsWidgetDataProvider
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
     * CustomerRequestsWidget constructor.
     *
     * @param \TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface            $configuration
     * @param \Walther\JiraServiceDesk\Widgets\Provider\RequestsWidgetDataProvider $dataProvider
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface                     $cache
     * @param \TYPO3\CMS\Fluid\View\StandaloneView                                 $view
     * @param \TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface                 $buttonProvider
     * @param array                                                                $options
     */
    public function __construct(WidgetConfigurationInterface $configuration, RequestsWidgetDataProvider $dataProvider, Cache $cache, StandaloneView $view, $buttonProvider = null, array $options = [])
    {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->cache = $cache;
        $this->view = $view;
        $this->buttonProvider = $buttonProvider;
        $this->options = array_merge(
            [
                'lifeTime' => 60*60*1,
                'limit' => 10,
                'page' => 0
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
        $this->view->setTemplate('Widget/Requests');
        $this->view->assignMultiple([
            'requests' => $this->getRequests(),
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'configuration' => $this->configuration
        ]);
        return $this->view->render();
    }

    /**
     * getChartData
     *
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequests()
    {
        $cacheHash = md5('getRequests:' . $this->options['page'] . ':' . $this->options['limit']);

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->dataProvider->getRequests($this->options['page'], $this->options['limit']);
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->options['lifeTime']);

        return $items;
    }
}
