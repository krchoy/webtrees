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
use WT_Menu;

/**
 * Class Fab - The F.A.B. theme.
 */
class Fab extends BaseTheme {
	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/fab/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/fab/css-1.6.0/';
	}

	/**
	 * {@inheritdoc}
	 */
	public function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
	}

	/** {@inheritdoc} */
	public function logoPoweredBy() {
		return
			'<a style="font-size:150%; color:#888;" href="' . WT_WEBTREES_URL . '" title="' . WT_WEBTREES . ' - ' . WT_VERSION . '">' .
			WT_WEBTREES .
			'</a>';
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
	public function formatMainMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	public function formatUserMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	public function headerContainer() {
		return
			'<header id="header" class="block">' .
			'<div id="header-user-links">'.
			'<ul class="makeMenu" role="menubar">' .
			$this->formatUserMenu() .
			($this->menuPendingChanges() ? $this->menuPendingChanges()->getMenuAsList() : '') .
			'<li>' .
			$this->formatQuickSearch() .
			'</li>' .
			'</ul>' .
			'</div>' .
			'<div id="header-title" dir="auto">' .
			$this->formatTreeTitle() .
			'</div>' .
			'<div id="header-menu">' .
			'<ul class="makeMenu" role="menubar">' .
			$this->formatMainMenu() .
			'</ul>' .
			'</div>' .
			'</header>' .
			$this->flashMessages();
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'F.A.B.';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('F.A.B.');
	}
}
