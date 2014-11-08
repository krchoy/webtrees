<?php
// Administration theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

return new WT\Theme\Administration();

// Main icons
use WT\Theme;

$WT_IMAGES=array(
	// Lightbox module uses this in manage media links, and also admin_media.php for delete folder.
	'remove'          => $this->cssUrl() . 'images/delete.png',

	// Need different sizes before moving to CSS
	'default_image_F' => $this->cssUrl() . 'images/silhouette_female.png',
	'default_image_M' => $this->cssUrl() . 'images/silhouette_male.png',
	'default_image_U' => $this->cssUrl() . 'images/silhouette_unknown.png',
);
