<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Class Requesttype
 * @package Walther\JiraServiceDesk\Service
 */
class Requesttype extends \Walther\JiraServiceDesk\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'requesttype';

    /**
     * Get all request types
     *
     * This method returns all customer request types used in the Jira Service Desk instance, optionally filtered by a query string.
     * Use servicedeskapi/servicedesk/{serviceDeskId}/requesttype to find the customer request types supported by a specific service desk.
     *
     * The returned list of customer request types can be filtered using the query parameter.
     * The parameter is matched against the customer request types' name or description.
     * For example, searching for "Install", "Inst", "Equi", or "Equipment" will match a customer request type with the name "Equipment Installation Request".
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-requesttype-get
     *
     * @param string $searchQuery
     * @param int    $start
     * @param int    $limit
     * @param bool   $expand
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllRequestTypes(string $searchQuery = '', int $start = 0, int $limit = 20, bool $expand = false) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'searchQuery' => $searchQuery,
            'start' => $start,
            'limit' => $limit
        ];

        if ($expand) {
            $data['expand'] = [];
        }

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource)
            ->setGetParams($data)
            ->request();
    }
}
