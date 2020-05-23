<?php

/**
 * ext_emconf.php
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Jira Service Desk',
    'description' => 'Get support from Jira Service Desk.',
    'category' => 'misc',
    'version' => '9.5.1',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Carsten Walther',
    'author_email' => 'walther.carsten@web.de',
    'author_company' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
