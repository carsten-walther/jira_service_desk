<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Controller;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This class implements the required Ajax methods.
 *
 * @package Walther\JiraServiceDesk\Controller
 * @author Carsten Walther
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
    protected $serviceDesk;

    /**
     * Request resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Request
     */
    protected $request;

    /**
     * Knowledgebase resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Knowledgebase
     */
    protected $knowledgebase;

    /**
     * AjaxController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        if (\Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess()) {

            $this->service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Service::class);

            if ($this->service->initialize()) {
                $this->serviceDesk = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\ServiceDesk::class, $this->service);
                $this->request = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\Request::class, $this->service);
                $this->knowledgebase = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\Knowledgebase::class, $this->service);
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
    public function dispatch(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
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
                } else if ($requestData['name'] === 'priority') {
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
                $customerRequests = $this->request->getCustomerRequests($serviceDeskId, 0, true);
                $customerRequestCounts = [];
                if (is_array($customerRequests->body->values)) {
                    foreach ($customerRequests->body->values as $key => $customerRequest) {
                        $customerRequestCounts[$customerRequest->currentStatus->status]++;
                    }
                }
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$customerRequestCounts);
                break;




            // subscribe to an issue
            case 'subscribe':
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$this->request->subscribe($formData['issueId']));
                break;

            // unsubscribe to an issue
            case 'unsubscribe':
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$this->request->unsubscribe($formData['issueId']));
                break;




            // this is called by the serviceDesk action on input search strings
            case 'getRequestTypes':
                // @todo We have to check if we can get access to knowledgebase articles, currently we get 404 errors. Maybe because of experimental api?
                $data = $this->serviceDesk->getRequestTypes($serviceDeskId);
                if ($formData['searchTerm'] !== '') {
                    foreach ($data->body->values as $key => $value) {
                        if (stripos(strtolower($value->name), strtolower($formData['searchTerm'])) === false && stripos(strtolower($value->description), strtolower($formData['searchTerm'])) === false) {
                            unset($data->body->values[$key]);
                        }
                    }
                }
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$data);
                break;




            // this is called to perform a request comment
            case 'performRequestComment':
                \TYPO3\CMS\Backend\Utility\BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$this->request->createRequestComment($formData['requestId'], $formData['comment']));
                break;




            // this is called by the serviceDesk action to load the create request form
            case 'getTransitionForm':
                $view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
                $view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jira_service_desk') . 'Resources/Private/Partials/Form/Transition.html');
                $view->assignMultiple([
                    'requestId' => $formData['requestId'],
                    'transitionId' => $formData['transitionId']
                ]);
                return new \TYPO3\CMS\Core\Http\HtmlResponse($view->render());
                break;

            // this is called to perform a customer transition
            case 'performCustomerTransition':
                \TYPO3\CMS\Backend\Utility\BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
                return new \TYPO3\CMS\Core\Http\JsonResponse((array)$this->request->performCustomerTransition($formData['transition[requestId]'], (int)$formData['transition[transitionId]'], $formData['transition[comment]']));
                break;
        }
    }
}
