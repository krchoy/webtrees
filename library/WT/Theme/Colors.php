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

use WT\Auth;
use WT\Theme;
use WT_Controller_Page;
use WT_I18N;
use WT_Menu;
use WT_Site;

/**
 * Class Colors - The colors theme.
 */
class Colors extends BaseTheme {
	/** @var string[] A list of color palettes */
	protected $sub_colors;

	/** @var string Which of the color palettes to use on this page */
	protected $sub_color;

	/** {@inheritdoc} */
	protected function cssUrl() {
		return 'themes/colors/css-1.6.0/';
	}

	/**
	 * Create a menu of palette options
	 *
	 * @return WT_Menu[]
	 */
	public function paletteMenu() {
		$menu = new WT_Menu(/* I18N: A colour scheme */ WT_I18N::translate('Palette'), '#', 'menu-color');

		foreach ($this->sub_colors as $colorChoice => $color_name) {
			$submenu = new WT_Menu($color_name, get_query_url(array('themecolor' => $colorChoice), '&amp;'), 'menu-color-' . $colorChoice);
			if ($this->session->subColor) {
				if ($this->session->subColor === $colorChoice) {
					$submenu->addClass('', '', 'theme-active');
				}
			} elseif (WT_Site::getPreference('DEFAULT_COLOR_PALETTE') === $colorChoice) {
				// here when visitor changes palette from default
				$submenu->addClass('', '', 'theme-active');
			} elseif ($this->sub_color === 'ash') {
				// here when site has different theme as default and user switches to colors
				if ($this->sub_color === $colorChoice) {
					$submenu->addClass('', '', 'theme-active');
				}
			}
			$menu->addSubMenu($submenu);
		}

		return $menu->getMenuAsList();
	}

	/**
	 * Create resources for the colors theme.
	 *
	 * {@inheritdoc}
	 */
	public function hookAfterInit() {
		$this->sub_colors = array(
			'aquamarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Aqua Marine'),
			'ash'             => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Ash'),
			'belgianchocolate'=> /* I18N: The name of a colour-scheme */ WT_I18N::translate('Belgian Chocolate'),
			'bluelagoon'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Lagoon'),
			'bluemarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Marine'),
			'coffeeandcream'  => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Coffee and Cream'),
			'coldday'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Cold Day'),
			'greenbeam'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Green Beam'),
			'mediterranio'    => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mediterranio'),
			'mercury'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mercury'),
			'nocturnal'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Nocturnal'),
			'olivia'          => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Olivia'),
			'pinkplastic'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Pink Plastic'),
			'sage'            => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Sage'),
			'shinytomato'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Shiny Tomato'),
			'tealtop'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Teal Top'),
		);
		uasort($this->sub_colors, array('WT_I18N', 'strcasecmp'));

		// If we've selected a new palette, and we are logged in, set this value as a default.
		if (isset($_GET['themecolor']) && array_key_exists($_GET['themecolor'], $this->sub_colors)) {
			// Request to change color
			$this->sub_color = $_GET['themecolor'];
			Auth::user()->setPreference('themecolor', $this->sub_color);
			if (Auth::isAdmin()) {
				WT_Site::setPreference('DEFAULT_COLOR_PALETTE', $this->sub_color);
			}
			unset($_GET['themecolor']);
			// Rember that we have selected a value
			$this->session->subColor = $this->sub_color;
		}
		// If we are logged in, use our preference
		$this->sub_color = Auth::user()->getPreference('themecolor');
		// If not logged in or no preference, use one we selected earlier in the session?
		if (!$this->sub_color) {
			$this->sub_color = $this->session->subColor;
		}
		// We haven't selected one this session?  Use the site default
		if (!$this->sub_color) {
			$this->sub_color = WT_Site::getPreference('DEFAULT_COLOR_PALETTE');
		}
		// Make sure our selected palette actually exists
		if (!array_key_exists($this->sub_color, $this->sub_colors)) {
			$this->sub_color = 'ash';
		}
	}

	/** {@inheritdoc} */
	public function stylesheets() {
		return array(
			'themes/colors/jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css',
			$this->cssUrl() . 'css/colors.css',
			$this->cssUrl() . 'css/' . $this->sub_color . '.css',
		);
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
		$html = parent::headContents($controller);

		if (stristr($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
			$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.8, maximum-scale=2.0" />';
			$html .= '<link type="text/css" rel="stylesheet" href="' . $this->cssUrl() . 'ipad.css">';
		}

		// This is needed for all versions of IE.
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE') || stristr($_SERVER['HTTP_USER_AGENT'], 'Trident')) {
			$html .= '<link type="text/css" rel="stylesheet" href="' . $this->cssUrl() . 'msie.css">';
		}

		return $html;
	}

	/** {@inheritdoc} */
	public function headerContent() {
		return
			'<div id="header">' .
			'<span class="title">' .
			$this->formatTreeTitle() .
			'</span>' .
			'<ul class="makeMenu" role="menubar">' .
			$this->formatUserMenu() .
			'<li>' .
			$this->formatQuickSearch() .
			'</li>' .
			'</ul>' .
			'</div>' .
			'<ul id="main-menu" role="menubar">' .
			$this->formatMainMenu() .
			'</ul>' .
			$this->flashMessages();
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'colors';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('colors');
	}
}
