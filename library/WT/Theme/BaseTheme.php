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

use gedcom_favorites_WT_Module;
use user_favorites_WT_Module;
use WT\Auth;
use WT\Theme;
use WT\User;
use WT_Controller_Page;
use WT_DB;
use WT_Fact;
use WT_FlashMessages;
use WT_Gedcom_Tag;
use WT_GedcomRecord;
use WT_I18N;
use WT_Filter;
use WT_Individual;
use WT_Menu;
use WT_Module;
use WT_Site;
use WT_Tree;
use Zend_Session_Namespace;

/**
 * Class Base - Common functions and interfaces for all themes.
 *
 * Functions whose behaviour and call-signature may change are marked as
 * final, to prevent problems in custom themes.
 */
abstract class BaseTheme {
	/** @var BaseTheme[] A list of all installed themes */
	private static $installed_themes;

	/** @var Zend_Session_Namespace */
	protected $session;

	/** @var WT_Tree */
	protected $tree;

	/** @var string An escaped version of the "ged=XXX" URL parameter */
	protected $tree_url;

	/**
	 * Custom themes should place their initialization code in the function hookAfterInit(), not in
	 * the constructor, as all themes get constructed - whether used or not.
	 */
	final public function __construct() {
	}

	/**
	 * Create a contact link for a user.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLink(User $user) {
		$method = $user->getPreference('contactmethod');

		switch ($method) {
		case 'none':
			return '';
		case 'mailto':
			return '<a href="mailto:' . WT_Filter::escapeHtml($user->getEmail()) . '">' . WT_Filter::escapeHtml($user->getRealName()) . '</a>';
		default:
			return "<a href='#' onclick='message(\"" . WT_Filter::escapeJs($user->getUserName()) . "\", \"" . $method . "\", \"" . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_Filter::escapeJs(get_query_url()) . "\", \"\");return false;'>" . WT_Filter::escapeHtml($user->getRealName()) . '</a>';
		}
	}

	/**
	 * Create contact link for genealogy support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLinkEverything(User $user) {
		return WT_I18N::translate('For technical support or genealogy questions, please contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for genealogy support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLinkGenealogy(User $user) {
		return WT_I18N::translate('For help with genealogy questions contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for technical support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLinkTechnical(User $user) {
		return WT_I18N::translate('For technical support and information contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact links for the page footer.
	 *
	 * @return string
	 */
	public function contactLinks() {
		$contact_user   = User::find($this->tree->getPreference('CONTACT_USER_ID'));
		$webmaster_user = User::find($this->tree->getPreference('WEBMASTER_USER_ID'));

		if ($contact_user && $contact_user === $webmaster_user) {
			return $this->contactLinkEverything($contact_user);
		} elseif ($contact_user && $webmaster_user) {
			return $this->contactLinkGenealogy($contact_user) . '<br>' . $this->contactLinkTechnical($webmaster_user);
		} elseif ($contact_user) {
			return $this->contactLinkGenealogy($contact_user);
		} elseif ($webmaster_user) {
			return $this->contactLinkTechnical($webmaster_user);
		} else {
			return '';
		}
	}

	/**
	 * Where are our CSS assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	protected function cssUrl() {
		return '';
	}

	/**
	 * Create the <DOCTYPE> tag.
	 *
	 * @return string
	 */
	public function doctype() {
		return '<!DOCTYPE html>';
	}

	/**
	 * HTML link to a "favorites icon".
	 *
	 * @return string
	 */
	public function favicon() {
		return '';
	}

	/**
	 * A list of messages generated in a previous request.
	 *
	 * @return string
	 */
	public function flashMessages() {
		return WT_FlashMessages::getHtmlMessages();
	}

