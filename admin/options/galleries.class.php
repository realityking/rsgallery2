<?php
/**
* category class
* @version $Id: galleries.class.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Category database table class
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgGalleriesItem extends JTable {
    /** @var int Primary key */
    var $id = null;
    var $parent = 0;
    var $name = null;
	var $alias = null;
    var $description = null;
    var $published = null;
    var $checked_out        = null;
    var $checked_out_time   = null;
    var $ordering = null;
    var $hits = null;
    var $date = null;
    var $params = null;
    var $user = null;
    var $uid = null;
    var $allowed = null;
    var $thumb_id = null;

    /**
    * @param database A database connector object
    */
    function rsgGalleriesItem( &$db ) {
		parent::__construct('#__rsgallery2_galleries', 'id', $db );
    }
    /** 
     * overloaded check function 
     */
    function check() {
        // filter malicious code
        $ignoreList = array( 'params','description' );
		
		$ignore = is_array( $ignoreList );
		
		$filter = & JFilterInput::getInstance();
		foreach ($this->getProperties() as $k => $v)
		{
			if ($ignore && in_array( $k, $ignoreList ) ) {
				continue;
			}
			$this->$k = $filter->clean( $this->$k );
		}
		
        /** check for valid name */
        if (trim( $this->name ) == '') {
            $this->_error = JText::_('Gallery name');
            return false;
        }

        /** check for existing name */
        $query = "SELECT id"
        . "\n FROM #__rsgallery2_galleries"
        . "\n WHERE name = '".$this->name."'"
        . "\n AND parent = ".$this->parent
        ;
        $this->_db->setQuery( $query );

        $xid = intval( $this->_db->loadResult() );
        if ($xid && $xid != intval( $this->id )) {
            $this->_error = JText::_('There is a gallery already with that name, please try again.');
            return false;
        }
        return true;
    }
}

/**
 * build the select list for parent item
 * ripped from joomla.php: mosAdminMenus::Parent()
 * @param row current gallery
 * @return HTML Selectlist
 */
function galleryParentSelectList( &$row ) {
    $database =& JFactory::getDBO();

    $id = '';
    if ( $row->id ) {
        $id = " AND id != $row->id";
    }

    // get a list of the menu items
    // excluding the current menu item and its child elements
    $query = "SELECT *"
    . " FROM #__rsgallery2_galleries"
    . " WHERE published != -2"
    . $id
    . " ORDER BY parent, ordering"
    ;
    $database->setQuery( $query );
    
    $mitems = $database->loadObjectList();

    // establish the hierarchy of the menu
    $children = array();

    if ( $mitems ) {
        // first pass - collect children
        foreach ( $mitems as $v ) {
            $pt     = $v->parent;
            $list   = @$children[$pt] ? $children[$pt] : array();
            array_push( $list, $v );
            $children[$pt] = $list;
        }
    }

    // second pass - get an indent list of the items
    $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

    // assemble menu items to the array
    $mitems     = array();
    $mitems[]   = JHTMLSelect::option( '0', JText::_('Top Gallery') );

    foreach ( $list as $item ) {
        $mitems[] = JHTMLSelect::option( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
    }

    $output = JHTML::_("select.genericlist", $mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent );

    return $output;
}
/* ACL functions from here */
/**
 * Returns an array with the gallery ID's from the children of the parent
 * @param int Gallery ID from the parent ID to check
 * @return array Array with Gallery ID's from children
 */
function subList( $gallery_id ) {
	$database =& JFactory::getDBO();
	$sql = "SELECT id FROM #__rsgallery2_galleries WHERE parent = '$gallery_id'";
	$database->setQuery( $sql );
	$result = $database->loadResultArray();
	if (count($result) > 0)
		return result;
	else
		return 0;
}
?>