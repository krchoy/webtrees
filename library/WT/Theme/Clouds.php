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

use WT\Theme;
use WT_Controller_Page;
use WT_I18N;
use WT_Menu;

/**
 * Class Clouds - The clouds theme.
 */
class Clouds extends BaseTheme {
	/** {@inheritdoc} */
	public function formatMainMenuItem(WT_Menu $menu) {
		// Create an inert menu - to use as a label
		$tmp = new WT_Menu(strip_tags($menu->getLabel()), '');
		// Insert the label into the submenu
		if (is_array($menu->getSubmenus())) {
			array_unshift($menu->getSubmenus(), $tmp);
		} else {
			$menu->addSubmenu($tmp);
		}
		// Neutralise the top-level menu
		$menu->setLabel('');
		$menu->setLink('');
		$menu->setOnclick('');

		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/clouds/css-1.6.0/';
	}

	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/clouds/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
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
	public function headerContent() {
		return
			'<div id="clouds-container">' .
			'<div id="header">' .
			'<span class="title">' .
			$this->formatTreeTitle() .
			'</span>' .
			'<div class="hsearch">' .
			$this->formatQuickSearch() .
			'</div>' .
			'</div>' .
			'<div id="topMenu">' .
			'<ul id="main-menu" role="menubar">' .
			$this->formatMainMenu() .
			'</ul>' .
			'<div id="menu-right">' .
			'<ul class="makeMenu" role="menubar">' .
			$this->formatUserMenu() .
			'</ul>' .
			'</div>' .
			'</div>' .
			'</div>' .
			$this->flashMessages();
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'clouds';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('clouds');
	}
}
