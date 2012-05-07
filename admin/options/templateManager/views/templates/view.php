<?php
/**
 * @version		$Id: view.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		RSGallery2
 * @subpackage	Template installer
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * RSGallery2 Template Manager Templates View
 *
 * @package		RSGallery2
 * @subpackage	Template installer
 * @since		1.5
 */

include_once(dirname(dirname(__FILE__)) . '/default/view.php');

class InstallerViewTemplates extends InstallerViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::makeDefault( 'setDefault' );
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList( '', 'remove' );
		JToolBarHelper::editList( 'template' );
		JToolBarHelper::help( 'screen.installer2' );

		// Get data from the model
		$items		= &$this->get('Items');
		$pagination	= &$this->get('Pagination');

		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::showHeader();
		parent::display($tpl);
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->id		= $item->directory;
		$item->index	= $index;

		if ($item->isDisabled) {
			$item->cbd		= 'disabled';
			$item->style	= 'style="color:#999999;"';
		} else {
			$item->cbd		= null;
			$item->style	= null;
		}
		$item->author_information = @$item->authorEmail .'<br />'. @$item->authorUrl;

		$this->assignRef('item', $item);
	}
}
