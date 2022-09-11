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

defined('MOODLE_INTERNAL') || die();

/**
 * ARI 9000 block definition class.
 *
 * @package   block_ari9000
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_ari9000 extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_ari9000');
    }

    /**
     * JS required for block.
     */
    function get_required_javascript() {
        $this->page->requires->js_call_amd('block_ari9000/content_modal', 'init');
    }

    /**
     * Returns the aria role attribute that best describes this block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'application';
    }

    /**
     * If header should be hidden.
     *
     * @return boolean
     */
    function hide_header() {
        return true;
    }

    /**
     * Subclasses should override this and return true if the
     * subclass block has a settings.php file.
     *
     * @return boolean
     */
    function has_config() {
        return true;
    }

    /**
     * Gets the block contents.
     *
     * @return string The block HTML.
     */
    public function get_content() {
        global $OUTPUT;
        if ($this->content !== null) {
            return $this->content;
        }

        $buttonimage = $OUTPUT->image_url('button', 'block_ari9000');
        $image = html_writer::img($buttonimage->out(false), get_string('openari', 'block_ari9000'));

        $this->content = new stdClass();
        $this->content->text = html_writer::link('#', $image, [
            'class' => 'block_ari9000_accessbutton',
            'data-action' => 'block_ari9000_show_modal',
            'data-courseid' => $this->page->course->id,
            'title' => get_string('openari', 'block_ari9000')
        ]);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return ['site-index' => true, 'site' => true, 'course' => true];
    }

    /**
     * Allows the block to be added multiple times to a single page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }
}
