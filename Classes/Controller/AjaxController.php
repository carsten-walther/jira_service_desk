<?php

namespace Walther\JiraServiceDesk\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Walther\JiraServiceDesk\Service\Resource\Knowledgebase;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Resource\ServiceDesk;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * This class implements the required Ajax methods.
 *
 * @package Walther\JiraServiceDesk\Controller
 * @author  Carsten Walther
 */
class AjaxController implements SingletonInterface
{
    /**
     * Service object
     *
     * @var \Walther\JiraServiceDesk\Service\Service
     */
    protected $service;
    /**
     * ServiceDesk resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\ServiceDesk
     */
    protected $serviceDeskResource;
    /**
     * Request resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Request
     */
    protected $requestResource;
    /**
     * Knowledgebase resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Knowledgebase
     */
    protected $knowledgebaseResource;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var int
     */
    private $lifeTime = 60 * 60 * 1;

    /**
     * AjaxController constructor.
     *
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface        $cache
     * @param \Walther\JiraServiceDesk\Service\Service                $service
     * @param \Walther\JiraServiceDesk\Service\Resource\ServiceDesk   $serviceDeskResource
     * @param \Walther\JiraServiceDesk\Service\Resource\Request       $requestResource
     * @param \Walther\JiraServiceDesk\Service\Resource\Knowledgebase $knowledgebaseResource
     */
    public function __construct(Cache $cache, Service $service, ServiceDesk $serviceDeskResource, Request $requestResource, Knowledgebase $knowledgebaseResource)
    {
        if (AccessUtility::hasAccess()) {
            $this->cache = $cache;

            $this->service = $service;
            $this->service->initialize();

            $this->serviceDeskResource = $serviceDeskResource;
            $this->serviceDeskResource->setService($this->service);

            $this->requestResource = $requestResource;
            $this->requestResource->setService($this->service);

            $this->knowledgebaseResource = $knowledgebaseResource;
            $this->knowledgebaseResource->setService($this->service);
        }
    }

    /**
     * Dispatches and returns the request results.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function dispatch(ServerRequestInterface $request) : ResponseInterface
    {
        $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

        $parsedBody = $request->getParsedBody();

        $formData = [];
        if (is_array($parsedBody)) {
            foreach ($parsedBody['tx_servicedesk_']['formData'] as $requestData) {
                if ($requestData['name'] === 'components') {
                    $formData[$requestData['name']][] = [
                        'id' => $requestData['value']
                    ];
                } elseif ($requestData['name'] === 'priority') {
                    $formData[$requestData['name']] = [
                        'id' => $requestData['value']
                    ];
                } else {
                    $formData[$requestData['name']] = $requestData['value'];
                }
            }
        }

        switch ($request->getQueryParams()['tx_servicedesk_']['action']) {

            // this is called by the topbar menu and its hook to update the counts
            case 'getServiceDeskMenuData':
                $customerRequests = $this->requestResource->getCustomerRequests($serviceDeskId, 0, true);
                $customerRequestCounts = [];
                if (is_array($customerRequests->body->values)) {
                    foreach ($customerRequests->body->values as $key => $customerRequest) {
                        $customerRequestCounts[$customerRequest->currentStatus->status]++;
                    }
                }
                return new JsonResponse((array)$customerRequestCounts);
                break;


            // subscribe to an issue
            case 'subscribe':
                return new JsonResponse((array)$this->requestResource->subscribe($formData['issueId']));
                break;

            // unsubscribe to an issue
            case 'unsubscribe':
                return new JsonResponse((array)$this->requestResource->unsubscribe($formData['issueId']));
                break;


            // this is called by the serviceDesk action on input search strings
            case 'getRequestTypes':
                // @todo We have to check if we can get access to knowledgebase articles, currently we get 404 errors. Maybe because of experimental api?
                $data = $this->getRequestTypes($serviceDeskId);
                if ($formData['searchTerm'] !== '') {
                    foreach ($data->body->values as $key => $value) {
                        if (stripos(strtolower($value->name), strtolower($formData['searchTerm'])) === false && stripos(strtolower($value->description), strtolower($formData['searchTerm'])) === false) {
                            unset($data->body->values[$key]);
                        }
                    }
                }
                return new JsonResponse((array)$data);
                break;


            // this is called to perform a request comment
            case 'performRequestComment':
                BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
                return new JsonResponse((array)$this->requestResource->createRequestComment($formData['requestId'], $formData['comment']));
                break;


            // this is called by the serviceDesk action to load the create request form
            case 'getTransitionForm':
                $view = GeneralUtility::makeInstance(StandaloneView::class);
                $view->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('jira_service_desk') . 'Resources/Private/Partials/Form/Transition.html');
                $view->assignMultiple([
                    'requestId' => $formData['requestId'],
                    'transitionId' => $formData['transitionId']
                ]);
                return new HtmlResponse($view->render());
                break;

            // this is called to perform a customer transition
            case 'performCustomerTransition':
                BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
                return new JsonResponse((array)$this->requestResource->performCustomerTransition($formData['transition[requestId]'], (int)$formData['transition[transitionId]'], $formData['transition[comment]']));
                break;
        }
    }

    /**
     * @param $serviceDeskId
     * @param $expand
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequestTypes($serviceDeskId, $expand = false)
    {
        $cacheHash = md5('getRequestTypes');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->serviceDeskResource->getRequestTypes($serviceDeskId, $expand);
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->lifeTime);

        return $items;
    }
}
