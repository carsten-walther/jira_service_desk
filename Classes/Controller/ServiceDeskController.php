<?php

namespace Walther\JiraServiceDesk\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as Cache;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use Walther\JiraServiceDesk\Domain\Model\Attachment;
use Walther\JiraServiceDesk\Service\Resource\Request;
use Walther\JiraServiceDesk\Service\Resource\Requesttype;
use Walther\JiraServiceDesk\Service\Resource\ServiceDesk;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Main functionality to render a TYPO3 Backend Module.
 *
 * @package Walther\JiraServiceDesk\Controller
 * @author  Carsten Walther
 */
class ServiceDeskController extends ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var \TYPO3\CMS\Backend\View\BackendTemplateView
     */
    protected $view;

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
     * request resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Request
     */
    protected $requestResource;

    /**
     * request resource object
     *
     * @var \Walther\JiraServiceDesk\Service\Resource\Requesttype
     */
    protected $requesttypeResource;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $lifeTime = 60 * 60 * 1;

    /**
     * ServiceDeskController constructor.
     *
     * @param \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

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
    public function injectServiceDesk(ServiceDesk $serviceDeskResource) : void
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
     * @param \Walther\JiraServiceDesk\Service\Resource\Requesttype $requesttypeResource
     */
    public function injectRequesttypeResource(Requesttype $requesttypeResource) : void
    {
        $this->requesttypeResource = $requesttypeResource;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view) : void
    {
        parent::initializeView($view);

        $allowedActionMethods = ['indexAction', 'listAction', 'showAction', 'newAction', 'createAction', 'helpAction', 'accessDeniedAction'];

        if (in_array($this->request->getArguments()['action'], $allowedActionMethods, false)) {
            $this->view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
        }

        if ($this->view instanceof BackendTemplateView) {
            $this->view->getModuleTemplate()->getPageRenderer()->addInlineLanguageLabelFile('EXT:jira_service_desk/Resources/Private/Language/locallang.xlf');
            $this->view->getModuleTemplate()->getPageRenderer()->addCssFile('EXT:jira_service_desk/Resources/Public/Css/backend.css');
            $this->view->getModuleTemplate()->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/JiraServiceDesk/Servicedesk');
        }

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

            if (!$this->requesttypeResource) {
                $this->requesttypeResource = GeneralUtility::makeInstance(Requesttype::class, $this->service);
            } else {
                $this->requesttypeResource->setService($this->service);
            }
        }
    }

    /**
     * This is the main service desk function an represents the service desk form.
     * With its search it is possible to search help topics in the linked helpdesk/confluence area.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function indexAction() : void
    {
        $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

        $requestTypeGroups = $this->getRequestTypeGroup($serviceDeskId);
        $requestTypes = $this->getRequestTypes($serviceDeskId, true);
        $serviceDesk = $this->getServiceDeskById($serviceDeskId);

        foreach ($requestTypeGroups->body->values as $requestTypeGroupKey => $requestTypeGroup) {
            foreach ($requestTypes->body->values as $requestType) {
                foreach ($requestType->groupIds as $groupId) {
                    if ((int)$groupId === (int)$requestTypeGroup->id) {
                        $requestTypeGroup->requestTypes[] = $requestType;
                    }
                    $requestTypeGroups->body->values[$requestTypeGroupKey] = $requestTypeGroup;
                }
            }
        }

        $this->view->assignMultiple([
            'serviceDesk' => $serviceDesk,
            'requestTypeGroups' => $requestTypeGroups,
            'hasAccess' => AccessUtility::hasAccess()
        ]);
    }

    /**
     * Lists all customer requests.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listAction() : void
    {
        $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

        $arguments = $this->request->getArguments();

        $filter = $arguments['filter'];
        $requestStatus = $filter['requestStatus'] ?? 'ALL_REQUESTS';
        $requestOwnership = $filter['requestOwnership'] ?? '';
        $requestTypeId = $filter['requestTypeId'] ?? 0;
        $searchTerm = $filter['searchTerm'] ?? '';
        $approvalStatus = $filter['approvalStatus'] ?? '';

        $page = $arguments['@widget_0']['page'] ?? 0;

        $limit = 10;

        $customerRequests = $this->requestResource->getCustomerRequests($serviceDeskId, (int)$requestTypeId, true, $searchTerm, $requestOwnership, $requestStatus, $approvalStatus, '', (int)$page, $limit);
        $requestTypes = $this->getRequestTypes($serviceDeskId, true);

        $this->view->assignMultiple([
            'page' => $page,
            'limit' => $limit,
            'searchTerm' => $searchTerm,
            'requestOwnership' => $requestOwnership,
            'requestStatus' => $requestStatus,
            'requestTypes' => $requestTypes,
            'requestTypeId' => $requestTypeId,
            'customerRequests' => $customerRequests,
            'hasAccess' => AccessUtility::hasAccess()
        ]);
    }

    /**
     * Show the details of a customer requests.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showAction() : void
    {
        $arguments = $this->request->getArguments();

        $comments = $this->requestResource->getRequestComments($arguments['issueId'], true, false);
        $sortedActivities = [];
        foreach ($comments->body->values as $comment) {
            // todo fetch attachments, currently it returns error 404
            // $attachments = $this->requestResource->getCommentAttachments($arguments['issueKey'], (int)$comment->id);
            $sortedActivities[$comment->created->epochMillis] = $comment;
        }

        $customerRequest = $this->requestResource->getCustomerRequestByIdOrKey($arguments['issueId'], true);
        foreach ($customerRequest->body->status->values as $state) {
            $sortedActivities[$state->statusDate->epochMillis] = $state;
        }
        krsort($sortedActivities);

        $customerRequest->body->status->values = $sortedActivities;

        $transitions = $this->requestResource->getCustomerTransitions($arguments['issueId']);

        $this->view->assignMultiple([
            'customerRequest' => $customerRequest,
            'transitions' => $transitions,
            'hasAccess' => AccessUtility::hasAccess()
        ]);
    }

    /**
     * Add comment fire signal slot and redirect back to show action.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @todo Implement attachment upload
     *
     */
    public function addCommentAction() : void
    {
        $arguments = $this->request->getArguments();

        $result = $this->requestResource->createRequestComment($arguments['comment']['requestId'], $arguments['comment']['comment']);

        if ($result->getStatus() === 201) {
            BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createRequestComment.success.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createRequestComment.success.title'),
                FlashMessage::OK, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
        } else {
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createRequestComment.error.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createRequestComment.error.title'),
                FlashMessage::ERROR, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
        }

        $this->redirect('show', 'ServiceDesk', null, [
            'issueId' => $arguments['comment']['requestId']
        ]);
    }

    /**
     * Perform a transition to a customer request, fire signal slot and redirect back to show action.
     *
     * @todo Don't know if it is a good idea to make this here. Currently transitions will be submitted by ajax call in AjaxController.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function addTransitionAction() : void
    {
        $arguments = $this->request->getArguments();

        $result = $this->requestResource->performCustomerTransition($arguments['transition']['requestId'], (int)$arguments['transition']['transitionId'], $arguments['transition']['comment']);

        if ($result->getStatus() === 204) {
            BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:performCustomerTransition.success.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:performCustomerTransition.success.title'),
                FlashMessage::OK, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
        } else {
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:performCustomerTransition.error.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:performCustomerTransition.error.title'),
                FlashMessage::ERROR, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
        }

        $this->redirect('show', 'ServiceDesk', null, [
            'issueId' => $arguments['transition']['requestId']
        ]);
    }

    /**
     * Shows the form for new customer requests.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newAction() : void
    {
        $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

        $arguments = $this->request->getArguments();

        $requestTypes = $this->getRequestTypes($serviceDeskId, true);
        $serviceDesk = $this->getServiceDeskById($serviceDeskId);

        $requestType = [];
        foreach ($requestTypes->body->values as $value) {
            if ($value->id === $arguments['requestTypeId']) {
                $requestType = $value;
            }
        }

        $requestTypeFields = $this->serviceDeskResource->getRequestTypeFields($serviceDeskId, (int)$arguments['requestTypeId']);

        $this->view->assignMultiple([
            'serviceDesk' => $serviceDesk,
            'requestType' => $requestType,
            'requestTypeFields' => $requestTypeFields,
            'newCustomerRequest' => $arguments['newCustomerRequest'],
            'hasAccess' => AccessUtility::hasAccess()
        ]);
    }

    /**
     * Creates a new customer request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function createAction() : void
    {
        $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

        $arguments = $this->request->getArguments();

        $formData = [];
        foreach ($arguments['newRequest'] as $key => $value) {
            if (!empty($value)) {
                if ($key === 'components') {
                    foreach ($value as $component) {
                        $formData[$key][] = [
                            'id' => $component
                        ];
                    }
                } elseif ($key === 'priority') {
                    $formData[$key] = [
                        'id' => $value
                    ];
                } else {
                    $formData[$key] = $value;
                }
            }
        }

        $newAttachment = null;

        // if we have attachments, upload and attach them to the request
        if ($formData['attachment'] && is_array($formData['attachment']) && !empty($formData['attachment'])) {

            $files = [];
            foreach ($formData['attachment'] as $attachment) {
                $files[] = [
                    'name' => $attachment['name'],
                    'url' => GeneralUtility::upload_to_tempfile($attachment['tmp_name'])
                ];
            }

            $temporaryAttachments = $this->serviceDeskResource->attachTemporaryFile($serviceDeskId, $files);

            if ($temporaryAttachments->getStatus() === 201) {
                $newAttachment = GeneralUtility::makeInstance(Attachment::class);
                $newAttachment->setPublic(true);
                foreach ($temporaryAttachments->getBody()->temporaryAttachments as $temporaryAttachment) {
                    $newAttachment->addTemporaryAttachmentId($temporaryAttachment->temporaryAttachmentId);
                }
            }
        }

        $newRequest = GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Domain\Model\Request::class);
        $newRequest->setServiceDeskId($serviceDeskId);
        $newRequest->setRequestTypeId((int)$arguments['requestType']['id']);

        $allowedFields = ['summary', 'components', 'priority', 'description', 'duedate'];
        foreach ($formData as $key => $value) {
            if ($value && in_array($key, $allowedFields, true)) {
                $newRequest->setRequestCustomField($key, $value);
            }
        }

        $result = $this->requestResource->createCustomerRequest($newRequest);

        if ($result->getStatus() === 201) {

            // if we have attachments, attach them to the request
            if ($newAttachment) {
                $issueKey = $result->getBody()->issueKey;
                $result2 = $this->requestResource->createAttachment($issueKey, $newAttachment);

                if ($result2->getStatus() !== 201) {
                    $message = GeneralUtility::makeInstance(
                        FlashMessage::class,
                        $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createAttachment.error.description'),
                        $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createAttachment.error.title'),
                        FlashMessage::ERROR, true
                    );
                    $service = GeneralUtility::makeInstance(FlashMessageService::class);
                    $queue = $service->getMessageQueueByIdentifier();
                    $queue->addMessage($message);
                    $this->redirect('new', 'ServiceDesk', null, $arguments);
                }
            }

            BackendUtility::setUpdateSignal('ServiceDeskToolbarItem::updateServiceDeskMenu');
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createCustomerRequest.success.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createCustomerRequest.success.title'),
                FlashMessage::OK, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
            $this->redirect('show', 'ServiceDesk', null, [
                'requestId' => $result->body->issueId
            ]);
        } else {
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createCustomerRequest.error.description'),
                $this->getLanguageService()->sL('LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:createCustomerRequest.error.title'),
                FlashMessage::ERROR, true
            );
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $queue = $service->getMessageQueueByIdentifier();
            $queue->addMessage($message);
            $this->redirect('index', 'ServiceDesk');
        }
    }

    /**
     * Show the help.
     *
     * @return void
     */
    public function helpAction() : void
    {

    }

    /**
     * Shows the access denied message.
     *
     * @return void
     */
    public function accessDeniedAction() : void
    {

    }

    /**
     * @param $serviceDeskId
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequestTypeGroup($serviceDeskId)
    {
        $cacheHash = md5('getRequestTypeGroup');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->serviceDeskResource->getRequestTypeGroup($serviceDeskId);
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->lifeTime);

        return $items;
    }

    /**
     * @param $serviceDeskId
     * @param $expand
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getRequestTypes($serviceDeskId, $expand)
    {
        $cacheHash = md5('getRequestTypes');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->serviceDeskResource->getRequestTypes($serviceDeskId, $expand);
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->lifeTime);

        return $items;
    }

    /**
     * @param $serviceDeskId
     *
     * @return mixed|\Walther\JiraServiceDesk\Service\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getServiceDeskById($serviceDeskId)
    {
        $cacheHash = md5('getServiceDeskById');

        if ($items = $this->cache->get($cacheHash)) {
            return $items;
        }

        $items = $this->serviceDeskResource->getServiceDeskById($serviceDeskId);
        $this->cache->set($cacheHash, $items, ['jira_service_desk'], $this->lifeTime);

        return $items;
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    private function getLanguageService() : LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
