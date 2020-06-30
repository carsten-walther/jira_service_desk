<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Class Knowledgebase
 * @package Walther\JiraServiceDesk\Service
 */
class Knowledgebase extends \Walther\JiraServiceDesk\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'knowledgebase';

    /**
     * Get articles
     *
     * Returns articles which match the given query string across all service desks.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-group-Knowledgebase
     *
     * @param string $query
     * @param bool   $highlight
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getArticles(string $query, bool $highlight = true, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'query' => $query,
            'highlight' => $highlight,
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/article')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }
}
