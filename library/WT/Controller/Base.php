<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Assets;

/**
 * Class WT_Controller_Base - Base controller for all other controllers
 */
class WT_Controller_Base {

	protected $page_header = false;        // Have we printed a page header?

	/**
	 * Startup activity
	 */
	public function __construct() {
	}

	/**
	 * Shutdown activity
	 */
	public function __destruct() {
		// If we printed a header, automatically print a footer
		if ($this->page_header) {
			$this->pageFooter();
		}
	}

	/**
	 * Print the page header, using the theme
	 *
	 * @return $this
	 */
	public function pageHeader() {
		// Once we've displayed the header, we should no longer write session data.
		Zend_Session::writeClose();

		// We've displayed the header - display the footer automatically
		$this->page_header = true;

		return $this;
	}

	/**
	 * Print the page footer, using the theme
	 */
	protected function pageFooter() {
		if (WT_DEBUG_SQL) {
			echo WT_DB::getQueryLog();
		}
		echo Assets::js();
	}
}
