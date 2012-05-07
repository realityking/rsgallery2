<?php
/**
 * @version		$Id: gpagination.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		RSgallery2 Component
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('JPATH_BASE') or die();
jimport("joomla.html.pagination");
/**
 * Gallery pagination Class.  Provides a common interface for gallery pagination for the
 * RSGallery2 components
 * Inheriting JPagination from the Joomla Framework
 *
 * @package 	RSGallery2
 * @since		2.0
 */
class JGPagination extends JPagination
{

	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @access	public
	 * @return	string	Pagination page list string
	 * @since	1.0
	 */
	public function getPagesLinks()
	{
		
		$appl = JFactory::getApplication();

		$lang =& JFactory::getLanguage();

		// Build the page navigation list
		$data = $this->_buildDataObject();

		$list = array();

		$itemOverride = false;
		$listOverride = false;

		$chromePath = JPATH_THEMES . '/' . $appl->getTemplate() . '/html/gpagination.php';
		if (file_exists($chromePath))
		{
			require_once $chromePath;
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
				$itemOverride = true;
			}
			if (function_exists('pagination_list_render')) {
				$listOverride = true;
			}
		}

		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = ($itemOverride) ? pagination_item_active($data->all) : $this->_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = ($itemOverride) ? pagination_item_active($data->start) : $this->_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
		}
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_active($data->previous) : $this->_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_active($page) : $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page) : $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = ($itemOverride) ? pagination_item_active($data->next) : $this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
		}
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = ($itemOverride) ? pagination_item_active($data->end) : $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
		}

		if($this->total > $this->limit){
			return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
		}
		else{
			return '';
		}
		
	}

	/**
	 * Return the pagination footer
	 *
	 * @access	public
	 * @return	string	Pagination footer
	 * @since	1.0
	 */
	public function getListFooter()
	{
		$appl = JFactory::getApplication();

		$list = array();
		$list['limit']			= $this->limit;
		$list['limitstart']		= $this->limitstart;
		$list['total']			= $this->total;
		$list['limitfield']		= $this->getLimitBox();
		$list['pagescounter']	= $this->getPagesCounter();
		$list['pageslinks']		= $this->getPagesLinks();

		$chromePath		= JPATH_THEMES . '/' . $appl->getTemplate() . '/html/gpagination.php';
		if (file_exists( $chromePath ))
		{
			require_once $chromePath;
			if (function_exists( 'pagination_list_footer' )) {
				return pagination_list_footer( $list );
			}
		}
		return $this->_list_footer($list);
	}

	/**
	 * Creates a dropdown box for selecting how many records to show per page
	 *
	 * @access	public
	 * @return	string	The html for the limit # input box
	 * @since	1.0
	 */
	public function getLimitBox()
	{
		$appl = JFactory::getApplication();

		// Initialize variables
		$limits = array ();

		// Make the option list
		for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = JHtml::_('select.option', "$i");
		}
		$limits[] = JHtml::_('select.option', '50');
		$limits[] = JHtml::_('select.option', '100');
		$limits[] = JHtml::_('select.option', '0', JText::_('all'));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list
		if ($appl->isAdmin()) {
			$html = JHtml::_('select.genericlist',  $limits, 'limitg', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $selected);
		} else {
			$html = JHtml::_('select.genericlist',  $limits, 'limitg', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}


	public function _list_render($list)
	{
		// Initialize variables
		$html = null;

		// Reverse output rendering for right-to-left display
		$html .= '&lt;&lt; ';
		$html .= $list['start']['data'];
		$html .= ' &lt; ';
		$html .= $list['previous']['data'];
		foreach( $list['pages'] as $page ) {
			$html .= ' '.$page['data'];
		}
		$html .= ' '. $list['next']['data'];
		$html .= ' &gt;';
		$html .= ' '. $list['end']['data'];
		$html .= ' &gt;&gt;';

		return $html;
	}

	public function _item_active(&$item)
	{
		$appl = JFactory::getApplication();
		if ($appl->isAdmin())
		{
			if($item->base>0)
				return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstartg.value=".$item->base."; submitform();return false;\">".$item->text."</a>";
			else
				return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstartg.value=0; submitform();return false;\">".$item->text."</a>";
		} else {
			return "<a title=\"".$item->text."\" href=\"".$item->link."\" class=\"pagenav\">".$item->text."</a>";
		}
	}

	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	public function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new JPaginationObject(JText::_('View All'));
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= JRoute::_("&limitstartg=");
		}

		// Set the start and previous data objects
		$data->start	= new JPaginationObject(JText::_('Start'));
		$data->previous	= new JPaginationObject(JText::_('Prev'));

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') - 2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= JRoute::_("&limitstartg=");
			$data->previous->base	= $page;
			$data->previous->link	= JRoute::_("&limitstartg=".$page);
		}

		// Set the next and end data objects
		$data->next	= new JPaginationObject(JText::_('Next'));
		$data->end	= new JPaginationObject(JText::_('End'));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= JRoute::_("&limitstartg=".$next);
			$data->end->base	= $end;
			$data->end->link	= JRoute::_("&limitstartg=".$end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new JPaginationObject($i);
			if ($i != $this->get('pages.current') || $this->_viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= JRoute::_("&limitstartg=".$offset);
			}
		}
		return $data;
	}
}
