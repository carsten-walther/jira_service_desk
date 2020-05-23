<?php

namespace Walther\JiraServiceDesk\Widgets\Provider;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;

/**
 * Class ButtonProvider
 *
 * @package Walther\JiraServiceDesk\Widgets\Provider
 */
class ButtonProvider implements ButtonProviderInterface
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $target;

    /**
     * JSDButtonProvider constructor.
     *
     * @param string $action
     * @param string $title
     * @param string $target
     */
    public function __construct(string $action, string $title, string $target = '')
    {
        $this->action = $action;
        $this->title = $title;
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTarget() : string
    {
        return $this->target;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function getLink() : string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute(
            'help_JiraServiceDeskJira',
            ['tx_jiraservicedesk_help_jiraservicedeskjira[action]' => $this->action]
        );
    }
}
