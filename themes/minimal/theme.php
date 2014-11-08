<?php
// Minimal theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

return new WT\Theme\Minimal;

//-- variables for image names
$WT_IMAGES = array(
	// used to draw charts
	'dline'           => Theme::theme()->cssUrl() . 'images/dline.png',
	'dline2'          => Theme::theme()->cssUrl() . 'images/dline2.png',
	'hline'           => Theme::theme()->cssUrl() . 'images/hline.png',
	'spacer'          => Theme::theme()->cssUrl() . 'images/spacer.png',
	'vline'           => Theme::theme()->cssUrl() . 'images/vline.png',

	// used in button images and javascript
	'add'             => Theme::theme()->cssUrl() . 'images/add.png',
	'button_family'   => Theme::theme()->cssUrl() . 'images/buttons/family.png',
	'minus'           => Theme::theme()->cssUrl() . 'images/minus.png',
	'plus'            => Theme::theme()->cssUrl() . 'images/plus.png',
	'remove'          => Theme::theme()->cssUrl() . 'images/remove.png',
	'search'          => Theme::theme()->cssUrl() . 'images/search.png',

	// need different sizes before moving to CSS
	'default_image_F' => Theme::theme()->cssUrl() . 'images/silhouette_female.png',
	'default_image_M' => Theme::theme()->cssUrl() . 'images/silhouette_male.png',
	'default_image_U' => Theme::theme()->cssUrl() . 'images/silhouette_unknown.png',
);

//-- Variables for the Fan chart
$fanChart = array(
	'font'     => WT_ROOT . 'includes/fonts/DejaVuSans.ttf',
	'size'     => 7,
	'color'    => '#000000',
	'bgColor'  => '#eeeeee',
	'bgMColor' => '#cccccc',
	'bgFColor' => '#dddddd'
);

// This section defines variables for the pedigree chart
$bwidth        = 250; // width of boxes on pedigree chart
$bheight       = 80;  // height of boxes on pedigree chart
$baseyoffset   = 10;  // position the entire pedigree tree relative to the top of the page
$basexoffset   = 10;  // position the entire pedigree tree relative to the left of the page
$bxspacing     = 0;   // horizontal spacing between boxes on the pedigree chart
$byspacing     = 5;   // vertical spacing between boxes on the pedigree chart
$brborder      = 1;   // box right border thickness
$linewidth     = 1.5; // width of joining lines
$shadowcolor   = '';  // shadow color for joining lines
$shadowblur    = 0;   // shadow blur for joining lines
$shadowoffsetX = 0;   // shadowOffsetX for joining lines
$shadowoffsetY = 0;   // shadowOffsetY for joining lines

// Global variables for the descendancy chart
$Dbaseyoffset  = 20;  // position the entire descendancy tree relative to the top of the page
$Dbasexoffset  = 20;  // position the entire descendancy tree relative to the left of the page
$Dbxspacing    = 5;   // horizontal spacing between boxes
$Dbyspacing    = 10;  // vertical spacing between boxes
$Dbwidth       = 260; // width of DIV layer boxes
$Dbheight      = 80;  // height of DIV layer boxes
$Dindent       = 15;  // width to indent descendancy boxes
$Darrowwidth   = 30;  // additional width to include for the up arrows
$Darrowwidth   = 15;  // additional width to include for the up arrows

// Dimensions for compact version of chart displays
$cbwidth  = 240;
$cbheight = 50;

// The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = 440;
$WT_STATS_S_CHART_Y = 125;
$WT_STATS_L_CHART_X = 900;
// For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = 440;
$WT_STATS_MAP_Y = 220;

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "555555";
$WT_STATS_CHART_COLOR3 = "cccccc";
