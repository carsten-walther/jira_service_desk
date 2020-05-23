<?php

/**
 * DashboardPresets.php
 */

return [
    'jiraservicedesk' => [
        'title' => 'LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:dashboardPresets.jiraservicedesk.title',
        'description' => 'LLL:EXT:jira_service_desk/Resources/Private/Language/locallang.xlf:dashboardPresets.jiraservicedesk.description',
        'iconIdentifier' => 'apps-toolbar-menu-service-desk',
        'defaultWidgets' => ['jiraServiceDesk_InformationWidget', 'jiraServiceDesk_StatusWidget', 'jiraServiceDesk_TypeGraphWidget', 'jiraServiceDesk_RequestsWidget'],
        'showInWizard' => true
    ],
];
