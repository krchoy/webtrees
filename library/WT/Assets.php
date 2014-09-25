<?php
namespace WT;

// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team
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

/**
 * Class Assets - a static interface to \Stolz\Assets\Manager
 */
class Assets {
	/** @var \Stolz\Assets\Manager */
	private static $asset_manager;

	/** @var string - see https://github.com/Stolz/Assets/issues/36 */
	private static $inline_js;

	/**
	 * @return \Stolz\Assets\Manager
	 */
	private static function assetManager() {
		if (self::$asset_manager === null) {
			self::$asset_manager = new \Stolz\Assets\Manager(array(
				'css_dir' => '.',
				'js_dir' => '.',
				'pipeline' => is_writable(WT_ROOT . 'min'),
				'pipeline_dir' => 'min',
				'pipeline_gzip' => 9,
				'public_dir' => WT_ROOT,
				'fetch_command' => function ($asset) {
					$unprocessed = file_get_contents($asset);
					$prefix = str_replace(WT_ROOT . '/', '.', dirname($asset)) . '/';
					$processed = preg_replace('/url\(([^:)]*)\)/', 'url(' . $prefix . "$1" . ')', $unprocessed);
					return $processed;
				},
			));
		}

		return self::$asset_manager;
	}

	public static function addCss($asset) {
		self::assetManager()->addCss($asset);
	}

	public static function addJs($asset) {
		self::assetManager()->addJs($asset);
	}

	public static function addInlineJs($script) {
		self::$inline_js .= '<script>' . $script . '</script>';
	}

	public static function css() {
		return self::assetManager()->css();
	}

	public static function js() {
		return self::assetManager()->js() . self::$inline_js;
	}
}
