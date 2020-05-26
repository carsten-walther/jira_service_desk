<?php

namespace Walther\JiraServiceDesk\Reports;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;
use Walther\JiraServiceDesk\Service\Resource\Info;
use Walther\JiraServiceDesk\Service\Service;
use Walther\JiraServiceDesk\Utility\AccessUtility;

/**
 * Provides an status report of the jira service desk.
 *
 * @package Walther\JiraServiceDesk\Reports
 * @author  Carsten Walther
 */
class Report implements StatusProviderInterface
{
    /**
     * Array of reports.
     *
     * @var array
     */
    protected $reports = [];

    /**
     * The ObjectManager object.
     *
     * @var object|\TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * The LanguageService object.
     *
     * @var object|\TYPO3\CMS\Core\Localization\LanguageService
     */
    protected $languageService;

    /**
     * Report constructor initializes the Objectmanager and the LanguageService.
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->languageService = $this->objectManager->get(LanguageService::class);
        $this->languageService->includeLLFile('EXT:jira_service_desk/Resources/Private/Language/locallang_report.xlf');
    }

    /**
     * Returns the status of an extension or (sub)system.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getStatus()
    {
        $this->reports = [];

        $pass = $this->checkExtensionConfiguration();

        if ($pass) {
            $this->checkServicedeskAvailability();
        }

        return $this->reports;
    }

    /**
     * Check the extension configuration and add reports to report array.
     *
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected function checkExtensionConfiguration() : bool
    {
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk'];

        $pass = false;
        if ($extensionConfiguration['serviceDeskUrl']) {
            $pass = true;
        }
        $this->reports[] = GeneralUtility::makeInstance(Status::class,
            $this->languageService->getLL('report.check.serviceDeskUrl.title'),
            $pass ? 'OK' : 'Error',
            $pass ? '' : $this->languageService->getLL('report.check.serviceDeskUrl.description'),
            $pass ? Status::OK : Status::ERROR
        );

        $pass = false;
        if ($extensionConfiguration['serviceDeskId']) {
            $pass = true;
        }
        $this->reports[] = GeneralUtility::makeInstance(Status::class,
            $this->languageService->getLL('report.check.serviceDeskId.title'),
            $pass ? 'OK' : 'Error',
            $pass ? '' : $this->languageService->getLL('report.check.serviceDeskId.description'),
            $pass ? Status::OK : Status::ERROR
        );

        return $pass;
    }

    /**
     * Checks the service desk availability.
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected function checkServicedeskAvailability() : bool
    {
        $pass = false;
        $msg = 'Please check the user credentials!';

        if (AccessUtility::hasAccess()) {
            $service = GeneralUtility::makeInstance(Service::class)->initialize();

            $info = GeneralUtility::makeInstance(Info::class, $service)->getInfo();

            if ($info->status === 200) {
                $pass = true;
                $msg = 'Version: ' . $info->body->version . '<br>Platform: ' . $info->body->platformVersion . '<br>Build date: ' . $info->body->buildDate->friendly;
            }
        }

        $this->reports[] = GeneralUtility::makeInstance(Status::class,
            $this->languageService->getLL('report.check.serviceDeskAvailability.title'),
            $pass ? 'OK' : 'Error',
            $msg ? $msg : $this->languageService->getLL('report.check.serviceDeskAvailability.description'),
            $pass ? Status::OK : Status::ERROR
        );

        return $pass;
    }
}
