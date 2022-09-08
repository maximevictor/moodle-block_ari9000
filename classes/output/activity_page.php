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

namespace block_ari9000\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use block_ari9000\api;
use renderer_base;

/**
 * ARI 9000 content renderable class.
 *
 * @package   block_ari9000
 * @copyright 2022 MAGMA Learning Sarl {@link https://www.magmalearning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_page implements renderable, templatable {

    /** @var int */
    protected $courseid;

    /**
     * Constructor.
     */
    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $iframeurl = api::get_iframe_url($this->courseid);
        return (object) ['iframesrc' => $iframeurl];
    }

}