.. ---------------------------------------------------------------
   This is the start file. It gets displayed as first page
   https://docs.typo3.org/m/typo3/docs-how-to-document/master/en-us/GeneralConventions/DirectoryFilenames.html#supported-filenames-and-formats
   ---------------------------------------------------------------

.. ---------------------------------------------------------------
   More information about creating an extension manual:
   https://docs.typo3.org/m/typo3/docs-how-to-document/master/en-us/WritingDocForExtension/CreateWithExtensionBuilder.html
   ---------------------------------------------------------------

.. ---------------------------------------------------------------
   comments start with 2 dots and a blank
   they can continue on the next line
   ---------------------------------------------------------------

.. ---------------------------------------------------------------
   every .rst file should include Includes.txt
   use correct path!
   ---------------------------------------------------------------

.. include:: Includes.txt

.. ---------------------------------------------------------------
   Every manual should have a start label for cross-referencing to
   start page. Do not remove this!
   ---------------------------------------------------------------

.. _start:

.. ---------------------------------------------------------------
   This is the doctitle
   ---------------------------------------------------------------

=============================================================
Jira Service Desk
=============================================================

:Extension Key:
    jira_service_desk

:Version:
    |release|

:Language:
    en

:Copyright:
    2020

:Author:
    Carsten Walther

:Email:
    walther.carsten@web.de

:License:
    This document is published under the Open Content License available from http://www.opencontent.org/opl.shtml

:Rendered:
   |today|

**TYPO3**

   The content of this document is related to TYPO3 CMS, a GNU/GPL CMS/Framework available from `typo3.org <https://typo3.org/>`_ .

**Community Documentation:**

    This documentation is community documentation for the TYPO3 extension Jira Service Desk Extension

    It is maintained as part of this third party extension.

    If you find an error or something is missing, please:
    `Report a Problem <https://github.com/carsten-walther/jira_service_desk/issues/new>`__

**Sitemap:**

   :ref:`sitemap`

.. ---------------------------------------------------------------
   This generates the menu
   https://docs.typo3.org/m/typo3/docs-how-to-document/master/en-us/WritingReST/MenuHierarchy.html
   ---------------------------------------------------------------

.. toctree::
   :maxdepth: 3
   :hidden:

   Introduction/Index
   User/Index
   Installation/Index
   Configuration/Index
   Developer/Index
   KnownProblems/Index
   ToDoList/Index
   ChangeLog/Index
   Sitemap
