<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Reports;

/**
 * Provides an status report of the jira service desk.
 *
 * @package Walther\JiraServiceDesk\Reports
 * @author Carsten Walther
 */
class Report implements \TYPO3\CMS\Reports\StatusProviderInterface
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
     */
    public function __construct()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        $this->languageService = $this->objectManager->get(\TYPO3\CMS\Core\Localization\LanguageService::class);
        $this->languageService->includeLLFile('EXT:jira_service_desk/Resources/Private/Language/locallang_report.xlf');
    }

    /**
     * Returns the status of an extension or (sub)system.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     */
    protected function checkExtensionConfiguration() : bool
    {
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk'];

        $pass = false;
        if ($extensionConfiguration['serviceDeskUrl']) {
            $pass = true;
        }
        $this->reports[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Reports\Status::class,
            $this->languageService->getLL('report.check.serviceDeskUrl.title'),
            $pass ? 'OK' : 'Error',
            $pass ? '' : $this->languageService->getLL('report.check.serviceDeskUrl.description'),
            $pass ? \TYPO3\CMS\Reports\Status::OK : \TYPO3\CMS\Reports\Status::ERROR
        );

        $pass = false;
        if ($extensionConfiguration['serviceDeskId']) {
            $pass = true;
        }
        $this->reports[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Reports\Status::class,
            $this->languageService->getLL('report.check.serviceDeskId.title'),
            $pass ? 'OK' : 'Error',
            $pass ? '' : $this->languageService->getLL('report.check.serviceDeskId.description'),
            $pass ? \TYPO3\CMS\Reports\Status::OK : \TYPO3\CMS\Reports\Status::ERROR
        );

        return $pass;
    }

    /**
     * Checks the service desk availability.
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function checkServicedeskAvailability() : bool
    {
        $service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Service::class)->initialize();

        $info = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\Info::class, $service)->getInfo();

        $pass = false;
        $msg = '';

        if ($info->status === 200) {
            $pass = true;
            $msg = 'Version: ' . $info->body->version . '<br>Platform: ' . $info->body->platformVersion . '<br>Build date: ' . $info->body->buildDate->friendly;
        }
        $this->reports[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Reports\Status::class,
            $this->languageService->getLL('report.check.serviceDeskAvailability.title'),
            $pass ? 'OK' : 'Error',
            $pass ? $msg : $this->languageService->getLL('report.check.serviceDeskAvailability.description'),
            $pass ? \TYPO3\CMS\Reports\Status::OK : \TYPO3\CMS\Reports\Status::ERROR
        );

        return $pass;
    }
}
