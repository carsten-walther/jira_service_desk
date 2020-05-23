<?php

/**
 * ext_localconf.php
 */

defined('TYPO3_MODE') or die();

call_user_func(static function($extKey) {

    if (TYPO3_MODE === 'BE') {

        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][$extKey] = \Walther\JiraServiceDesk\Backend\ToolbarItems\ServiceDeskToolbarItem::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['updateSignalHook']['ServiceDeskToolbarItem::updateServiceDeskMenu'] = \Walther\JiraServiceDesk\Backend\ToolbarItems\ServiceDeskToolbarItem::class . '->updateServiceDeskMenuHook';

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'apps-toolbar-menu-service-desk',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/toolbar-icon.svg'
            ]
        );

        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey] = [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
                'options' => [
                    'defaultLifetime' => 900,
                ],
            ];
        }

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dashboard')) {
            // Add module configuration
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
                'module.tx_dashboard.view {
                    templateRootPaths.1221344399 = EXT:' . $extKey . '/Resources/Private/Templates/
                    partialRootPaths.1221344399 = EXT:' . $extKey . '/Resources/Private/Partials/
                    layoutRootPaths.1221344399 = EXT:' . $extKey . '/Resources/Private/Layouts/
                }'
            );
        }
    }

}, 'jira_service_desk');
