<?php
/**
 * @version		$Id: view.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * RSGallery2 Template Manager Install View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */

include_once(dirname(dirname(__FILE__)) . '/default/view.php');

class InstallerViewInstall extends InstallerViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::help( 'screen.installer' );

		$paths = new stdClass();
		$paths->first = '';

		$this->assignRef('paths', $paths);
		$this->assignRef('state', $this->get('state'));

		parent::showHeader();
		parent::display($tpl);
	}

}
