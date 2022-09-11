// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Shows ARI 9000 content in modal.
 *
 * @module    block_ari9000/content_modal
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import Notification from 'core/notification';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import Ajax from 'core/ajax';

/** @type {Object} Selectors. */
const Selectors = {
    ShowModalAction: "[data-action='block_ari9000_show_modal']",
};

/**
 * Show content modal.
 *
 * @param {Element} element
 */
const showContentModal = function(element) {
    const request = {
        methodname: 'block_ari9000_get_iframe_url',
        args: {courseid: element.dataset.courseid},
    };

    const bodyPromise = Ajax.call([request])[0].then(data => {
        if (data.errormessage) {
            // If debug mode on, data will contain error message.
            window.console.error(data.errormessage);
        }
        return Templates.render('block_ari9000/activity_page', data);
    }).then((element) => {
        // Append dummy form element to prevent modal closing on backdrop click.
        return element + '<form></form>';
    });

    ModalFactory.create({
        type: ModalFactory.types.DEFAULT,
        title: '',
        large: true,
    }).then(function(modal) {
        modal.getModal().addClass('block-ari9000-content-modal');
        modal.getModal().addClass('modal-xl');
        modal.setBody(bodyPromise);
        modal.getRoot().on(ModalEvents.hidden, function() {
            modal.destroy();
        });
        modal.show();
    }).fail(Notification.exception);
};

/**
 * Init click action.
 */
const init = function() {
    document.addEventListener('click', (event) => {
        const actionElement = event.target.closest(Selectors.ShowModalAction);
        if (actionElement) {
            event.preventDefault();
            showContentModal(actionElement);
        }
    });
};

export default {
    init: init
};
