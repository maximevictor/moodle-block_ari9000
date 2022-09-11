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

namespace block_ari9000;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing helper methods for ARI 9000 block.
 *
 * @package   block_ari9000
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Generate iframe URL.
     *
     * @param int $courseid
     * @return array Array containing URL string and optional error message.
     */
    public static function get_iframe_url($courseid): array {
        global $USER, $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $curl = new \curl();

        // Set timeout.
        $curl->setopt(array('CURLOPT_TIMEOUT' => 5, 'CURLOPT_CONNECTTIMEOUT' => 5));

        // Query API.
        $params = [
            'api_key' => get_config('block_ari9000', 'apikey'),
            'username' => $USER->username,
            'email' => $USER->email,
            'course' => $courseid,
        ];

        $response = $curl->post('https://www.magmalearning.com/api/MoodleUser', $params);

        // Process errors.
        $info = $curl->get_info();
        $failurereason = '';
        if ($curlerrno = $curl->get_errno()) {
            $failurereason = "Unexpected API response, CURL error number: $curlerrno Error: {$curl->error}";
        } else if ((int)$info['http_code'] >= 400) {
            $failurereason = "Unexpected API response, HTTP code: " . $info['http_code'] . " Response: $response";
        }

        $response = json_decode($response, true);
        if (!empty($response['token'])) {
            $iframesrc = new moodle_url('https://www.ari9000.com/landing-sso', ['token' => $response['token']]);
        } else {
            // Getting token failed, fallback to use general page.
            $iframesrc = new moodle_url('https://www.ari9000.com/');
        }

        return [$iframesrc->out(false), $failurereason];
    }
}