	/**
	 * Create a container for messages that are "flashed" to the session
	 * on one request, and displayed on another.
	 *
	 * @return string
	 */
	public function flashMessagesContainer() {
		$content = $this->flashMessagesContent();

		if ($content) {
			return '<div id="flash-messages" role="status">' . $this->messagesContent() . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Create the flash messages.
	 *
	 * @return string
	 */
	public function flashMessagesContent() {
		return WT_FlashMessages::getHtmlMessages(); // Feedback from asynchronous actions
	}

	/**
	 * Create the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContainer() {
		return '<footer id="footer">' . $this->footerContent() . '</footer>';
	}

	/**
	 * Create the contents of the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContent() {
		return $this->formatContactLinks() . $this->logoPoweredBy();
	}

	/**
	 * Create the <footer> tag for popup windows.
	 *
	 * @return string
	 */
	public function footerSimple() {
		return '</div>';
	}

	/**
	 * Add markup to the contact links.
	 *
	 * @return string
	 */
	public function formatContactLinks() {
		if ($this->tree) {
			return '<div class="contact_links">' . $this->contactLinks() . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the main menu.
	 *
	 * @return string
	 */
	public function formatMainMenu() {
		return implode('', $this->menuBarMain());
	}

	/**
	 * Add markup to an item in the main menu.
	 *
	 * @param WT_Menu $menu
	 *
	 * @return string
	 */
	public function formatMainMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/**
	 * Create a pending changes link for the page footer.
	 *
	 * @return string
	 */
	public function formatPendingChangesLink() {
		if ($this->pendingChangesExist()) {
			return '<div class="pending-changes-link">' . $this->pendingChangesLink() . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the quick search form.
	 *
	 * @return string
	 */
	public function formatQuickSearch() {
		global $WT_IMAGES;

		if ($this->tree) {
			return
				'<form action="search.php" class="quick-search" method="post" role="search">' .
				'<input type="hidden" name="action" value="general">' .
				'<input type="hidden" name="ged" value="' . $this->tree->tree_name_html . '">' .
				'<input type="hidden" name="topsearch" value="yes">' .
				'<input type="search" name="query" size="25" placeholder="' . WT_I18N::translate('Search') . '">' .
				'<input type="image" src="' . $WT_IMAGES['search'] . '" alt="' . WT_I18N::translate('Search') . '">' .
				'</form>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to an item in the user menu.
	 *
	 * @return string
	 */
	public function formatTreeTitle() {
		if ($this->tree) {
			return $this->tree->tree_title_html;
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the user menu.
	 *
	 * @return string
	 */
	public function formatUserMenu() {
		return implode('', $this->menuBarUser());
	}

	/**
	 * Add markup to an item in the user menu.
	 *
	 * @param WT_Menu $menu
	 *
	 * @return string
	 */
	public function formatUserMenuItem(WT_Menu $menu) {
		return (string)$menu;
	}

	/**
	 * Create the contents of the <head> tag.
	 *
	 * @param WT_Controller_Page $controller The current controller
	 *
	 * @return string
	 */
	public function headContents(WT_Controller_Page $controller) {
		// The title often includes the names of records, which may include HTML markup.
		$title = html_entity_decode(strip_tags($controller->getPageTitle()), ENT_QUOTES);

		// If an extra (site) title is specified, append it.
		if ($this->tree && $this->tree->getPreference('META_TITLE')) {
			$title .= ' - ' . WT_Filter::escapeHtml($this->tree->getPreference('META_TITLE'));
		}

		$html =
			$this->metaCharset() .
			$this->metaRobots($controller->getMetaRobots()) .
			$this->title($title) .
			$this->metaGenerator(WT_WEBTREES . ' ' . WT_VERSION . ' - ' . WT_WEBTREES_URL);

		if ($controller->getCanonicalUrl()) {
			$html .= $this->metaCanonicalUrl($controller->getCanonicalUrl());
		}

		if ($this->tree) {
			$html .= $this->metaDescription($this->tree->getPreference('META_DESCRIPTION', html_entity_decode(strip_tags($this->tree->tree_title_html), ENT_QUOTES)));
		}

		// CSS files
		foreach ($this->stylesheets() as $css) {
			$html .= '<link rel="stylesheet" type="text/css" href="' . $css . '">';
		}

		return $html;
	}

	/**
	 * Create the <header> tag.
	 *
	 * @return string
	 */
	public function headerContainer() {
		return
			'<header id=header">' .
			$this->headerContent() .
			'</header>';
	}

	/**
	 * Create the contents of the <header> tag.
	 *
	 * @return string
	 */
	public function headerContent() {
		return
			$this->logoHeader() .
			$this->formatUserMenu() .
			$this->formatTreeTitle() .
			$this->formatQuickSearch() .
			$this->formatMainMenu(); // Feedback from asynchronous actions
	}

	/**
	 * Create the <header> tag for a popup window.
	 *
	 * @return string
	 */
	public function headerSimple() {
		return
			WT_FlashMessages::getHtmlMessages() . // Feedback from asynchronous actions
			'<div id="content">';
	}

	/**
	 * Allow custom themes to do things after initialization.
	 *
	 * @return void
	 */
	public function hookAfterInit() {
	}

	/**
	 * Create the <html> tag.
	 *
	 * @return string
	 */
	public function html() {
		return '<html ' . WT_I18N::html_markup() . '>';
	}

	/**
	 * Display an icon for this fact.
	 *
	 * @deprecated - the theme should generate the entire fact.
	 *
	 * @param WT_Fact $fact
	 *
	 * @return string
	 */
	public function icon(WT_Fact $fact) {
		$icon = 'images/facts/' . $fact->getTag() . '.png';
		$dir  = substr($this->cssUrl(), strlen(WT_STATIC_URL));
		if (file_exists($dir . $icon)) {
			return '<img src="' . $this->cssUrl() . $icon . '" title="' . WT_Gedcom_Tag::getLabel($fact->getTag()) . '">';
		} elseif (file_exists($dir . 'images/facts/NULL.png')) {
			// Spacer image - for alignment - until we move to a sprite.
			return '<img src="' . Theme::theme()->cssUrl() . 'images/facts/NULL.png">';
		} else {
			return '';
		}
	}

	/**
	 * Initialise the theme.  We cannot pass these in a constructor, as
	 * the construction happens in a theme, and we need to be able to
	 * change it.
	 *
	 * @param WT_Tree                $tree
	 * @param Zend_Session_Namespace $session
	 *
	 * @return string
	 */
	final public function init(WT_Tree $tree, Zend_Session_Namespace $session) {
		$this->tree     = $tree;
		$this->tree_url = 'ged=' . WT_Filter::escapeUrl($tree->tree_name);
		$this->session  = $session;
		$this->hookAfterInit();
	}

	/**
	 * Are we generating a page for a robot (instead of a human being).
	 *
	 * @return boolean
	 */
	public function isSearchEngine() {
		global $SEARCH_SPIDER;

		return (bool)$SEARCH_SPIDER;
	}

	/**
	 * A large webtrees logo, for the footer.
	 *
	 * @return string
	 */
	public function logoHeader() {
		return '<img src="' . $this->cssUrl() . 'images/webtrees.png" width="242" height="50" alt="' . WT_WEBTREES . '">';
	}

	/**
	 * A small "powered by" logo for the footer.
	 *
	 * @return string
	 */
	public function logoPoweredBy() {
		return
			'<a style="font-size:150%; color:#888;" href="' . WT_WEBTREES_URL . '" title="' . WT_WEBTREES . ' - ' . WT_VERSION . '">' .
			WT_WEBTREES .
			'</a>';
	}

	/**
	 * Generate a list of items for the main menu.
	 *
	 * @return WT_Menu[]
	 */
	public function menuBarMain() {
		return array_filter(array_merge(array(
			$this->menuHomePage(),
			$this->menuMyPage(),
			$this->menuCharts(),
			$this->menuLists(),
			$this->menuCalendar(),
			$this->menuReports(),
			$this->menuSearch(),
		), $this->menuModules()));
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return WT_Menu[]
	 */
	public function menuBarUser() {
		return array_filter(array(
			$this->menuLogin(),
			$this->menuMyAccount(),
			$this->menuLogout(),
			$this->menuFavorites(),
			$this->menuThemes(),
			$this->menuLanguages(),
		));
	}

	/**
	 * @return WT_Menu
	 */
	public function menuCalendar() {
		if ($this->isSearchEngine()) {
			return null;
		}

		// Default action is the day view.
		$menu = new WT_Menu(WT_I18N::translate('Calendar'), 'calendar.php?' . $this->tree_url, 'menu-calendar');

		// Day view
		$submenu = new WT_Menu(WT_I18N::translate('Day'), 'calendar.php?' . $this->tree_url, 'menu-calendar-day');
		$menu->addSubmenu($submenu);

		// Month view
		$submenu = new WT_Menu(WT_I18N::translate('Month'), 'calendar.php?' . $this->tree_url . '&amp;action=calendar', 'menu-calendar-month');
		$menu->addSubmenu($submenu);

		//Year view
		$submenu = new WT_Menu(WT_I18N::translate('Year'), 'calendar.php?' . $this->tree_url . '&amp;action=year', 'menu-calendar-year');
		$menu->addSubmenu($submenu);

		return $menu;
	}

	/**
	 * Generate a menu for each of the different charts.
	 *
	 * @return WT_Menu
	 */
	public function menuCharts() {
		global $controller;

		if ($this->isSearchEngine() || !WT_GED_ID) {
			return null;
		}

		$individual = $controller->getSignificantIndividual();

		// The top level menu is the pedigree chart
		$menu = $this->menuChartPedigree($individual);
		$menu->setLabel(WT_I18N::translate('Charts'));
		$menu->setId('menu-chart');

		$submenus = array_filter(array(
			$this->menuChartAncestors($individual),
			$this->menuChartCompact($individual),
			$this->menuChartDescendants($individual),
			$this->menuChartFamilyBook($individual),
			$this->menuChartFanChart($individual),
			$this->menuChartHourglass($individual),
			$this->menuChartLifespan($individual),
			$this->menuChartPedigree($individual),
			$this->menuChartStatistics(),
			$this->menuChartTimeline($individual),
		));

		usort($submenus, function(WT_Menu $x, WT_Menu $y) {
			return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel());
		});

		$menu->setSubmenus($submenus);

		return $menu;

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array(
			'relationship' => WT_I18N::translate('Relationships'),
		);
		// TODO: Use WT_Module_Chart ??
		if (array_key_exists('tree', WT_Module::getActiveModules())) {
			$menuList['tree'] = WT_I18N::translate('Interactive tree');
		}
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			$menuList['pedigree_map'] = WT_I18N::translate('Pedigree map');
		}
		asort($menuList);

		// Produce the submenus in localized name order
		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case 'timeline':
				$submenu = new WT_Menu($menuName, 'timeline.php?pids%5B%5D=' . $indi_xref . '&amp;' . $this->tree_url, 'menu-chart-timeline');
				if ($controller instanceof WT_Controller_Family && $controller->record) {
					// Build a sortable list of submenu items and then sort it in localized name order
					$menuList                   = array();
					$menuList['parentTimeLine'] = WT_I18N::translate('Show couple on timeline chart');
					$menuList['childTimeLine']  = WT_I18N::translate('Show children on timeline chart');
					$menuList['familyTimeLine'] = WT_I18N::translate('Show family on timeline chart');
					asort($menuList);

					// Produce the submenus in localized name order
					foreach ($menuList as $submenuType => $submenuName) {
						switch ($submenuType) {
						case 'parentTimeLine':
							// charts / parents_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?' . $controller->getTimelineIndis(array('HUSB', 'WIFE')) . '&amp;' . $this->tree_url,
								'menu-chart-timeline-parents'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						case 'childTimeLine':
							// charts / children_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?' . $controller->getTimelineIndis(array('CHIL')) . '&amp;' . $this->tree_url,
								'menu-chart-timeline-children'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						case 'familyTimeLine':
							// charts / family_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?' . $controller->getTimelineIndis(array('HUSB', 'WIFE', 'CHIL')) . '&amp;' . $this->tree_url,
								'menu-chart-timeline-family'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						}
					}
				}
				$menu->addSubmenu($submenu);
				break;

			case 'relationship':
				if ($indi_xref) {
					// Pages focused on a specific person - from the person, to me
					$pid1 = WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID;;
					$pid2 = $indi_xref;
					if ($pid1 === $pid2) {
						$pid2 = '';
					}
					$submenu = new WT_Menu(
						WT_I18N::translate('Relationships'),
						'relationship.php?pid1=' . $pid1 . '&amp;pid2=' . $pid2 . '&amp;' . $this->tree_url,
						'menu-chart-relationship'
					);
					if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
						// Add a submenu showing relationship from this person to each of our favorites
						foreach (user_favorites_WT_Module::getFavorites(Auth::id()) as $favorite) {
							if ($favorite['type'] === 'INDI' && $favorite['gedcom_id'] == WT_GED_ID) {
								$person = WT_Individual::getInstance($favorite['gid']);
								if ($person instanceof WT_Individual) {
									$subsubmenu = new WT_Menu(
										$person->getFullName(),
										'relationship.php?pid1=' . $person->getXref() . '&amp;pid2=' . $pid2 . '&amp;' . $this->tree_url,
										'menu-chart-relationship-' . $person->getXref() . '-' . $pid2 // We don't use these, but a custom theme might
									);
									$submenu->addSubmenu($subsubmenu);
								}
							}
						}
					}
				} else {
					// Regular pages - from me, to somebody
					$pid1    = WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID;
					$pid2    = '';
					$submenu = new WT_Menu(
						WT_I18N::translate('Relationships'),
						'relationship.php?pid1=' . $pid1 . '&amp;pid2=' . $pid2 . '&amp;' . $this->tree_url,
						'menu-chart-relationship'
					);
				}
				$menu->addSubmenu($submenu);
				break;

			case 'tree':
				$submenu = new WT_Menu($menuName, 'module.php?mod=tree&amp;mod_action=treeview&amp;' . $this->tree_url . '&amp;rootid=' . $indi_xref, 'menu-chart-tree');
				$menu->addSubmenu($submenu);
				break;

			case 'pedigree_map':
				$submenu = new WT_Menu($menuName, 'module.php?' . $this->tree_url . '&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=' . $indi_xref, 'menu-chart-pedigree_map');
				$menu->addSubmenu($submenu);
				break;
			}
		}

		return $menu;
	}

	/**
	 * Generate a menu item for the fan chart (fanchart.php).
	 *
	 * We can only do this if the GD2 library is installed with TrueType support.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartFanChart(WT_Individual $individual) {
		if (function_exists('imagettftext')) {
			return new WT_Menu(WT_I18N::translate('Fan chart'), 'fanchart.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-fanchart');
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu item for the lifepsan chart (lifespan.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartLifespan(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Lifespans'), 'lifespan.php?pids%5B%5D=' . $individual->getXref() . '&amp;addFamily=1&amp;' . $this->tree_url, 'menu-chart-lifespan');
	}

	/**
	 * Generate a menu item for the statistics charts (statistics.php).
	 *
	 * @return WT_Menu
	 */
	public function menuChartStatistics() {
		return new WT_Menu(WT_I18N::translate('Statistics'), 'statistics.php?' . $this->tree_url, 'menu-chart-statistics');
	}

	/**
	 * Generate a menu item for the timeline chart (timeline.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartTimeline(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Timeline'), 'timeline.php?pids%5B%5D=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-timeline');
	}

	/**
	 * Generate a menu item for the compact tree (compact.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartCompact(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Compact tree'), 'compact.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-compact');
	}

	/**
	 * Generate a menu item for the hourglass chart (hourglass.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartHourglass(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Hourglass chart'), 'hourglass.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-hourglass');
	}

	/**
	 * Generate a menu item for the family-book chart (familybook.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartFamilyBook(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Family book'), 'familybook.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-familybook');
	}

	/**
	 * Generate a menu item for the pedigree chart (pedigree.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartPedigree(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Pedigree'), 'pedigree.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-pedigree');
	}

	/**
	 * Generate a menu item for the ancestors chart (ancestry.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartAncestors(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Ancestors'), 'ancestry.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-pedigree');
	}

	/**
	 * Generate a menu item for the descendants chart (descendancy.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	public function menuChartDescendants(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Descendants'), 'descendancy.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-descendants');
	}

	/**
	 * Favorites menu.
	 *
	 * @return WT_Menu|null
	 */
	public function menuFavorites() {
		global $REQUIRE_AUTHENTICATION, $controller;

		$show_user_favs = Auth::check() && array_key_exists('user_favorites', WT_Module::getActiveModules());
		$show_gedc_favs = !$REQUIRE_AUTHENTICATION && array_key_exists('gedcom_favorites', WT_Module::getActiveModules());

		if ($show_user_favs && Auth::check()) {
			if ($show_gedc_favs && !$this->isSearchEngine()) {
				$favorites = array_merge(
					gedcom_favorites_WT_Module::getFavorites(WT_GED_ID),
					user_favorites_WT_Module::getFavorites(Auth::id())
				);
			} else {
				$favorites = user_favorites_WT_Module::getFavorites(Auth::id());
			}
		} else {
			if ($show_gedc_favs && !$this->isSearchEngine()) {
				$favorites = gedcom_favorites_WT_Module::getFavorites(WT_GED_ID);
			} else {
				return null;
			}
		}

		$menu = new WT_Menu(WT_I18N::translate('Favorites'), '#', 'menu-favorites');

		foreach ($favorites as $favorite) {
			switch ($favorite['type']) {
			case 'URL':
				$submenu = new WT_Menu($favorite['title'], $favorite['url']);
				$menu->addSubMenu($submenu);
				break;
			case 'INDI':
			case 'FAM':
			case 'SOUR':
			case 'OBJE':
			case 'NOTE':
				$obj = WT_GedcomRecord::getInstance($favorite['gid']);
				if ($obj && $obj->canShowName()) {
					$submenu = new WT_Menu($obj->getFullName(), $obj->getHtmlUrl());
					$menu->addSubMenu($submenu);
				}
				break;
			}
		}

		if ($show_user_favs) {
			if (isset($controller->record) && $controller->record instanceof WT_GedcomRecord) {
				$submenu = new WT_Menu(WT_I18N::translate('Add to favorites'), '#');
				$submenu->setOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'" . $controller->record->getXref() . "'},function(){location.reload();})");
				$menu->addSubMenu($submenu);
			}
		}

		return $menu;
	}

	/**
	 * @return WT_Menu
	 */
	public function menuHomePage() {
		$menu                = new WT_Menu(WT_I18N::translate('Home page'), 'index.php?ctype=gedcom&amp;' . $this->tree_url, 'menu-tree');
		$ALLOW_CHANGE_GEDCOM = WT_Site::getPreference('ALLOW_CHANGE_GEDCOM') && count(WT_Tree::getAll()) > 1;
		foreach (WT_Tree::getAll() as $tree) {
			if ($tree->tree_id === WT_GED_ID || $ALLOW_CHANGE_GEDCOM) {
				$submenu = new WT_Menu(
					$tree->tree_title_html,
					'index.php?ctype=gedcom&amp;ged=' . $tree->tree_name_url,
					'menu-tree-' . $tree->tree_id // Cannot use name - it must be a CSS identifier
				);
				$menu->addSubmenu($submenu);
			}
		}

		return $menu;
	}

	/**
	 * A menu to show a list of available languages.
	 *
	 * @return WT_Menu|null
	 */
	public function menuLanguages() {
		$menu = new WT_Menu(WT_I18N::translate('Language'), '#', 'menu-language');

		foreach (WT_I18N::installed_languages() as $lang => $name) {
			$submenu = new WT_Menu($name, get_query_url(array('lang' => $lang), '&amp;'), 'menu-language-' . $lang);
			if (WT_LOCALE === $lang) {
				$submenu->addClass('', '', 'lang-active');
			}
			$menu->addSubMenu($submenu);
		}

		if (count($menu->getSubmenus()) > 1 && !$this->isSearchEngine()) {
			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create a menu to show lists of individuals, families, sources, etc.
	 *
	 * @return WT_Menu|null
	 */
	public function menuLists() {
		global $controller;

		// The top level menu shows the individual list
		$menu = new WT_Menu(WT_I18N::translate('Lists'), 'indilist.php?' . $this->tree_url, 'menu-list');

		// Do not show empty lists
		$row = WT_DB::prepare(
			"SELECT SQL_CACHE" .
			" EXISTS(SELECT 1 FROM `##sources` WHERE s_file=?                  ) AS sour," .
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='REPO') AS repo," .
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='NOTE') AS note," .
			" EXISTS(SELECT 1 FROM `##media`   WHERE m_file=?                  ) AS obje"
		)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchOneRow();

		// Build a list of submenu items and then sort it in localized name order
		$surname_url = '&surname=' . rawurlencode($controller->getSignificantSurname()) . '&amp;' . $this->tree_url;

		$menulist = array(
			new WT_Menu(WT_I18N::translate('Individuals'), 'indilist.php?' . $this->tree_url . $surname_url, 'menu-list-indi'),
		);

		if (!$this->isSearchEngine()) {
			$menulist[] = new WT_Menu(WT_I18N::translate('Families'), 'famlist.php?' . $this->tree_url . $surname_url, 'menu-list-fam');
			$menulist[] = new WT_Menu(WT_I18N::translate('Branches'), 'branches.php?' . $this->tree_url . $surname_url, 'menu-branches');
			$menulist[] = new WT_Menu(WT_I18N::translate('Place hierarchy'), 'placelist.php?' . $this->tree_url, 'menu-list-plac');
			if ($row->obje) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Media objects'), 'medialist.php?' . $this->tree_url, 'menu-list-obje');
			}
			if ($row->repo) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Repositories'), 'repolist.php?' . $this->tree_url, 'menu-list-repo');
			}
			if ($row->sour) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Sources'), 'sourcelist.php?' . $this->tree_url, 'menu-list-sour');
			}
			if ($row->note) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Shared notes'), 'notelist.php?' . $this->tree_url, 'menu-list-note');
			}
		}
		uasort($menulist, function (WT_Menu $x, WT_Menu $y) {
				return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel());
			});

