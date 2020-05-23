<?php

/**
 * be_users.php
 */

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    $table = 'be_users',
    $columnArray = [
        'serviceDeskUsername' => [
            'exclude' => TRUE,
            'label' => 'LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_db.xlf:be_users.field.serviceDeskUsername',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim,required',
                'max' => 255,
                'softref' => 'email[subst]'
            ]
        ],
        'serviceDeskPassword' => [
            'exclude' => TRUE,
            'label' => 'LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_db.xlf:be_users.field.serviceDeskPassword',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 100,
                'eval' => 'trim,required,password',
                'autocomplete' => false,
            ]
        ]
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    $table = 'be_users',
    $newFieldsString = '--div--;LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_db.xlf:be_users.tab, serviceDeskUsername, serviceDeskPassword',
    $typeList = '',
    $position = ''
);
