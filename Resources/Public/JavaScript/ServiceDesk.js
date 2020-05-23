/**
 * Module: TYPO3/CMS/JiraServiceDesk/ServiceDesk
 */
define([
    'jquery',
    'nprogress',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Severity',
    'TYPO3/CMS/Backend/Viewport',
    'TYPO3/CMS/Backend/Notification'
], function ($, NProgress, Modal, Severity, Viewport, Notification) {

    'use strict';

    /**
     * ServiceDesk
     *
     * @type {{options: {jqxhr: {abort: abort}, url: *}}}
     */
    let ServiceDesk = {
        options: {
            url: TYPO3.settings.ajaxUrls.servicedesk,
            jqxhr: {
                abort: function () {}
            }
        }
    };

    /**
     * initializeEvents
     */
    ServiceDesk.initializeEvents = function () {
        NProgress.configure({
            parent: '.module-loading-indicator',
            showSpinner: false
        });

        $('form[name="searchForm"]').on({
            submit: function (event) {
                event.preventDefault();
            }
        });

        $('input[name="searchTerm"]').on({
            input: function (event) {
                event.preventDefault();
                $('input[name="searchTerm"]').parent().find('button.clearSearchForm').show();
                $('#requestTypesNavigation').fadeOut(150, function () {
                    ServiceDesk.getRequestTypes($('input[name="searchTerm"]').val());
                });
            }
        });

        $('textarea[name="tx_jiraservicedesk_help_jiraservicedeskjira[comment][comment]"]').css('height', 32);
        $('textarea[name="tx_jiraservicedesk_help_jiraservicedeskjira[comment][comment]"]').on({
            input: function (event) {
                event.preventDefault();
                $(this).parent().removeClass('has-error');
            },
            focus: function (event) {
                event.preventDefault();
                $(this).animate({
                    height: 100
                }, 150);
            },
            blur: function (event) {
                event.preventDefault();
                if (!$.trim($(this).val())) {
                    $(this).animate({
                        height: 32
                    }, 150);
                }
            }
        });
    };





    /**
     * clearFilter
     */
    ServiceDesk.clearFilter = function () {
        $('#requestTypesResultList').fadeOut(150, function () {
            $(this).find('ul').empty();
            $('button.clearSearchForm').hide();
            $('input[name="searchTerm"]').val('');
            $('#requestTypesNavigation').fadeIn(150);
        });
    };





    /**
     * getRequestTypes
     *
     * @param searchTerm
     */
    ServiceDesk.getRequestTypes = function (searchTerm) {
        ServiceDesk.options.jqxhr.abort();
        ServiceDesk.options.jqxhr = $.ajax({
            url: ServiceDesk.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'getRequestTypes',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                tx_servicedesk_: {
                    formData: [
                        {
                            name: 'searchTerm',
                            value: searchTerm
                        }
                    ]
                }
            },
            beforeSend: function() {
                NProgress.start();
            },
            success: function(data) {
                if (data.status === 200) {
                    $.each(data.body.values, function (index, value) {

                        console.log(value);

                        let html =
                            '<li class="media">' +
                                '<a href="#">' +
                                    '<div class="media-left">' +
                                        '<img src="' + value.icon._links.iconUrls['48x48'] + '" alt="" width="42" height="42" />' +
                                    '</div>' +
                                    '<div class="media-body">' +
                                        '<h4 class="media-heading">' + value.name + '</h4>' +
                                        '<p>' + value.description + '</p>' +
                                        '<p><small>' + value.helpText + '</small></p>' +
                                    '</div>' +
                                '</a>' +
                            '</li>';
                        $('#requestTypesResultList').fadeIn().find('ul').append(html);
                    });
                }
            },
            complete: function() {
                NProgress.done();
            }
        });
    };





    ServiceDesk.subscribe = function (issueId, issueKey) {
        ServiceDesk.options.jqxhr.abort();
        ServiceDesk.options.jqxhr = $.ajax({
            url: ServiceDesk.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'subscribe',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                tx_servicedesk_: {
                    formData: [
                        {
                            name: 'issueId',
                            value: issueId
                        },
                        {
                            name: 'issueKey',
                            value: issueKey
                        }
                    ]
                }
            },
            beforeSend: function() {
                NProgress.start();
            },
            success: function(data) {

                console.log(data);

                if (data.status === 204) {
                    Notification.success(TYPO3.lang['performCustomerTransition.success.title'], TYPO3.lang['performCustomerTransition.success.description']);
                } else {
                    Notification.error(TYPO3.lang['performCustomerTransition.error.title'], TYPO3.lang['performCustomerTransition.error.description']);
                }
            },
            complete: function() {
                NProgress.done();
                ServiceDesk.reload();
            }
        });
    };

    ServiceDesk.unsubscribe = function (issueId, issueKey) {
        ServiceDesk.options.jqxhr.abort();
        ServiceDesk.options.jqxhr = $.ajax({
            url: ServiceDesk.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'unsubscribe',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                tx_servicedesk_: {
                    formData: [
                        {
                            name: 'issueId',
                            value: issueId
                        },
                        {
                            name: 'issueKey',
                            value: issueKey
                        }
                    ]
                }
            },
            beforeSend: function() {
                NProgress.start();
            },
            success: function(data) {

                console.log(data);

                if (data.status === 204) {
                    Notification.success(TYPO3.lang['performCustomerTransition.success.title'], TYPO3.lang['performCustomerTransition.success.description']);
                } else {
                    Notification.error(TYPO3.lang['performCustomerTransition.error.title'], TYPO3.lang['performCustomerTransition.error.description']);
                }
            },
            complete: function() {
                NProgress.done();
                ServiceDesk.reload();
            }
        });
    };





    /**
     * createCustomerTransition
     *
     * @param requestId
     * @param transitionId
     */
    ServiceDesk.createCustomerTransition = function (requestId, transitionId) {
        ServiceDesk.options.jqxhr.abort();
        ServiceDesk.options.jqxhr = $.ajax({
            url: ServiceDesk.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'getTransitionForm',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'POST',
            dataType: 'html',
            cache: false,
            data: {
                tx_servicedesk_: {
                    formData: [
                        {
                            name: 'requestId',
                            value: requestId
                        },
                        {
                            name: 'transitionId',
                            value: transitionId
                        }
                    ]
                }
            },
            beforeSend: function() {
                NProgress.start();
            },
            success: function(data) {
                Modal.advanced({
                    title: TYPO3.lang['modal.performCustomerTransition'] || 'Perform transition',
                    content: $.parseHTML(data),
                    type: Modal.types.default,
                    size: Modal.sizes.default,
                    severity: Severity.notice,
                    buttons: [
                        {
                            text: TYPO3.lang['button.submit'] || 'Submit',
                            btnClass: 'btn-' + Severity.getCssClass(Severity.warning),
                            name: 'submit',
                            trigger: function() {
                                Modal.currentModal.find('form[name="transition"]').each(function() {
                                    let $form = $(this);
                                    let formData = $form.serializeArray();
                                    ServiceDesk.performCustomerTransition(formData);
                                    Modal.currentModal.trigger('modal-dismiss');
                                    /*
                                    $form.find(':input').each(function () {
                                        if ($(this).attr('required')) {
                                            if ($.trim($(this).val())) {
                                                let formData = $form.serializeArray();
                                                ServiceDesk.performCustomerTransition(formData);
                                                Modal.currentModal.trigger('modal-dismiss');
                                            } else {
                                                $(this).parent().addClass('has-error');
                                            }
                                        }
                                    });
                                    */
                                });
                            }
                        },
                        {
                            text: TYPO3.lang['button.cancel'] || 'Cancel',
                            btnClass: 'btn-default',
                            name: 'cancel',
                            active: true,
                            trigger: function() {
                                Modal.currentModal.trigger('modal-dismiss');
                            }
                        }
                    ]
                });
            },
            complete: function() {
                NProgress.done();
            }
        });
    };

    /**
     * performCustomerTransition
     *
     * @param formData
     */
    ServiceDesk.performCustomerTransition = function (formData) {
        $.ajax({
            url: ServiceDesk.options.url + '&' + $.param({
                tx_servicedesk_: {
                    action: 'performCustomerTransition',
                    controller: 'ServiceDesk'
                }
            }),
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                tx_servicedesk_: {
                    formData
                }
            },
            beforeSend: function() {
                NProgress.start();
            },
            success: function(data) {
                if (data.status === 204) {
                    Notification.success(TYPO3.lang['performCustomerTransition.success.title'], TYPO3.lang['performCustomerTransition.success.description']);
                } else {
                    Notification.error(TYPO3.lang['performCustomerTransition.error.title'], TYPO3.lang['performCustomerTransition.error.description']);
                }
            },
            complete: function() {
                NProgress.done();
                ServiceDesk.reload();
            }
        });
    };





    /**
     * reload
     */
    ServiceDesk.reload = function () {
        $('iframe#typo3-contentIframe').attr('src', function() {
            $(this).attr('src', $('.iframe-reload-button').attr('href'));
            this.contentWindow.location.reload(true);
        });
    };





    Viewport.Topbar.Toolbar.registerEvent(function() {
        ServiceDesk.initializeEvents();
    });

    // expose to global
    TYPO3.ServiceDesk = ServiceDesk;

    return ServiceDesk;
});
