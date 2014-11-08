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
 * Class Xenea - The xenea theme.
 */
class Xenea extends BaseTheme {
	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/xenea/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/xenea/css-1.6.0/';
	}

	/** {@inheritdoc} */
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
	public function headerContainer() {
		return
			'<header>' .
			'<div id="header">' .
			'<span class="title" dir="auto">' .
			$this->formatTreeTitle() .
			'</span>' .
			'<div class="hsearch">' .
			$this->formatQuickSearch() .
			'</div>' .
			'</div>' .
			'<div id="optionsmenu">'.
			'<div id="theme-menu">'.
			'<ul class="makeMenu">' .
			($this->menuThemes() ? $this->menuThemes()->getMenuAsList() : '') .
			'</ul>' .
			'</div>' .
			'<div id="fav-menu">' .
			'<ul class="makeMenu">' .
			($this->menuFavorites() ? $this->menuFavorites()->getMenuAsList() : '') .
			'</ul>' .
			'</div>' .
			'<div id="login-menu">' .
			'<ul class="makeMenu">' .
			($this->menuLogin() ? $this->menuLogin()->getMenuAsList() : '') . ' ' .
			($this->menuMyAccount() ? $this->menuMyAccount()->getMenuAsList() : '') . ' ' .
			($this->menuLogout() ? $this->menuLogout()->getMenuAsList() : '') . ' ' .
			($this->menuPendingChanges() ? $this->menuPendingChanges()->getMenuAsList() : '') .
			'</ul>' .
			'</div>' .
			'<div id="lang-menu" class="makeMenu">' .
			'<ul class="makeMenu" role="menubar">' .
			($this->menuLanguages() ? $this->menuLanguages()->getMenuAsList() : '') .
			'</ul>' .
			'</div>' .
			'</div>' .
			'<div id="topMenu">' .
			'<ul id="main-menu" role="menubar">' .
			$this->formatMainMenu() .
			'</ul>' .
			'</div>' .
			'</div>' .
			'</header>' .
			$this->flashMessages();
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'xenea';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('xenea');
	}
}
