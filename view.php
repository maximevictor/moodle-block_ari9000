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

/**
 * Embed ARI 9000 content.
 *
 * @package   block_ari9000
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/filelib.php');

$courseid = required_param('course', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

// Check permissions.
require_login(null, false);
$coursecontext = \context_course::instance($course->id);
if (!is_enrolled($coursecontext, $USER->id, '', true) &&
        !has_capability('moodle/course:view', $coursecontext, $USER->id)) {
    print_error('requireloginerror');
}
$PAGE->set_context($coursecontext);

// API request.
$curl = new \curl();

// Set timeout.
$curl->setopt(array('CURLOPT_TIMEOUT' => 5, 'CURLOPT_CONNECTTIMEOUT' => 5));

// TODO: Auth header?

// Query API.
$params = [
    'username' => $USER->username,
    'email' => $USER->email,
    'course' => $course->id,
];
$response = $curl->post('http://www.magmalearning.com/api/MoodleUser', $params);

// Process errors.
$info = $curl->get_info();
if ($curlerrno = $curl->get_errno()) {
    $failurereason = "Unexpected response, CURL error number: $curlerrno Error: {$curl->error}";
} else if ((int)$info['http_code'] >= 400) {
    $failurereason = "Unexpected response, HTTP code: " . $info['http_code'] . " Response: $response";
}

$response = json_decode($response, true);
if (!empty($response['token'])) {
    $iframesrc = new moodle_url('https://www.ari9000.com/landing-sso', ['token' => $response['token']]);
} else {
    $iframesrc = new moodle_url('https://www.ari9000.com/');
}

// Page setup. Configure navbar as if this is native course activity page.
$PAGE->navbar->add(get_string('courses'), new moodle_url('/course/index.php'));
$PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', ['id' => $course->id]));
$PAGE->navbar->add(get_string('pluginname', 'block_ari9000'));

$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/ari9000/view.php', ['course' => $course->id]);
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading(get_string('pluginname', 'block_ari9000'));

echo $OUTPUT->header();

if (isset($failurereason)) {
    // Output debug info.
    debugging($failurereason);
}

// TODO: Move to template.
echo html_writer::tag('iframe', '', ['class' => 'block_ari9000_iframe', 'id' => 'ari9000content', 'src' => $iframesrc->out(false), 'height' => '600px', 'width' => '100%']);

echo $OUTPUT->footer();

