<?php namespace WT\Theme;
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
use WT_Controller_Page;

/**
 * Class Administration - The admin theme.
 */
class Administration extends BaseTheme {
	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/_administration/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/_administration/css-1.6.0/';
	}

	/** {@inheritdoc} */
	public function favicon() {
		// Use the default webtrees favicon
		return '<link rel="icon" href="favicon.ico" type="image/x-icon">';
	}

	/**
	 * Add some additional markup to the <head> element.
	 *
	 * {@inheritdoc}
	 */
	public function headContents(WT_Controller_Page $controller) {
		return
			parent::headContents($controller) .
			'<!--[if IE]>' .
			'<link type="text/css" rel="stylesheet" href="' . $this->cssUrl() . 'msie.css">' .
			'<![endif]-->';
	}

	/** {@inheritdoc} */
	public function themeId() {
		return '_administration';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return 'administration';
	}
}
