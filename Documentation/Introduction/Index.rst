.. include:: ../Includes.txt


.. _introduction:

============
Introduction
============


.. tip::

   New to reStructuredText and Sphinx?

   Get an introduction:
   https://docs.typo3.org/m/typo3/docs-how-to-document/master/en-us/WritingReST/Index.html

   Use this cheat sheet as reference:
   https://docs.typo3.org/m/typo3/docs-how-to-document/master/en-us/WritingReST/CheatSheet.html

.. _what-it-does:

What does it do?
================

To use this extension you have to specify the URL of your Jira installation and the ID of the provided service desk in the extension configuration.

The ticket management is handled by a backend user. To use this extension, you have to enter the e-mail address and password registered in Jira in the corresponding backend user.

.. important::

   Don't forget to repeat your extension's version number in the
   :file:`Settings.cfg` file, in the :code:`release` property. It will be
   automatically picked up on the cover page by the :code:`|release|`
   substitution.

.. _dashboard:

Dashboard
=========

The dashboard, introduced with TYPO3 version 10, supports 4 widgets:

* Information (general information of the Service Desk)
* Status (a graphical overview of open vs. closed issues)
* Type (a graphical overview of all ticket types)
* Requests (a list of recent user requests)

.. _screenshots:

Screenshots
===========

This chapter should help people understand how the extension works. Remove it
if it is not relevant.

.. figure:: ../Images/IntroductionPackage.png
   :class: with-shadow
   :alt: Introduction Package
   :width: 300px

   Introduction Package just after installation (caption of the image)

How the Frontend of the Introduction Package looks like just after installation (legend of the image)
