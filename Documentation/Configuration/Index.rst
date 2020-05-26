.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _configuration:

Configuration
=============

Target group: **Developers, Integrators**

To use this extension you have to specify the URL of your Jira installation and the ID of the provided service desk in the extension configuration.

The ticket management is handled by a backend user. To use this extension, you have to enter the e-mail address and password registered in Jira in the corresponding backend user.

Typical Configuration
---------------------

Example of TypoScript Configuration:

.. code-block:: typoscript

   # cat=basic//; type=boolean; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.adminAccessOnly
   adminAccessOnly = 0

   # cat=basic//; type=string; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.serviceDeskId
   serviceDeskId = 1

   # cat=basic//; type=string; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.serviceDeskUrl
   serviceDeskUrl = https://path-to-my-jira.tld/

.. _configuration-typoscript:
