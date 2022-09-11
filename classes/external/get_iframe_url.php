<?php
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

namespace block_ari9000\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use block_ari9000\api;

/**
 * block_ari9000 external class get_iframe_url.
 *
 * @package   block_ari9000
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_iframe_url extends external_api {

    /**
     * Get iframe URL parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Get iframe URL
     *
     * @param int $courseid
     * @return array
     */
    public static function execute(int $courseid): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
        ]);

        // Check permissions.
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);

        [$iframeurl, $errormessage] = api::get_iframe_url($params['courseid']);

        if (!debugging()) {
            // Clear error message if debugging is disabled.
            $errormessage = '';
        }

        return ['errormessage' => $errormessage, 'iframesrc' => $iframeurl];
    }

    /**
     * Return for getting iframe URL request.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
               'errormessage' => new external_value(PARAM_TEXT, 'API request error'),
               'iframesrc' => new external_value(PARAM_URL, 'Iframe URL'),
        ], 'Iframe url and errors');
    }
}