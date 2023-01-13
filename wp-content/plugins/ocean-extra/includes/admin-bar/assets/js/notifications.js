/**
 * Ocean Admin Notifications
 */

'use strict';

jQuery(document).ready(function ($) {

    var OceanNotifications = {

        active_blocking_process: false,

        elements: {
            $notifications: $('#ocean-notifications'),
            $nextButton: $('#ocean-notifications .navigation .next'),
            $prevButton: $('#ocean-notifications .navigation .prev'),
            $adminBarCounter: $('#wp-admin-bar-ocean-menu .ocean-menu-notification-counter'),
            $adminBarMenuItem: $('#wp-admin-bar-ocean-notifications'),
        },

        /**
         * Init notifications
         */
        init: function () {
            OceanNotifications.updateNavigation();
            OceanNotifications.add_events();
        },

        /**
         * Add events
         */
         add_events: function () {
            OceanNotifications.elements.$notifications
                .on('click', '.notice-dismiss', OceanNotifications.block)
                .on('click', '.next', OceanNotifications.navNext)
                .on('click', '.prev', OceanNotifications.navPrev);
        },

        block_block_buttons: function () {
            OceanNotifications.elements.$notifications.find('.notice-dismiss').prop('disabled', true);
        },

        unblock_block_buttons: function () {
            OceanNotifications.elements.$notifications.find('.notice-dismiss').prop('disabled', false);
        },

        /**
         * Block notification
         */
         block: function (event) {

            OceanNotifications.block_block_buttons();

            if( OceanNotifications.active_blocking_process ) {
                return;
            }
            OceanNotifications.active_blocking_process = true;

            if (OceanNotifications.elements.$currentMessage.length === 0) {
                return;
            }

            // Update admin bar counter.
            var count = parseInt(OceanNotifications.elements.$adminBarCounter.text(), 10);
            if (count > 1) {
                --count;
                OceanNotifications.elements.$adminBarCounter.html(count);
            } else {
                OceanNotifications.elements.$adminBarCounter.remove();
                OceanNotifications.elements.$adminBarMenuItem.remove();
            }

            // Remove notification.
            var $nextMessage = OceanNotifications.elements.$nextMessage.length < 1 ? OceanNotifications.elements.$prevMessage : OceanNotifications.elements.$nextMessage,
                messageId = OceanNotifications.elements.$currentMessage.data('message-id');

            if ($nextMessage.length === 0) {
                OceanNotifications.elements.$notifications.remove();
            } else {
                OceanNotifications.elements.$currentMessage.remove();
                $nextMessage.addClass('current');
                OceanNotifications.updateNavigation();
            }

            // AJAX call - block notification
            var data = {
                action: 'ocean_notification_block',
                nonce: ocean_notifications_admin.nonce,
                id: messageId,
            };

            $.post(ocean_notifications_admin.ajax_url, data, function (res) {
                OceanNotifications.active_blocking_process = false;
                OceanNotifications.unblock_block_buttons();
            }).fail(function (xhr, textStatus, e) {
                OceanNotifications.active_blocking_process = false;
                OceanNotifications.unblock_block_buttons();
                console.log(xhr.responseText);
            });
        },

        /**
         * Go to next notification
         */
        navNext: function (event) {
            if (OceanNotifications.elements.$nextButton.hasClass('disabled')) {
                return;
            }

            OceanNotifications.elements.$currentMessage.removeClass('current');
            OceanNotifications.elements.$nextMessage.addClass('current');

            OceanNotifications.updateNavigation();
        },

        /**
         * Go to previous notification
         */
        navPrev: function (event) {
            if (OceanNotifications.elements.$prevButton.hasClass('disabled')) {
                return;
            }

            OceanNotifications.elements.$currentMessage.removeClass('current');
            OceanNotifications.elements.$prevMessage.addClass('current');

            OceanNotifications.updateNavigation();
        },

        /**
         * Update navigation buttons
         */
        updateNavigation: function () {
            OceanNotifications.elements.$currentMessage = OceanNotifications.elements.$notifications.find('.ocean-notifications-message.current');
            OceanNotifications.elements.$nextMessage = OceanNotifications.elements.$currentMessage.next('.ocean-notifications-message');
            OceanNotifications.elements.$prevMessage = OceanNotifications.elements.$currentMessage.prev('.ocean-notifications-message');

            if (OceanNotifications.elements.$nextMessage.length === 0) {
                OceanNotifications.elements.$nextButton.addClass('disabled');
            } else {
                OceanNotifications.elements.$nextButton.removeClass('disabled');
            }

            if (OceanNotifications.elements.$prevMessage.length === 0) {
                OceanNotifications.elements.$prevButton.addClass('disabled');
            } else {
                OceanNotifications.elements.$prevButton.removeClass('disabled');
            }
        },
    };


    OceanNotifications.init()
});