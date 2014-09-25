<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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

class ckeditor_WT_Module extends WT_Module {
	const VERSION = 'ckeditor-4.4.1-custom';

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module.  CKEditor is a trademark.  Do not translate it?  http://ckeditor.com */ WT_I18N::translate('CKEditor™');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “CKEditor” module.  WYSIWYG = “what you see is what you get” */ WT_I18N::translate('Allow other modules to edit text using a “WYSIWYG” editor, instead of using HTML codes.');
	}

	/**
	 * Convert <textarea class="html-edit"> fields to CKEditor fields
	 *
	 * This function needs to be called *after* we have sent the page header and
	 * before we have sent the page footer.
	 *
	 * @return void
	 */
	public static function enableEditor() {
		// The ckeditor library attempts to detect its own installation directory, so it can
		// generate URLs to its other resources.  When an asset pipeline is used, this detection
		// fails.  Therefore we need to tell it the original installation path, and we need
		// do this (in the current version at least) *before* loading the library.
		echo '<script>var CKEDITOR_BASEPATH="' . WT_MODULES_DIR . 'ckeditor/' . self::VERSION . '/";</script>';

		Assets::addJs(WT_MODULES_DIR . 'ckeditor/' . self::VERSION . '/ckeditor.js');
		Assets::addJs(WT_MODULES_DIR . 'ckeditor/' . self::VERSION . '/adapters/jquery.js');
		Assets::addInlineJs('jQuery(".html-edit").ckeditor(function(){}, {
			language: "' . str_replace('_','-',strtolower(WT_LOCALE)) . '"
		});');
	}
}
