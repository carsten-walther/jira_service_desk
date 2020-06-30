<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class ServiceDesk
 *
 * @package Walther\JiraServiceDesk\Service
 */
class ServiceDesk extends \Walther\JiraServiceDesk\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'servicedesk';

    /**
     * Get service desks
     *
     * This method returns all the service desks in the Jira Service Desk instance that the user has permission to access.
     * Use this method where you need a list of service desks or need to locate a service desk by name or keyword.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-get
     *
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getServiceDesks(int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setGetParams($data)
            ->setUrl($this->resource)
            ->request();
    }

    /**
     * Get service desk by id
     *
     * This method returns a service desk. Use this method to get service desk details whenever your application component
     * is passed a service desk ID but needs to display other service desk details.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-get
     *
     * @param int $serviceDeskId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getServiceDeskById(int $serviceDeskId) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId)
            ->request();
    }

    /**
     * Attach temporary file
     *
     * This method adds one or more temporary attachments to a service desk, which can then be permanently attached to a
     * customer request using servicedeskapi/request/{issueIdOrKey}/attachment.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-attachTemporaryFile-post
     *
     * @param int   $serviceDeskId
     * @param array $files
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function attachTemporaryFile(int $serviceDeskId, array $files) : \Walther\JiraServiceDesk\Service\Response
    {
        $multipartFields = [];

        foreach ($files as $file) {
            $multipartFields[] = [
                'name' => 'file',
                'filename' => $file['name'],
                'contents' => fopen($file['url'], 'rb')
            ];
        }

        $result = $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/attachTemporaryFile')
            ->setHeaders([
                'X-ExperimentalApi' => 'opt-in',
                'X-Atlassian-Token' => 'no-check'
            ])
            ->setMultipart($multipartFields)
            ->request();

        // Workaround: we have to reset the multipart stuff
        unset($this->service->options['multipart']);

        return $result;
    }

    /**
     * Get customers
     *
     * This method returns a list of the customers on a service desk.
     * The returned list of customers can be filtered using the query parameter.
     * The parameter is matched against customers' displayName, name, or email.
     * For example, searching for "John", "Jo", "Smi", or "Smith" will match a user with display name "John Smith".
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-customer-get
     *
     * @param int    $serviceDeskId
     * @param string $query
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomers(int $serviceDeskId, string $query = '', int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'query' => $query,
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/customer')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Add customers
     *
     * Adds one or more customers to a service desk. If any of the passed customers are associated with the
     * service desk, no changes will be made for those customers and the resource returns a 204 success code.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-customer-post
     *
     * @param int   $serviceDeskId
     * @param array $usernames
     * @param array $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addCustomers(int $serviceDeskId, array $usernames = [], array $accountIds = []) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/customer')
            ->request();
    }

    /**
     * Remove customers
     *
     * This method removes one or more customers from a service desk. The service desk must have closed access.
     * If any of the passed customers are not associated with the service desk, no changes will be made for
     * those customers and the resource returns a 204 success code.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-customer-delete
     *
     * @param int   $serviceDeskId
     * @param array $usernames
     * @param array $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeCustomers(int $serviceDeskId, array $usernames = [], array $accountIds = []) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/customer')
            ->request();
    }

    /**
     * Get articles
     *
     * Returns articles which match the given query and belong to the knowledge base linked to the service desk.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-knowledgebase-article-get
     *
     * @param int    $serviceDeskId
     * @param string $query
     * @param bool   $highlight
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getArticles(int $serviceDeskId, string $query, bool $highlight = false, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'query' => $query,
            'highlight' => $highlight ? 'true' : 'false',
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/knowledgebase/article')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Get queues
     *
     * This method returns the queues in a service desk. To include a customer request count for each queue
     * (in the issueCount field) in the response, set the query parameter includeCount to true (its default is false).
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-queue-get
     *
     * @param int  $serviceDeskId
     * @param bool $includeCount
     * @param int  $start
     * @param int  $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQueues(int $serviceDeskId, bool $includeCount = false, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'includeCount' => $includeCount,
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/queue')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Get queue
     *
     * This method returns a specific queues in a service desk. To include a customer request count for the
     * queue (in the issueCount field) in the response, set the query parameter includeCount to true (its default is false).
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-queue-queueId-get
     *
     * @param int  $serviceDeskId
     * @param int  $queueId
     * @param bool $includeCount
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQueue(int $serviceDeskId, int $queueId, bool $includeCount = false) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'includeCount' => $includeCount
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/queue/' . $queueId)
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Get issues in queue
     *
     * This method returns the customer requests in a queue. Only fields that the queue is configured to
     * show are returned. For example, if a queue is configured to show description and due date,
     * then only those two fields are returned for each customer request in the queue.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-queue-queueId-issue-get
     *
     * @param int $serviceDeskId
     * @param int $queueId
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getIssuesInQueue(int $serviceDeskId, int $queueId, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/queue/' . $queueId . '/issue')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Get request types
     *
     * This method returns all customer request types from a service desk. There are two parameters for filtering the returned list:
     * - groupId which filters the results to items in the customer request type group.
     * - searchQuery which is matched against request types' name or description. For example, the strings "Install", "Inst", "Equi", or "Equipment" will match a request type with the name "Equipment Installation Request".
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-get
     *
     * @param int    $serviceDeskId
     * @param bool   $expand
     * @param int    $groupId
     * @param string $searchQuery
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestTypes(int $serviceDeskId, bool $expand = false, int $groupId = null, string $searchQuery = '', int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'groupId' => $groupId,
            'searchQuery' => $searchQuery,
            'start' => $start,
            'limit' => $limit
        ];

        if ($expand) {
            $data['expand'] = 'group,serviceDesk,participant,status,sla,requestType,serviceDesk';
        }

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Create request type
     *
     * This method enables a customer request type to be added to a service desk based on an issue type.
     * Note that not all customer request type fields can be specified in the request and these fields are given the following default values:
     * - Request type icon is given the question mark icon.
     * - Request type groups is left empty, which means this customer request type will not be visible on the customer portal.
     * - Request type status mapping is left empty, so the request type has no custom status mapping but inherits the status map from the issue type upon which it is based.
     * - Request type field mapping is set to show the required fields as specified by the issue type used to create the customer request type.
     * These fields can be updated by a service desk administrator using the Request types option in Project settings.
     * Request Types are created in next-gen projects by creating Issue Types. Please use the Jira Cloud Platform Create issue type endpoint instead.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-post
     *
     * @param int    $serviceDeskId
     * @param string $issueTypeId
     * @param string $name
     * @param string $description
     * @param string $helpText
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createRequestType(int $serviceDeskId, string $issueTypeId = '', string $name = '', string $description = '', string $helpText = '') : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'issueTypeId' => $issueTypeId,
            'name' => $name,
            'description' => $description,
            'helpText' => $helpText
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype')
            ->request();
    }

    /**
     * Get request type by id
     *
     * This method returns a customer request type from a service desk.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-get
     *
     * @param int  $serviceDeskId
     * @param int  $requestTypeId
     * @param bool $expand
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestTypeById(int $serviceDeskId, int $requestTypeId, bool $expand = false) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [];

        if ($expand) {
            $data['expand'] = 'group,serviceDesk,participant,status,sla,requestType,serviceDesk';
        }

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get request type fields
     *
     * This method returns the fields for a service desk's customer request type.
     * Also, the following information about the user's permissions for the request type is returned:
     * - canRaiseOnBehalfOf returns true if the user has permission to raise customer requests on behalf of other customers. Otherwise, returns false.
     * - canAddRequestParticipants returns true if the user can add customer request participants. Otherwise, returns false.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-field-get
     *
     * @param int $serviceDeskId
     * @param int $requestTypeId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestTypeFields(int $serviceDeskId, int $requestTypeId) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId . '/field')
            ->request();
    }

    /**
     * Get properties keys
     *
     * Returns the keys of all properties for a request type.
     * Properties for a Request Type in next-gen are stored as Issue Type properties and therefore the keys of all properties for a request type are also available by calling the Jira Cloud Platform Get issue type property keys endpoint.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-property-get
     *
     * @param int $serviceDeskId
     * @param int $requestTypeId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPropertiesKeys(int $serviceDeskId, int $requestTypeId) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId . '/property')
            ->request();
    }

    /**
     * Get property
     *
     * Returns the value of the property from a request type.
     * Properties for a Request Type in next-gen are stored as Issue Type properties and therefore also available by calling the Jira Cloud Platform Get issue type property endpoint.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-property-propertyKey-get
     *
     * @param int    $serviceDeskId
     * @param int    $requestTypeId
     * @param string $propertyKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProperty(int $serviceDeskId, int $requestTypeId, string $propertyKey) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId . '/property/' . $propertyKey)
            ->request();
    }

    /**
     * Set property
     *
     * Sets the value of a request type property. Use this resource to store custom data against a request type.
     * Properties for a Request Type in next-gen are stored as Issue Type properties and therefore can also be set by calling the Jira Cloud Platform Set issue type property endpoint.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-property-propertyKey-put
     *
     * @param int    $serviceDeskId
     * @param int    $requestTypeId
     * @param string $propertyKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setProperty(int $serviceDeskId, int $requestTypeId, string $propertyKey) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_PUT)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId . '/property/' . $propertyKey)
            ->request();
    }

    /**
     * Delete property
     *
     * Removes a property from a request type.
     * Properties for a Request Type in next-gen are stored as Issue Type properties and therefore can also be deleted by calling the Jira Cloud Platform Delete issue type property endpoint.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttype-requestTypeId-property-propertyKey-delete
     *
     * @param int    $serviceDeskId
     * @param int    $requestTypeId
     * @param string $propertyKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteProperty(int $serviceDeskId, int $requestTypeId, string $propertyKey) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttype/' . $requestTypeId . '/property/' . $propertyKey)
            ->request();
    }

    /**
     * Get request type groups
     *
     * This method returns a service desk's customer request type groups. Jira Service Desk administrators can arrange
     * the customer request type groups in an arbitrary order for display on the customer portal; the groups are returned in this order.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-requesttypegroup-get
     *
     * @param int $serviceDeskId
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestTypeGroup(int $serviceDeskId, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $serviceDeskId . '/requesttypegroup')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }
}
