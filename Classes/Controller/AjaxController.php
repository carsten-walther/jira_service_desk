<?php

namespace Walther\JiraServiceDesk\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;
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
class AjaxController
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
     * @param \Walther\JiraServiceDesk\Service\Service $service
     */
    public function injectService(Service $service) : void
    {
        $this->service = $service;
    }

    /**
     * @param \Walther\JiraServiceDesk\Service\Resource\ServiceDesk $serviceDeskResource
     */
    public function injectServiceDeskResource(ServiceDesk $serviceDeskResource) : void
    {
        $this->serviceDeskResource = $serviceDeskResource;
    }

    /**
     * @param \Walther\JiraServiceDesk\Service\Resource\Request $requestResource
     */
    public function injectRequestResource(Request $requestResource) : void
    {
        $this->requestResource = $requestResource;
    }

    /**
     * @param \Walther\JiraServiceDesk\Service\Resource\Knowledgebase $knowledgebase
     */
    public function injectKnowledgebaseResource(\Walther\JiraServiceDesk\Service\Resource\Knowledgebase $knowledgebase) : void
    {
        $this->knowledgebaseResource = $knowledgebase;
    }

    /**
     * AjaxController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        if (AccessUtility::hasAccess()) {

            $this->service = !$this->service ? GeneralUtility::makeInstance(Service::class) : $this->service;

            if ($this->service->initialize()) {

                if (!$this->serviceDeskResource) {
                    $this->serviceDeskResource = GeneralUtility::makeInstance(ServiceDesk::class, $this->service);
                } else {
                    $this->serviceDeskResource->setService($this->service);
                }

                if (!$this->requestResource) {
                    $this->requestResource = GeneralUtility::makeInstance(Request::class, $this->service);
                } else {
                    $this->requestResource->setService($this->service);
                }

                if (!$this->knowledgebaseResource) {
                    $this->knowledgebaseResource = GeneralUtility::makeInstance(Knowledgebase::class, $this->service);
                } else {
                    $this->knowledgebaseResource->setService($this->service);
                }
            }
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
                $data = $this->serviceDeskResource->getRequestTypes($serviceDeskId);
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
}
