<?php

/**
 * ext_tables.php
 */

defined('TYPO3_MODE') or die();

call_user_func(static function($extKey) {

    if (TYPO3_MODE === 'BE') {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Walther.JiraServiceDesk',
            'help',
            'jira',
            'top',
            [
                'ServiceDesk' => 'index,list,show,addComment,addTransition,new,create,help,accessDenied'
            ],
            [
                'access' => 'user,group,admin',
                'icon' => 'EXT:' . $extKey . '/Resources/Public/Icons/extension.svg',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_module.xlf'
            ]
        );

        // register report for the TYPO3 report module
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['Jira Service Desk'][] = \Walther\JiraServiceDesk\Reports\Report::class;
    }

}, 'jira_service_desk');
