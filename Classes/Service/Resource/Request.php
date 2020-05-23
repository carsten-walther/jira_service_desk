<?php

namespace Walther\JiraServiceDesk\Service\Resource;

use Walther\JiraServiceDesk\Domain\Model\Attachment;
use Walther\JiraServiceDesk\Service\Response;
use Walther\JiraServiceDesk\Service\Service;

/**
 * Class Request
 *
 * @package Walther\JiraServiceDesk\Service
 */
class Request extends AbstractResource
{
    public const OPEN_REQUESTS = 'OPEN_REQUESTS';
    public const CLOSED_REQUESTS = 'CLOSED_REQUESTS';
    public const ALL_REQUESTS = 'ALL_REQUESTS';

    /**
     * @var string
     */
    protected $resource = 'request';

    /**
     * Get customer requests
     *
     * This method returns all customer requests for the user executing the query.
     * The returned customer requests are ordered chronologically by the latest activity on each request.
     * For example, the latest status transition or comment.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-get
     *
     * @param int    $serviceDeskId
     * @param int    $requestTypeId
     * @param bool   $expand
     * @param string $searchTerm
     * @param string $requestOwnership
     * @param string $requestStatus
     * @param string $approvalStatus
     * @param string $organizationId
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomerRequests(int $serviceDeskId = 0, int $requestTypeId = 0, bool $expand = false, string $searchTerm = '', string $requestOwnership = '', string $requestStatus = '', string $approvalStatus = '', string $organizationId = '', int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'serviceDeskId' => $serviceDeskId,
            'requestTypeId' => $requestTypeId ? : null,
            'searchTerm' => $searchTerm,
            'requestOwnership' => $requestOwnership,
            'requestStatus' => $requestStatus,
            'approvalStatus' => $approvalStatus,
            'organizationId' => $organizationId,
            'start' => $start,
            'limit' => $limit
        ];

        if ($expand) {
            $data['expand'] = ['participant', 'status', 'sla', 'requestType', 'serviceDesk'];
        }

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Create customer request
     *
     * This method creates a customer request in a service desk.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @param \Walther\JiraServiceDesk\Domain\Model\Request $request
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createCustomerRequest(\Walther\JiraServiceDesk\Domain\Model\Request $request) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData((array)$request)
            ->setUrl($this->resource)
            ->request();
    }

    /**
     * Get customer request by id or key
     *
     * This method returns a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-get
     *
     * @param string $issueIdOrKey
     * @param bool   $expand
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomerRequestByIdOrKey(string $issueIdOrKey, bool $expand = false) : Response
    {
        $data = [];

        if ($expand) {
            $data['expand'] = ['participant', 'status', 'sla', 'requestType', 'serviceDesk', 'attachment', 'action', 'comment'];
        }

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get approvals
     *
     * This method returns all approvals on a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-approval-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getApprovals(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/approval')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get approval by id
     *
     * This method returns an approval. Use this method to determine the status of an approval and the list of approvers.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-approval-approvalId-get
     *
     * @param string $issueIdOrKey
     * @param int    $approvalId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getApprovalById(string $issueIdOrKey, int $approvalId) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/approval/' . $approvalId)
            ->request();
    }

    /**
     * Answer approval
     *
     * This method enables a user to Approve or Decline an approval on a customer request.
     * The approval is assumed to be owned by the user making the call.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-approval-approvalId-post
     *
     * @param string $issueIdOrKey
     * @param int    $approvalId
     * @param string $decision
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function answerApproval(string $issueIdOrKey, int $approvalId, string $decision = '') : Response
    {
        $data = [
            'decision' => $decision
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/approval/' . $approvalId)
            ->request();
    }

    /**
     * Get attachments for request
     *
     * This method returns all the attachments for a customer requests.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-attachment-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAttachmentsForRequest(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/attachment')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Create attachment
     *
     * This method adds one or more temporary files (attached to the request's service desk
     * using servicedesk/{serviceDeskId}/attachTemporaryFile) as attachments to a customer request
     * and set the attachment visibility using the public flag. Also, it is possible to include a comment with the attachments.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-attachment-post
     *
     * @param                                                  $issueIdOrKey
     * @param \Walther\JiraServiceDesk\Domain\Model\Attachment $attachment
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createAttachment($issueIdOrKey, Attachment $attachment) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData((array)$attachment)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/attachment')
            ->request();
    }

    /**
     * Get request comments
     *
     * This method returns all comments on a customer request. No permissions error is provided if, for example,
     * the user doesn't have access to the service desk or request, the method simply returns an empty response.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-comment-get
     *
     * @param string $issueIdOrKey
     * @param bool   $public
     * @param bool   $internal
     * @param bool   $expand
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestComments(string $issueIdOrKey, bool $public = true, bool $internal = true, bool $expand = false, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'public' => $public ? 'true' : 'false',
            'internal' => $internal ? 'true' : 'false',
            'start' => $start,
            'limit' => $limit
        ];

        if ($expand) {
            $data['expand'] = ['participant', 'status', 'sla', 'requestType', 'serviceDesk', 'attachment', 'renderedBody'];
        }

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/comment')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Create request comment
     *
     * This method creates a public or private (internal) comment on a customer request,
     * with the comment visibility set by public. The user recorded as the author of the comment.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-comment-post
     *
     * @param string $issueIdOrKey
     * @param string $body
     * @param bool   $public
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createRequestComment(string $issueIdOrKey, string $body, bool $public = true) : Response
    {
        $data = [
            'body' => $body,
            'public' => $public ? 'true' : 'false'
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/comment')
            ->request();
    }

    /**
     * Get request comment by id
     *
     * This method returns details of a customer request's comment.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-comment-commentId-get
     *
     * @param string $issueIdOrKey
     * @param int    $commentId
     * @param bool   $expand
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestCommentById(string $issueIdOrKey, int $commentId, bool $expand = false) : Response
    {
        $data = [];

        if ($expand) {
            $data['expand'] = ['attachment', 'renderedBody'];
        }

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/comment/' . $commentId)
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get comment attachments
     *
     * This method returns the attachments referenced in a comment.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-comment-commentId-attachment-get
     *
     * @param string $issueIdOrKey
     * @param int    $commentId
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCommentAttachments(string $issueIdOrKey, int $commentId, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/comment/' . $commentId . '/attachment')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Get subscription status
     *
     * This method returns the notification subscription status of the user making the request.
     * Use this method to determine if the user is subscribed to a customer request's notifications.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-notification-get
     *
     * @param string $issueIdOrKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSubscriptionStatus(string $issueIdOrKey) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/notification')
            ->setGetParams([])
            ->request();
    }

    /**
     * Subscribe
     *
     * This method subscribes the user to receiving notifications from a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-notification-put
     *
     * @param string $issueIdOrKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribe(string $issueIdOrKey) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_PUT)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/notification')
            ->request();
    }

    /**
     * Unsubscribe
     *
     * This method unsubscribes the user from notifications from a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-notification-delete
     *
     * @param string $issueIdOrKey
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribe(string $issueIdOrKey) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_DELETE)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/notification')
            ->request();
    }

    /**
     * Get request participants
     *
     * This method returns a list of all the participants on a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-participant-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestParticipants(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/participant')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Add request participants
     *
     * This method adds participants to a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-participant-post
     *
     * @param string $issueIdOrKey
     * @param array  $usernames
     * @param array  $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addRequestParticipants(string $issueIdOrKey, array $usernames = [], array $accountIds = []) : Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/participant')
            ->request();
    }

    /**
     * Remove request participants
     *
     * This method removes participants from a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-participant-delete
     *
     * @param string $issueIdOrKey
     * @param array  $usernames
     * @param array  $accountIds
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeRequestParticipants(string $issueIdOrKey, array $usernames = [], array $accountIds = []) : Response
    {
        $data = [
            'usernames' => $usernames,
            'accountIds' => $accountIds
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_DELETE)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/participant')
            ->request();
    }

    /**
     * Get sla information
     *
     * This method returns all the SLA records on a customer request. A customer request can have zero or more SLAs.
     * Each SLA can have recordings for zero or more "completed cycles" and zero or 1 "ongoing cycle".
     * Each cycle includes information on when it started and stopped, and whether it breached the SLA goal.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-sla-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSlaInformation(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/sla')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get sla information by id
     *
     * This method returns the details for an SLA on a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-sla-slaMetricId-get
     *
     * @param string $issueIdOrKey
     * @param int    $slaMetricId
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSlaInformationById(string $issueIdOrKey, int $slaMetricId) : Response
    {
        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/sla/' . $slaMetricId)
            ->request();
    }

    /**
     * Get customer request status
     *
     * This method returns a list of all the statuses a customer Request has achieved.
     * A status represents the state of an issue in its workflow. An issue can have one active status only.
     * The list returns the status history in chronological order, most recent (current) status first.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-status-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestStatus(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/status')
            ->setGetParams($data)
            ->request();
    }

    /**
     * Get customer transitions
     *
     * This method returns a list of transitions, the workflow processes that moves a customer request
     * from one status to another, that the user can perform on a request. Use this method to provide a
     * user with a list if the actions they can take on a customer request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-transition-get
     *
     * @param string $issueIdOrKey
     * @param int    $start
     * @param int    $limit
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomerTransitions(string $issueIdOrKey, int $start = 0, int $limit = 20) : Response
    {
        $data = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_GET)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/transition')
            ->setGetParams($data)
            ->setExperimentalApi()
            ->request();
    }

    /**
     * Perform customer transition
     *
     * This method performs a customer transition for a given request and transition.
     * An optional comment can be included to provide a reason for the transition.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-issueIdOrKey-transition-post
     *
     * @param string $issueIdOrKey
     * @param int    $transitionId
     * @param string $additionalComment
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function performCustomerTransition(string $issueIdOrKey, int $transitionId, string $additionalComment = '') : Response
    {
        $data = [
            'id' => $transitionId,
            'additionalComment' => [
                'body' => $additionalComment
            ]
        ];

        return $this->service
            ->setType(Service::REQUEST_METHOD_POST)
            ->setPostData($data)
            ->setUrl($this->resource . '/' . $issueIdOrKey . '/transition')
            ->setExperimentalApi()
            ->request();
    }
}
