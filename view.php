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

// Page setup. Configure navbar as if this is native course activity page.
$PAGE->navbar->add(get_string('courses'), new moodle_url('/course/index.php'));
$PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', ['id' => $course->id]));
$PAGE->navbar->add(get_string('pluginname', 'block_ari9000'));

$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/ari9000/view.php', ['course' => $course->id]);
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading(get_string('pluginname', 'block_ari9000'));

$output = $PAGE->get_renderer('block_ari9000');
echo $output->header();

$content = new \block_ari9000\output\activity_page($course->id);
echo $output->render_activity_page($content);

echo $output->footer();