		$menu->setSubmenus($menulist);

		return $menu;
	}

	/**
	 * A login menu option (or null if we are already logged in).
	 *
	 * @return WT_Menu|null
	 */
	public function menuLogin() {
		global $SEARCH_SPIDER;

		if (Auth::check() || $SEARCH_SPIDER) {
			return null;
		} else {
			return new WT_Menu(WT_I18N::translate('Login'), WT_LOGIN_URL . '?url=' . rawurlencode(get_query_url()));
		}
	}

	/**
	 * A logout menu option (or null if we are already logged out).
	 *
	 * @return WT_Menu|null
	 */
	public function menuLogout() {
		if (Auth::check()) {
			return new WT_Menu(WT_I18N::translate('Logout'), 'logout.php');
		} else {
			return null;
		}
	}

	/**
	 * Get the additional menus created by each of the modules
	 *
	 * @return WT_Menu[]
	 */
	public function menuModules() {
		$menus = array();
		foreach (WT_Module::getActiveMenus() as $module) {
			$menu = $module->getMenu();
			if ($menu) {
				$menus[] = $menu;
			}
		}

		return $menus;
	}

	/**
	 * A link to allow users to edit their account settings.
	 *
	 * @return WT_Menu|null
	 */
	public function menuMyAccount() {
		if (Auth::check()) {
			return new WT_Menu(WT_Filter::escapeHtml(Auth::user()->getRealName()), 'edituser.php');
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu
	 */
	public function menuMyPage() {
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;

		$showFull   = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		if (!Auth::id()) {
			return null;
		}

		//-- main menu
		$menu = new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;' . $this->tree_url, 'menu-mymenu');

		//-- mypage submenu
		$submenu = new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;' . $this->tree_url, 'menu-mypage');
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (Auth::user()->getPreference('editaccount')) {
			$submenu = new WT_Menu(WT_I18N::translate('My account'), 'edituser.php', 'menu-myaccount');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new WT_Menu(
				WT_I18N::translate('My pedigree'),
				'pedigree.php?' . $this->tree_url . '&amp;rootid=' . WT_USER_GEDCOM_ID . "&amp;show_full={$showFull}&amp;talloffset={$showLayout}",
				'menu-mypedigree'
			);
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new WT_Menu(WT_I18N::translate('My individual record'), 'individual.php?pid=' . WT_USER_GEDCOM_ID . '&amp;' . $this->tree_url, 'menu-myrecord');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN) {
			//-- admin submenu
			$submenu = new WT_Menu(WT_I18N::translate('Administration'), 'admin.php', 'menu-admin');
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/**
	 * Create a pending changes menu.
	 *
	 * @return WT_Menu|null
	 */
	public function menuPendingChanges() {
		if ($this->pendingChangesExist()) {
			$menu = new WT_Menu(WT_I18N::translate('Pending changes'), '#', 'menu-pending');
			$menu->setOnclick('window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;');

			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu|null
	 */
	public function menuReports() {
		$active_reports = WT_Module::getActiveReports();
		if ($this->isSearchEngine() || !$active_reports) {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('Reports'), 'reportengine.php?' . $this->tree_url, 'menu-report');

		$sub_menu = false;
		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
				$sub_menu = true;
			}
		}

		if ($sub_menu && !$this->isSearchEngine()) {
			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu
	 */
	public function menuSearch() {
		if ($this->isSearchEngine()) {
			return null;
		}
		//-- main search menu item
		$menu = new WT_Menu(WT_I18N::translate('Search'), 'search.php?' . $this->tree_url, 'menu-search');
		//-- search_general sub menu
		$submenu = new WT_Menu(WT_I18N::translate('General search'), 'search.php?' . $this->tree_url, 'menu-search-general');
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new WT_Menu(/* I18N: search using “sounds like”, rather than exact spelling */
			WT_I18N::translate('Phonetic search'), 'search.php?' . $this->tree_url . '&amp;action=soundex', 'menu-search-soundex');
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new WT_Menu(WT_I18N::translate('Advanced search'), 'search_advanced.php?' . $this->tree_url, 'menu-search-advanced');
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Search and replace'), 'search.php?' . $this->tree_url . '&amp;action=replace', 'menu-search-replace');
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/**
	 * Themes menu.
	 *
	 * @return WT_Menu|null
	 */
	public function menuThemes() {
		if ($this->tree && !$this->isSearchEngine() && WT_Site::getPreference('ALLOW_USER_THEMES') && $this->tree->getPreference('ALLOW_THEME_DROPDOWN')) {
			$menu = new WT_Menu(WT_I18N::translate('Theme'), '#', 'menu-theme');
			foreach (Theme::themeNames() as $themename => $themedir) {
				$submenu = new WT_Menu($themename, get_query_url(array('theme' => $themedir), '&amp;'), 'menu-theme-' . $themedir);
				if (WT_THEME_DIR === 'themes/' . $themedir . '/') {
					$submenu->addClass('', '', 'theme-active');
				}
				$menu->addSubMenu($submenu);
			}

			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create the <link rel="canonical"> tag.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function metaCanonicalUrl($url) {
		return '<link rel="canonical" href="' . $url . '">';
	}

	/**
	 * Create the <meta charset=""> tag.
	 *
	 * @return string
	 */
	public function metaCharset() {
		return '<meta charset="UTF-8">';
	}

	/**
	 * Create the <meta name="description"> tag.
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public function metaDescription($description) {
		return '<meta name="description" content="' . $description . '">';
	}

	/**
	 * Create the <meta name="description"> tag.
	 *
	 * @param string $generator
	 *
	 * @return string
	 */
	public function metaGenerator($generator) {
		return '<meta meta name="generator" content="' . $generator . '">';
	}

	/**
	 * Create the <meta name="robots"> tag.
	 *
	 * @param string $robots
	 *
	 * @return string
	 */
	public function metaRobots($robots) {
		return '<meta name="robots" content="' . $robots . '">';
	}

	/**
	 * Create the <meta http-equiv="X-UA-Compatible"> tag.
	 *
	 * @return string
	 */
	public function metaUaCompatible() {
		return '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	}

	/**
	 * Are there any pending changes for us to approve?
	 *
	 * @return bool
	 */
	public function pendingChangesExist() {
		return exists_pending_change(Auth::user(), $this->tree);
	}

	/**
	 * Create a pending changes link.
	 *
	 * @return string
	 */
	public function pendingChangesLink() {
		return
			'<a href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;">' .
			$this->pendingChangesLinkText() .
			'</a>';
	}

	/**
	 * Text to use in the pending changes link.
	 *
	 * @return string
	 */
	public function pendingChangesLinkText() {
		return WT_I18N::translate('There are pending changes for you to moderate.');
	}

	/**
	 * Send any HTTP headers.
	 *
	 * @return void
	 */
	public function sendHeaders() {
		header('Content-Type: text/html; charset=UTF-8');
	}

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	public function stylesheets() {
		return array();
	}

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	abstract public function themeId();

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	abstract public function themeName();

	/**
	 * Create the <title> tag.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function title($title) {
		return '<title>' . WT_Filter::escapeHtml($title) . '</title>';
	}
}
