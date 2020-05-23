/**
 * Module: TYPO3/CMS/JiraServiceDesk/Toolbar/ServiceDeskMenu
 */
define(['jquery', 'TYPO3/CMS/Backend/Icons', 'TYPO3/CMS/Backend/Viewport'], function($, Icons, Viewport) {

    'use strict';

    /**
     *
     * @type {{options: {containerSelector: string, timer: null, toolbarIconSelector: string, counterSelector: string, counterSelectorInText: string, interval: number, servicedeskModuleLinkSelector: string, url: *}}}
     */
    let ServiceDeskMenu = {
        options: {
            interval: 5,
            containerSelector: '#walther-jiraservicedesk-backend-toolbaritems-servicedesktoolbaritem',
            toolbarIconSelector: '.toolbar-item-icon .t3js-icon',
            counterSelector: '.jira-service-desk-counter',
            counterSelectorInText: '.jira-service-desk-counter-in-text',
            servicedeskModuleLinkSelector: '.jira-service-desk-modulelink',
            url: TYPO3.settings.ajaxUrls.servicedesk,
            timer: null
        }
    };

    /**
     * initializeEvents
     */
    ServiceDeskMenu.initializeEvents = function () {
        $(ServiceDeskMenu.options.containerSelector).on('click', ServiceDeskMenu.options.servicedeskModuleLinkSelector, function(evt) {
            evt.preventDefault();
            top.goToModule($(this).data('module'), '', 'tx_jiraservicedesk_help_jiraservicedeskjira[action]=' + $(this).data('action') + '&' + 'tx_jiraservicedesk_help_jiraservicedeskjira[controller]=' + $(this).data('controller'));
        });
        ServiceDeskMenu.options.timer = setInterval(ServiceDeskMenu.updateMenu, 1000 * 60 * ServiceDeskMenu.options.interval)
    };

    /**
     * updateMenu
     */
    ServiceDeskMenu.updateMenu = function() {
        let $toolbarItemIcon = $(ServiceDeskMenu.options.toolbarIconSelector, ServiceDeskMenu.options.containerSelector);
        let $existingIcon = $toolbarItemIcon.clone();

        Icons.getIcon('spinner-circle-light', Icons.sizes.small).done(function(spinner) {
            $toolbarItemIcon.replaceWith(spinner);
        });

        $.ajax({
            url: ServiceDeskMenu.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'getServiceDeskMenuData',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function(data) {
                console.log(data);
                ServiceDeskMenu.updateNumberOfRequests(data);
                $(ServiceDeskMenu.options.toolbarIconSelector, ServiceDeskMenu.options.containerSelector).replaceWith($existingIcon);
            },
            error: function (data) {
                $(ServiceDeskMenu.options.toolbarIconSelector, ServiceDeskMenu.options.containerSelector).replaceWith($existingIcon);
            }
        })
    };

    /**
     * updateNumberOfRequests
     *
     * @param data
     */
    ServiceDeskMenu.updateNumberOfRequests = function(data) {
        let entries = Object.entries(data);

        let count = 0;
        let countWithoutDone = 0;

        for (const [key, sum] of entries) {
            if (key !== 'Fertig') {
                countWithoutDone += sum;
            }
            count += sum;
        }

        $(ServiceDeskMenu.options.counterSelectorInText).text(count ? count : 'no');
        $(ServiceDeskMenu.options.counterSelector).text(countWithoutDone).toggle(countWithoutDone > 0);
    };

    /**
     * toggleMenu
     */
    ServiceDeskMenu.toggleMenu = function() {
        $('.scaffold').removeClass('scaffold-toolbar-expanded');
        $(ServiceDeskMenu.options.containerSelector).toggleClass('open');
    };

    /**
     * registerEvent
     */
    Viewport.Topbar.Toolbar.registerEvent(function() {
        ServiceDeskMenu.initializeEvents();
        ServiceDeskMenu.updateMenu();
    });

    // expose to global
    TYPO3.ServiceDeskMenu = ServiceDeskMenu;

    return ServiceDeskMenu;
});
