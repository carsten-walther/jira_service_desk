<?php
declare(strict_types = 1);

namespace Walther\JiraServiceDesk\Backend\ToolbarItems;

/**
 * Main functionality to render a toolbar at the top bar of the TYPO3 Backend.
 *
 * @package Walther\JiraServiceDesk\Backend\ToolbarItems
 * @author Carsten Walther
 */
class ServiceDeskToolbarItem implements \TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface
{
    /**
     * Checks whether the user has access to this toolbar item.
     *
     * @return bool TRUE if user has access, FALSE if not
     */
    public function checkAccess() : bool
    {
        return \Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess();
    }

    /**
     * Render "item" part of this toolbar.
     *
     * @return string Toolbar item HTML
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function getItem() : string
    {
        if (\Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess()) {
            return $this->getFluidTemplateObject('ToolbarItem.html')->render();
        }

        return '';
    }

    /**
     * TRUE if this toolbar item has a collapsible drop down.
     *
     * @return bool TRUE if there is a drop down, FALSE if not
     */
    public function hasDropDown() : bool
    {
        if (\Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess()) {
            return true;
        }

        return false;
    }

    /**
     * Render "drop down" part of this toolbar.
     *
     * @return string Drop down HTML
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    public function getDropDown() : string
    {
        if (\Walther\JiraServiceDesk\Utility\AccessUtility::hasAccess()) {
            $serviceDeskId = (int)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['jira_service_desk']['serviceDeskId'];

            $service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Service::class)->initialize();
            $serviceDesk = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\ServiceDesk::class, $service);

            $request = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Walther\JiraServiceDesk\Service\Resource\Request::class, $service);
            $customerRequests = $request->getCustomerRequests($serviceDeskId);

            $customerRequestCounts = [];
            if (is_array($customerRequests->body->values)) {
                foreach ($customerRequests->body->values as $key => $customerRequest) {
                    $customerRequestCounts[$customerRequest->currentStatus->status]++;
                }
            }

            $view = $this->getFluidTemplateObject('DropDown.html');
            $view->assignMultiple([
                'serviceDesk' => $serviceDesk->getServiceDeskById($serviceDeskId),
                'customerRequests' => array_sum($customerRequestCounts),
                'customerRequestCounts' => $customerRequestCounts,
            ]);

            return $view->render();
        }

        return '';
    }

    /**
     * Returns an array with additional attributes added to containing <li> tag of the item.
     * Typical usages are additional css classes and data-* attributes, classes may be merged
     * with other classes needed by the framework. Do NOT set an id attribute here.
     *
     * array(
     *     'class' => 'my-class',
     *     'data-foo' => '42',
     * )
     *
     * @return array List item HTML attributes
     */
    public function getAdditionalAttributes() : array
    {
        return [];
    }

    /**
     * Returns an integer between 0 and 100 to determine the position of this item relative to others.
     * By default, extensions should return 50 to be sorted between main core
     * items and other items that should be on the very right.
     *
     * @return int 0 .. 100
     */
    public function getIndex() : int
    {
        return 60;
    }

    /**
     * Called as a hook in \TYPO3\CMS\Backend\Utility\BackendUtility::getUpdateSignalCode,
     * calls a JS function to change the number of opened documents
     *
     * @param $params
     */
    public function updateServiceDeskMenuHook(&$params) : void
    {
        $params['JScode'] = '
            if (top && top.TYPO3.ServiceDeskMenu) {
                top.TYPO3.ServiceDeskMenu.updateMenu();
            }
        ';
    }

    /**
     * Returns a new standalone view, shorthand function.
     *
     * @param string $filename
     *
     * @return \TYPO3\CMS\Fluid\View\StandaloneView The standalone view
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    protected function getFluidTemplateObject(string $filename) : \TYPO3\CMS\Fluid\View\StandaloneView
    {
        $view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
        $view->setLayoutRootPaths(['EXT:jira_service_desk/Resources/Private/Layouts']);
        $view->setPartialRootPaths(['EXT:jira_service_desk/Resources/Private/Partials']);
        $view->setTemplateRootPaths(['EXT:jira_service_desk/Resources/Private/Templates/ToolbarItems']);
        $view->setTemplate($filename);
        $view->getRequest()->setControllerExtensionName('ServiceDesk');

        return $view;
    }
}
