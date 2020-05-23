<?php

namespace Walther\JiraServiceDesk\Domain\Model;

/**
 * Class Request
 *
 * @package Walther\JiraServiceDesk\Domain\Model
 * @author  Carsten Walther
 */
class Request
{
    /**
     * VARS
     */
    public const VAR_COMPONENTS = 'components';
    public const VAR_DESCRIPTION = 'description';
    public const VAR_DUE_DATE = 'duedate';
    public const VAR_LABELS = 'labels';
    public const VAR_SUMMARY = 'summary';

    /**
     * ID of the service desk in which to create the request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @var integer
     */
    public $serviceDeskId;

    /**
     * ID of the request type for the request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @var integer
     */
    public $requestTypeId;

    /**
     * JSON map of Jira field IDs and their values representing the content of the request.
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @var array
     */
    public $requestFieldValues;

    /**
     * List of customers to participate in the request, as a list of name or accountId values.
     * Note that name has been deprecated, in favour of accountId (see the migration guide for details).
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @var array
     */
    public $requestParticipants;

    /**
     * The name or accountId of the customer the request is being raised on behalf of.
     * Note that name has been deprecated, in favour of accountId (see the migration guide for details).
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/#api-rest-servicedeskapi-request-post
     *
     * @var string
     */
    public $raiseOnBehalfOf;

    /**
     * Returns the service desk id.
     *
     * @return int
     */
    public function getServiceDeskId() : int
    {
        return $this->serviceDeskId;
    }

    /**
     * Setter for the service desk id.
     *
     * @param int $serviceDeskId
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setServiceDeskId(int $serviceDeskId) : Request
    {
        $this->serviceDeskId = $serviceDeskId;
        return $this;
    }

    /**
     * Returns the request type id.
     *
     * @return int
     */
    public function getRequestTypeId() : int
    {
        return $this->requestTypeId;
    }

    /**
     * Setter for the request type id.
     *
     * @param int $requestTypeId
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestTypeId(int $requestTypeId) : Request
    {
        $this->requestTypeId = $requestTypeId;
        return $this;
    }

    /**
     * Returns the request field values.
     *
     * @return array
     */
    public function getRequestFieldValues() : array
    {
        return $this->requestFieldValues;
    }

    /**
     * Setter for summary.
     *
     * @param string $summary
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestSummary(string $summary) : Request
    {
        $this->requestFieldValues[self::VAR_SUMMARY] = $summary;
        return $this;
    }

    /**
     * Setter for the request description.
     *
     * @param string $description
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestDescription(string $description) : Request
    {
        $this->requestFieldValues[self::VAR_DESCRIPTION] = $description;
        return $this;
    }

    /**
     * Setter for the request due date. Thr format is: Y-m-d.
     *
     * @param string $due_date date('Y-m-d')
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestDueDate(string $due_date) : Request
    {
        $this->requestFieldValues[self::VAR_DUE_DATE] = $due_date;
        return $this;
    }

    /**
     * Setter for the request labels.
     *
     * @param array $labels
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestLabels(array $labels) : Request
    {
        $this->requestFieldValues[self::VAR_LABELS] = $labels;
        return $this;
    }

    /**
     * Setter for one request label.
     *
     * @param string $label
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function addRequestLabels(string $label) : Request
    {
        $this->requestFieldValues[self::VAR_LABELS][] = $label;
        return $this;
    }

    /**
     * Setter for the request components.
     *
     * @param array $components
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestComponents(array $components) : Request
    {
        $this->requestFieldValues[self::VAR_COMPONENTS] = $components;
        return $this;
    }

    /**
     * Setter for one request component.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function addRequestComponent(string $key, string $value) : Request
    {
        $this->requestFieldValues[self::VAR_COMPONENTS][] = [$key => $value];
        return $this;
    }

    /**
     * Setter for request custom field.
     *
     * @param string $field_key
     * @param mixed  $value
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestCustomField(string $field_key, $value) : Request
    {
        $this->requestFieldValues[$field_key] = $value;
        return $this;
    }

    /**
     * Returns request participants.
     *
     * @return array
     */
    public function getRequestParticipants() : array
    {
        return $this->requestParticipants;
    }

    /**
     * Setter for request participants.
     *
     * @param array $requestParticipants
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRequestParticipants(array $requestParticipants) : Request
    {
        $this->requestParticipants = $requestParticipants;
        return $this;
    }

    /**
     * Setter for one request participant.
     *
     * @param string $requestParticipant
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function addRequestParticipant(string $requestParticipant) : Request
    {
        $this->requestParticipants[] = $requestParticipant;
        return $this;
    }

    /**
     * Returns the raise on behalf of.
     *
     * @return string
     */
    public function getRaiseOnBehalfOf() : string
    {
        return $this->raiseOnBehalfOf;
    }

    /**
     * Setter for the raise on behalf of.
     *
     * @param int $raiseOnBehalfOf
     *
     * @return \Walther\JiraServiceDesk\Domain\Model\Request
     */
    public function setRaiseOnBehalfOf(int $raiseOnBehalfOf) : Request
    {
        $this->raiseOnBehalfOf = $raiseOnBehalfOf;
        return $this;
    }
}
