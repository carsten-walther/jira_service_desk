<?php

/**
 * ext_localconf.php
 */

defined('TYPO3_MODE') or die();

call_user_func(function($extKey) {

    if (TYPO3_MODE === 'BE') {

        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][$extKey] = \Walther\JiraServiceDesk\Backend\ToolbarItems\ServiceDeskToolbarItem::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['updateSignalHook']['ServiceDeskToolbarItem::updateServiceDeskMenu'] = \Walther\JiraServiceDesk\Backend\ToolbarItems\ServiceDeskToolbarItem::class . '->updateServiceDeskMenuHook';

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'apps-toolbar-menu-service-desk',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:jira_service_desk/Resources/Public/Icons/toolbar-icon.svg'
            ]
        );
    }

}, 'jira_service_desk');
