<?php

namespace Walther\JiraServiceDesk\Service\Resource;

use Walther\JiraServiceDesk\Service\Response;
use Walther\JiraServiceDesk\Service\Service;

/**
 * Class Customer
 *
 * @package Walther\JiraServiceDesk\Service
 */
class Customer extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'customer';

    /**
     * Create customer
     *
     * This method adds a customer to the Jira Service Desk instance by passing a JSON file including an email address
     * and display name. The display name does not need to be unique. The record's identifiers, name and key, are
     * automatically generated from the request details.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-customer-post
     *
     * @param string $email
     * @param string $displayName
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createCustomer(string $email, string $displayName) : Response
    {
        $data = [
            'email' => $email,
            'displayName' => $displayName
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource)
            ->request();
    }
}
