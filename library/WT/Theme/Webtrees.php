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
use WT_I18N;

/**
 * Class Webtrees - The webtrees (default) theme.
 */
class Webtrees extends BaseTheme {
	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/webtrees/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/webtrees/css-1.6.0/';
	}

	/** {@inheritdoc} */
	public function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
	}

	/**
	 * The webtrees theme shows the pending changes link in the footer, rather than as a menu item.
	 *
	 * {@inheritdoc}
	 */
	public function footerContent() {
		return parent::footerContent() . $this->formatPendingChangesLink();
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
	public function headerContainer() {
		return
			'<header id="header">' .
			'<div class="header_img">' .
			$this->logoHeader() .
			'</div>' .
			'<ul id="extra-menu" class="makeMenu" role="menubar">' .
			$this->formatUserMenu() .
			'</ul>' .
			'<div class="title">' .
			$this->formatTreeTitle() .
			'</div>' .
			'<div class="header_search">' .
			$this->formatQuickSearch() .
			'</div>' .
			'<div id="topMenu">' .
			'<ul id="main-menu" role="menubar">' .
			$this->formatMainMenu() .
			'</ul>' .
			'</div>' .
			'</header>' .
			$this->flashMessages();
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'webtrees';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return WT_I18N::translate('webtrees');
	}
}
