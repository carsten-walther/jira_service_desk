<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Service;

/**
 * Class Service
 *
 * @package Walther\JiraServiceDesk\Service
 * @author Carsten Walther
 */
class Service
{
    /**
     * METHOD_URL
     */
    public const METHOD_URL = 'rest/servicedeskapi/';

    /**
     * REQUEST_METHOD_GET
     */
    const REQUEST_METHOD_GET = 'GET';

    /**
     * REQUEST_METHOD_POST
     */
    const REQUEST_METHOD_POST = 'POST';

    /**
     * REQUEST_METHOD_PUT
     */
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * REQUEST_METHOD_DELETE
     */
    const REQUEST_METHOD_DELETE = 'DELETE';

    /**
     * The username.
     *
     * @var string
     */
    protected $username;

    /**
     * The password.
     *
     * @var string
     */
    protected $password;

    /**
     * The request type.
     *
     * @var string
     */
    protected $type;

    /**
     * The host name.
     *
     * @var string
     */
    protected $host;

    /**
     * The url path.
     *
     * @var string
     */
    protected $url;

    /**
     * The get params.
     *
     * @var
     */
    protected $getParams;

    /**
     * The request language.
     *
     * @var string
     */
    protected $requestLanguage;

    /**
     * The client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * The options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Service constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'http_errors' => false
        ]);
    }

    /**
     * Returns the username.
     *
     * @return string
     */
    protected function getUsername() : string
    {
        return \Walther\JiraServiceDesk\Utility\AccessUtility::getBackendUser()->user['serviceDeskUsername'];
    }

    /**
     * Returns the password.
     *
     * @return string
     */
    protected function getPassword() : string
    {
        return \Walther\JiraServiceDesk\Utility\AccessUtility::getBackendUser()->user['serviceDeskPassword'];
    }

    /**
     * initialize
     *
     * @return $this|bool
     */
    public function initialize()
    {
        if (\Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess()) {

            $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk'];

            $this->setHost($extensionConfiguration['serviceDeskUrl']);
            $this->setPassword($this->getPassword());
            $this->setUsername($this->getUsername());
            $this->setHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json']);

            return $this;
        }

        return false;
    }

    /**
     * Setter for host.
     *
     * @param string $host
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setHost(string $host) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Setter for username.
     *
     * @param $username
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setUsername($username) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Setter for password.
     *
     * @param $password
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setPassword($password) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Setter for request type.
     *
     * @param $type
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setType($type) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Setter for url path.
     *
     * @param $url
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setUrl($url) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->url = $this->host . self::METHOD_URL . $url;
        return $this;
    }

    /**
     * Setter for get params.
     *
     * @param array $data
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setGetParams(array $data) : \Walther\JiraServiceDesk\Service\Service
    {
        $data = array_filter($data, static function ($value) {
            return ($value !== NULL && $value !== FALSE && $value !== '');
        }, ARRAY_FILTER_USE_BOTH);

        $expand = '';
        if (is_array($data['expand'])) {
            $expand = '&expand=' . implode('&expand=', $data['expand']);
            unset($data['expand']);
        }

        if ($this->requestLanguage) {
            $data['requestLanguage'] = $this->requestLanguage;
        }

        $this->getParams = '?' . http_build_query($data) . $expand;
        return $this;
    }

    /**
     * Setter for request language.
     *
     * @param string $requestLanguage
     *
     * @return $this
     */
    public function setRequestLanguage(string $requestLanguage) : self
    {
        $this->requestLanguage = $requestLanguage;
        return $this;
    }

    /**
     * Setter for post data.
     *
     * @param $post_data
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setPostData($post_data) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->options['json'] = $post_data;
        return $this;
    }

    /**
     * Setter for header.
     *
     * @param $headers
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setHeaders($headers) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->options['headers'] = $headers;
        return $this;
    }

    /**
     * Setter for multipart forms.
     *
     * @param $multipart
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setMultipart($multipart) : \Walther\JiraServiceDesk\Service\Service
    {
        $this->options['multipart'] = $multipart;
        return $this;
    }

    /**
     * Setter for experimental API.
     *
     * @return \Walther\JiraServiceDesk\Service\Service
     */
    public function setExperimentalApi() : \Walther\JiraServiceDesk\Service\Service
    {
        $this->options['headers']['X-ExperimentalApi'] = 'opt-in';
        return $this;
    }

    /**
     * Returns the request response.
     *
     * @return \Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request() : \Walther\JiraServiceDesk\Service\Response
    {
        if ($this->username && $this->password) {
            $this->options['auth'] = [$this->username, $this->password];
        }
        return new \Walther\JiraServiceDesk\Service\Response($this->client->request($this->type, $this->url . $this->getParams, $this->options));
    }
}
