<?php

/**
 * AjaxRoutes.php
 */

return [
    'servicedesk' => [
        'path' => '/help/JiraServiceDesk',
        'target' => \Walther\JiraServiceDesk\Controller\AjaxController::class . '::dispatch'
    ]
];
