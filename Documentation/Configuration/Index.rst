.. include:: ../Includes.txt


.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

To use this extension you have to specify the URL of your Jira installation and the ID of the provided service desk in the extension configuration.

The ticket management is handled by a backend user. To use this extension, you have to enter the e-mail address and password registered in Jira in the corresponding backend user.

Typical Example
===============

- Do we need to include a static template?
- For example add a code snippet with comments

Minimal example of TypoScript:

- Code-blocks have support for syntax highlighting
- Use any supported language

.. code-block:: typoscript

   # cat=basic//; type=boolean; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.adminAccessOnly
   adminAccessOnly = 0

   # cat=basic//; type=string; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.serviceDeskId
   serviceDeskId = 1

   # cat=basic//; type=string; label=LLL:EXT:jira_service_desk/Resources/Private/Language/locallang_config.xlf:config.basic.serviceDeskUrl
   serviceDeskUrl = https://path-to-my-jira.tld/

.. _configuration-typoscript:

TypoScript Reference
====================

When detailing data types or standard TypoScript
features, don't hesitate to cross-link to the TypoScript
Reference.

Information about how to use cross-references:
https://docs.typo3.org/typo3cms/HowToDocument/WritingReST/Hyperlinks.html

See the :file:`Settings.cfg` file for the declaration of cross-linking keys.
You can add more keys besides tsref.
