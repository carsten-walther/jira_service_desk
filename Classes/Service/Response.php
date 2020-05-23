<?php

namespace Walther\JiraServiceDesk\Service;

/**
 * Class Response
 *
 * @package Walther\JiraServiceDesk\Service
 * @author Carsten Walther
 */
class Response
{
    /**
     * The body
     *
     * @var mixed
     */
    public $body;

    /**
     * The message
     *
     * @var mixed
     */
    public $message;

    /**
     * The status
     *
     * @var mixed
     */
    public $status;

    /**
     * Response constructor.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct(\GuzzleHttp\Psr7\Response $response)
    {
        $this->status = $response->getStatusCode();
        $this->message = $response->getReasonPhrase();
        $this->body = json_decode($response->getBody()->getContents(), false);
    }

    /**
     * Return the response body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return the response status.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return the response message.
     *
     * @return null|string
     */
    public function getMessage() :? string
    {
        return $this->message;
    }
}
