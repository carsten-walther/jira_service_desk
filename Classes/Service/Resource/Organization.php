<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service\Resource;

/**
 * Class Organization
 * @package Walther\JiraServiceDesk\Service
 */
class Organization extends \Walther\JiraServiceDesk\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $resource = 'organization';

    /**
     * Get organizations
     *
     * This method returns a list of organizations in the Jira Service Desk instance.
     * Use this method when you want to present a list of organizations or want to locate an organization by name.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-get
     *
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrganizations(int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Create organization
     *
     * This method creates an organization by passing the name of the organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-post
     *
     * @param string $name
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrganization(string $name) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'name' => $name
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource)
            ->request();
    }

    /**
     * Get organization
     *
     * This method returns details of an organization. Use this method to get organization details whenever
     * your application component is passed an organization ID but needs to display other organization details.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-get
     *
     * @param int $id
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrganization(int $id) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $id)
            ->request();
    }

    /**
     * Delete organization
     *
     * This method deletes an organization. Note that the organization is deleted regardless of other associations
     * it may have. For example, associations with service desks.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-delete
     *
     * @param string $id
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteOrganization(string $id) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setUrl($this->resource . '/' . $id)
            ->request();
    }

    /**
     * Get properties keys
     *
     * Returns the keys of all properties for an organization. Use this resource when you need to find out what
     * additional properties items have been added to an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-property-get
     *
     * @param string $id
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPropertiesKeys(string $id) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $id . '/property')
            ->request();
    }

    /**
     * Set property
     *
     * Sets the value of a property for an organization. Use this resource to store custom data against an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-property-propertyKey-put
     *
     * @param string $id
     * @param string $propertyKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setProperty(string $id, string $propertyKey)
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_PUT)
            ->setUrl($this->resource . '/' . $id . '/property/' . $propertyKey)
            ->request();
    }

    /**
     * Delete property
     *
     * Removes a property from an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-property-propertyKey-delete
     *
     * @param string $id
     * @param string $propertyKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteProperty(string $id, string $propertyKey) : \Walther\JiraServiceDesk\Service\Response
    {
        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setUrl($this->resource . '/' . $id . '/property/' . $propertyKey)
            ->request();
    }

    /**
     * Get users in organization
     *
     * This method returns all the users associated with an organization. Use this method where you want to
     * provide a list of users for an organization or determine if a user is associated with an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-user-get
     *
     * @param int $id
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsersInOrganization(int $id, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $id . '/user')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Add users to organization
     *
     * This method adds users to an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-user-post
     *
     * @param int   $id
     * @param array $usernames
     * @param array $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addUsersToOrganization(int $id, array $usernames = [], array $accountIds = []) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $id . '/user')
            ->request();
    }

    /**
     * Remove users from organization
     *
     * This method removes users from an organization.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-organization-organizationId-user-delete
     *
     * @param int   $id
     * @param array $usernames
     * @param array $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeUsersFromOrganization(int $id, array $usernames = [], array $accountIds = []) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $id . '/user')
            ->request();
    }

    /**
     * Get organizations by serviceDeskId
     *
     * This method returns a list of all organizations associated with a service desk.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-organization-get
     *
     * @param int $id
     * @param int $start
     * @param int $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrganizationsByServiceDeskId(int $id, int $start = 0, int $limit = 20) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_GET)
            ->setUrl('servicedesk/' . $id . '/' . $this->resource)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Add organization to service desk
     *
     * This method adds an organization to a service desk. If the organization ID is already associated with the service
     * desk, no change is made and the resource returns a 204 success code.
     *
     * @param int $id
     * @param int $organizationId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addOrganizationToServiceDesk(int $id, int $organizationId) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'organizationId' => $organizationId
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl('servicedesk/' . $id . '/' . $this->resource)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Remove organization from service desk
     *
     * This method removes an organization from a service desk. If the organization ID does not match an organization associated
     * with the service desk, no change is made and the resource returns a 204 success code.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-servicedesk-serviceDeskId-organization-delete
     *
     * @param int $id
     * @param int $organizationId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeOrganizationFromServiceDesk(int $id, int $organizationId) : \Walther\JiraServiceDesk\Service\Response
    {
        $data = [
            'organizationId' => $organizationId
        ];

        return $this->service
            ->setType(\Walther\JiraServiceDesk\Service\Service::REQUEST_METHOD_DELETE)
            ->setPostData($data)
            ->setUrl('servicedesk/' . $id . '/' . $this->resource)
            ->setGetParams($data)
            ->request();
    }
}
