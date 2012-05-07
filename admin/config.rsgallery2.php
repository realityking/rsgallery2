<?php
/**
* This file handles configuration processing for RSGallery.
*
* @version $Id: config.rsgallery2.php 1024 2011-05-04 10:53:51Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
**/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


/**
 * Class with util functions for RSGallery2
 * @package RSGallery2
 */
class galleryUtils {

    /**
     * shows proper Joomla path
     * contributed by Jeckel
	 * Deprecated? It seems that galleryUtils::showRSPath is not used in v2.1.0 anymore.
     */
    function showRSPath($catid, $imgid = 0){
        global $mainframe, $database;
    
        if ($catid != 0) {
            $database->setQuery('SELECT * FROM #__rsgallery2_galleries WHERE id = "'. $catid . '"');
            $rows = $database->loadObjectList();

            $cat = $rows[0];
            $cats = array();
            array_push($cats, $cat);
            
            while ($cat->parent != 0) {
                $database->setQuery('SELECT * FROM #__rsgallery2_galleries WHERE id = "' . $cat->parent . '"');
                $rows = $database->loadObjectList();
                $cat = $rows[0];
                array_unshift($cats, $cat);
            }    // while
            
            reset($cats);
            foreach($cats as $cat) {
                if ($cat->id == $catid && empty($imgid)) {
                    $mainframe->appendPathWay($cat->name);
                } else {
					$mainframe->appendPathWay('<a href="' .JRoute::_('index.php?option=com_rsgallery2&catid=' . $cat->id ). '">' . $cat->name . '</a>');
                }    // if
            }    // foreach
        }    // if
        
        if (!empty($imgid)) {
            $database->setQuery('SELECT title FROM #__rsgallery2_files WHERE id = "'. $imgid . '"');
            $imgTitle = $database->loadResult();
            $mainframe->appendPathWay($imgTitle);
        }    // if
        
    }

    /**
     * Shows random images for display on main page
     */
    function showRandom() {
    $database =& JFactory::getDBO();

    $database->setQuery("SELECT file.gallery_id, file.ordering, file.id, file.name, file.descr".
                        " FROM #__rsgallery2_files file, #__rsgallery2_galleries gal".
                        " WHERE file.gallery_id=gal.id and gal.published=1".
                        " ORDER BY rand() limit 3");
    $rows = $database->loadObjectList();

    HTML_RSGALLERY::showRandom($rows);
    }

    /**
     * Shows latest uploaded images for display on main page
     */
    function showLatest() {
    $database =& JFactory::getDBO();
    
    $database->setQuery("SELECT file.gallery_id, file.ordering, file.id, file.name, file.descr".
                        " FROM #__rsgallery2_files file, #__rsgallery2_galleries gal".
                        " WHERE file.gallery_id=gal.id and gal.published=1".
                        " ORDER BY file.date DESC limit 3");
    $rows = $database->loadObjectList();
    
    HTML_RSGALLERY::showLatest($rows);
    }
    
    /**
     * Shows a dropdownlist with all categories, owned by the logged in user
     * @param int Category ID to show the current category selected. Defaults to 0.
     * @param int User ID of the owner of the gallery
     * @param string Name of select form element
     * @return string HTML representation of dropdown box
     * @todo Make all categories visible if user is Super Administrator
     */
    function showCategories($s_id = 0, $uid, $selectname = 'i_cat') {
	global $dropdown_html;
	$database =& JFactory::getDBO();
	$database->setQuery("SELECT * FROM #__rsgallery2_galleries WHERE parent = '0' AND uid = '$uid' ORDER BY ordering ASC");
	$rows = $database->loadObjectList();
	$dropdown_html = "<select name=\"$selectname\"><option value=\"0\" SELECTED>".JText::_('- Select gallery -')."</option>\n";

    foreach ($rows as $row)
		{
		$id = $row->id;
		$database->setQuery("SELECT * FROM #__rsgallery2_galleries WHERE parent = '$id' AND uid = '$uid' ORDER BY ordering ASC");
		$rows2 = $database->loadObjectList();

		if (!isset($s_id))
			{
			$s_id=0;
			}
        $dropdown_html .= "<option value=\"$row->id\"";
        if ($row->id == $s_id)
            $dropdown_html .= " SELECTED>";
        else
            $dropdown_html .= ">";
        $dropdown_html .=  $row->name."</option>\n";

		foreach($rows2 as $row2)
			{
            $dropdown_html .= "<option value=\"$row2->id\">-->$row2->name</option>\n";
			}
		}
        echo $dropdown_html."</select>";
	}
    
	/**
	 * Show gallery select list according to the permissions of the logged in user
	 * @param string Action type
	 * @param string Name of the select box, defaults to 'catid'
	 * @param integer ID of selected gallery
	 * @return HTML to show selectbox
	 */
	function showUserGalSelectList($action = '', $select_name = 'catid', $gallery_id = null, $js = '') {
		global $rsgAccess;
		
		//Get gallery Id's where action is permitted and write to string
		$galleries = $rsgAccess->actionPermitted($action);
		
		$dropdown_html = "<select name=\"$select_name\" $js><option value=\"0\" SELECTED>".JText::_('- Select gallery -')."</option>\n";
		$dropdown_html .= galleryUtils::addToGalSelectList(0, 0, $gallery_id, $galleries);
		echo $dropdown_html."</select>";
	}

	/**
	 * Add galleries to the gallery select list according to the permissions of the logged in user
	 * @param level in gallery tree
	 * @param integer ID of current node in gallery tree
	 * @param integer ID of selected gallery
	 * @param list of permitted galleries
	 * @return HTML to add
	 */
	function addToGalSelectList($level, $galid, $gallery_id, $galleries) {
		// provided by Klaas on Dec.13.2007
		$database = JFactory::getDBO();		
		
		$dropdown_html = "";
		$database->setQuery("SELECT * FROM #__rsgallery2_galleries WHERE parent = '$galid' ORDER BY ordering ASC");
		$rows = $database->loadObjectList();
		foreach ($rows as $row) {
			$dropdown_html .= "<option value=\"$row->id\"";
			// Disable when action not allowed or user not owner
			if (!in_array($row->id, $galleries))
				$dropdown_html .= " DISABLED";

			if ($row->id == $gallery_id)
				$dropdown_html .= " SELECTED";
			
			$dropdown_html .= " >";
			$indent = "";
			for ($i = 0; $i < $level; $i++) {
				$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if ($level)
				$indent .= "|--&nbsp;";
			$dropdown_html .=  $indent.$row->name."</option>\n";
			$dropdown_html .=  galleryUtils::addToGalSelectList($level + 1, $row->id, $gallery_id, $galleries);
		}
		return $dropdown_html;
	}
	
    /**
     * build the select list to choose a parent gallery for a specific user
     * @param int current gallery id
     * @param string selectbox name
     * @param boolean Dropdown(false) or Liststyle(true)
     * @return string HTML representation for selectlist
     */
    function createGalSelectList( $galleryid=null, $listName='galleryid', $style = true ) {
    $database = JFactory::getDBO();
	$my =& JFactory::getUser();
    $my_id = $my->id;
    if ($style == true)
        $size = ' size="10"';
    else
        $size = ' size="1"';
    // get a list of the menu items
    // excluding the current menu item and its child elements
    $query = "SELECT *"
    . " FROM #__rsgallery2_galleries"
    . " WHERE published != -2"
    . " AND uid = '$my_id'"
    . " ORDER BY parent, ordering";
    
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
    $mitems[]   = JHTML::_("Select.option", '0', JText::_('Top'));

    foreach ( $list as $item ) {
        $mitems[] = JHTML::_("Select.option", $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
    }

    $output = JHTML::_("select.genericlist", $mitems, $listName, 'class="inputbox"'.$size, 'value', 'text', $galleryid );

    echo $output;
}

    
    /**
     * build the select list to choose a gallery
     * based on options/galleries.class.php:galleryParentSelectList()
     * @param int current gallery id
     * @param string selectbox name
     * @param boolean Dropdown(false) or Liststyle(true)
     * @param string javascript entries ( e.g: 'onChange="form.submit();"' )
     * @return string HTML representation for selectlist
     */
    function galleriesSelectList( $galleryid=null, $listName='gallery_id', $style = true, $javascript = NULL ) {
    $database =& JFactory::getDBO();
    if ($style == true)
        $size = ' size="10"';
    else
        $size = ' size="1"';
    // get a list of the menu items
    // excluding the current menu item and its child elements
    $query = "SELECT *"
    . " FROM #__rsgallery2_galleries"
    . " WHERE published != -2"
    . " ORDER BY parent, ordering";
    
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
    $mitems[] 	= JHTML::_("Select.option", '-1', JText::_('Select Gallery') );
    $mitems[] 	= JHTML::_("Select.option", '0', '- Top Gallery -' );

    foreach ( $list as $item ) {
        $mitems[] = JHTML::_("Select.option", $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
    }

    $output = JHTML::_("select.genericlist", $mitems, $listName, 'class="inputbox"'.$size.' '.$javascript, 'value', 'text', $galleryid, false );

    return $output;
}

    /**
     * Retrieves the thumbnail image. presented in the category overview
     * @param int Category id
     * @param int image height
     * @param int image width
     * @param string Class name to format thumb view in css files
     * @return string html tag, showing the thumbnail
     * @todo being depreciated in favor of $rsgGallery->thumb() and $rsgDisplay functions
     */
     
    function getThumb($catid, $height = 0, $width = 0,$class = "") {
	    global $mainframe ;
		$database = JFactory::getDBO();
	    
	    //Setting attributes for image tag
	    $imgatt="";
	    if ($height > 0) 		$imgatt .= " height=\"$height\" ";
	    if ($width > 0)  		$imgatt .=" width=\"$width\" ";
	    if ($class != "")
	    	$imgatt .=" class=\"$class\" ";
	    else
	        $imgatt.=" class=\"rsg2-galleryList-thumb\" ";
	    //If no thumb, show default image.
	    if ( galleryUtils::getFileCount($catid) == 0 ) {
	        $thumb_html = "<img $imgatt src=\"".JURI_SITE."/components/com_rsgallery2/images/no_pics.gif\" alt=\"No pictures in gallery\" />";
	    } else {
	    	//Select thumb setting for specific gallery("Random" or "Specific thumb")
	        $sql = "SELECT thumb_id FROM #__rsgallery2_galleries WHERE id = '$catid'";
	        $database->setQuery($sql);
	        $thumb_id = $database->loadResult();
	        $list = galleryUtils::getChildList( $catid );
	        if ( $thumb_id == 0 ) {
	            //Random thumbnail
	            $sql = "SELECT name FROM #__rsgallery2_files WHERE gallery_id IN ($list) AND published=1 ORDER BY rand() LIMIT 1";
	            $database->setQuery($sql);
	            $thumb_name = $database->loadResult();
	        } else {
	            //Specific thumbnail
	            $thumb_name = galleryUtils::getFileNameFromId($thumb_id);
	        }
	        $thumb_html = "<img $imgatt src=\"".imgUtils::getImgThumb($thumb_name)."\" alt=\"\" />";
	    }
	    return $thumb_html;
    }
    
    /**
     * Returns number of files within a specific gallery and it's children
     * @deprecated use rsgGallery->itemCount() instead.
     * @param int Category id
     * @return int Number of files in category
     */
    function getFileCount($id) {
        $database =& JFactory::getDBO();
        $list = galleryUtils::getChildList( $id );
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE gallery_id IN ($list)");
        $count = $database->loadResult();
        return $count;
    }
        
    /**
     * Retrieves category name, based on the category id
     * @param integer The ID of the currently selected category
     * @return string Category Name
     */
    function getCatnameFromId($id)
        {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT name FROM #__rsgallery2_galleries WHERE id = '$id'");
        $catname = $database->loadResult();
        return $catname;
        }
     
    /**
     * Retrieves category ID, based on the filename id
     * @param integer The ID of the currently selected file
     * @return string Category ID
     */
    function getCatIdFromFileId($id)
        {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT gallery_id FROM #__rsgallery2_files WHERE id = '$id'");
        $gallery_id = $database->loadResult();
        return $gallery_id;
        }
        
     /**
      * Retrieves filename, based on the filename id
      * @param integer The ID of the currently selected file
      * @return string Filename
      */    
     function getFileNameFromId($id)
        {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT name FROM #__rsgallery2_files WHERE id = '$id'");
        $filename = $database->loadResult();
        return $filename;
        }
    
    /**
      * Retrieves title, based on the filename id
      * @param integer The ID of the currently selected file
      * @return string title
      */    
     function getTitleFromId($id)
        {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT title FROM #__rsgallery2_files WHERE id = '$id'");
        $title = $database->loadResult();
        return $title;
        }
    
    /**
     * Returns parent ID from chosen gallery
     * @param int Gallery ID
     * @return int Parent ID
     */
     function getParentId($gallery_id) {
     	$database =& JFactory::getDBO();
     	$sql = "SELECT parent FROM #__rsgallery2_galleries WHERE id = '$gallery_id'";
     	$database->setQuery($sql);
     	$parent = $database->loadResult();
     	return $parent;
     }  
      
    /**
     * Creates new thumbnails with new settings
     * @param Category ID
     */
    function regenerateThumbs ($catid = NULL) {
    global $database, $rsgConfig;
    $i = 0;
    $files  = mosReadDirectory( JPATH_ROOT.$rsgConfig->get('imgPath_original') );
    //check if size is changed
    foreach ($files as $file)
        {
        if ( imgUtils::makeThumbImage( JPATH_ROOT.$rsgConfig->get('imgPath_original').$file ) )
            continue;
        else
            $error[] = $file;
            $i++;
        }
    }
    
    function addHit($id)
        {
        $database =& JFactory::getDBO();
        //Get hits from DB
        $database->setQuery("SELECT hits FROM #__rsgallery2_files WHERE id = '$id'");
        $hits = $database->loadResult();
        $hits++;
        $database->setQuery("UPDATE #__rsgallery2_files SET hits = '$hits' WHERE id = '$id'");
        if ($database->query())
            {
            return(1);//OK
            }
        else
            {
            return(0);//Not OK
            }
        }
    
    function addCatHit($hid)
        {
        $database =& JFactory::getDBO();
        //Get hits from DB
        $database->setQuery("SELECT hits FROM #__rsgallery2_galleries WHERE id = '$hid'");
        $hits = $database->loadResult();
        $hits++;
        $database->setQuery("UPDATE #__rsgallery2_galleries SET hits = '$hits' WHERE id = '$hid'");
        if ($database->query())
            {
            return(1);//OK
            }
        else
            {
            return(0);//Not OK
            }
        }
        
    function showRating($id) {
        $database = JFactory::getDBO();
        $database->setQuery("SELECT * FROM #__rsgallery2_files WHERE id = '$id'");
        $values = array(JText::_('No rating'),JText::_('&nbsp;Very Bad&nbsp;'),JText::_('&nbsp;Bad&nbsp;'),JText::_('&nbsp;Ok&nbsp;'),JText::_('&nbsp;Good&nbsp;'),JText::_('&nbsp;Very Good&nbsp;'));
        $rows = $database->loadObjectList();
        $images = "";
        foreach ($rows as $row)
            {
            $average = $row->rating/$row->votes;
            $average1 = round($average);
            for ($t = 1; $t <= $average1; $t++)
                {
                $images .= "<img src=\"JURI_SITE/images/M_images/rating_star.png\">&nbsp;";
                }
            }
            return $images;
        }
        
	/**
	 * @depreciated use rsgGallery->hasNewImages() instead;
	 */
    function newImages($xid) {
    $database =& JFactory::getDBO();
    $lastweek  = mktime (0, 0, 0, date("m"),    date("d") - 7, date("Y"));
    $lastweek = date("Y-m-d H:m:s",$lastweek);
    $database->setQuery("SELECT * FROM #__rsgallery2_files WHERE date >= '$lastweek' AND published=1 AND gallery_id = '$xid'");
    $rows = $database->loadObjectList();
    if (count($rows) > 0)
        {
        foreach ($rows as $row)
            {
            $gallery_id = $row->gallery_id;
            if ($gallery_id == $xid)
                {
                echo JText::_('New!');
                break;
                }
            }
        }
    else
        {
        echo "";
        }
    }
    
    /**
     * This function will retrieve the user Id's of the owner of this gallery.
     * @param integer id of category
     * @return the requested user id
     */
    function getUID($catid) {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT uid FROM #__rsgallery2_galleries WHERE id = '$catid'");
        $uid = $database->loadResult();
        return $uid;
        }
        
    /**
     * This function returns the number of created galleries by the logged in user
     * @param integer user ID
     * @return integer number of created categories
     */
    function userCategoryTotal($id) {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_galleries WHERE uid = '$id'");
        $cats = $database->loadResult();
        return $cats;
        }
    
    /**
     * This function returns the number of uploaded images  by the logged in user
     * @param integer user ID
     * @return integer number of uploaded images
     */
    function userImageTotal($id) {
        $database =& JFactory::getDBO();
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE userid = '$id'");
        $result = $database->loadResult();
        return $result;
        }
        
    /**
     * This function returns the number of uploaded images  by the logged in user
     * @param integer user ID
     * @return integer number of uploaded images
     */
    function latestCats() {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();

    $database->setQuery("SELECT * FROM #__rsgallery2_galleries ORDER BY id DESC LIMIT 0,5");
    $rows = $database->loadObjectList();
    if (count($rows) > 0)
            {
            foreach ($rows as $row)
                {
                ?>
                <tr>
                    <td><?php echo $row->name;?></td>
                    <td><?php echo galleryUtils::genericGetUsername($row->uid);?></td>
                    <td><?php echo $row->id;?></td>
                </tr>
                <?php
                }
            }
        else
            {
            echo "<tr><td colspan=\"3\">".JText::_('No new entries')."</td></tr>";
            }
    }
    
    /**
     * This function will retrieve the user name based on the user id
     * @param integer user id
     * @return the username
     * @todo isn't there a joomla function for this?
     */
    function genericGetUsername($uid) {
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		global $name;

        $database->setQuery("SELECT username FROM #__users WHERE id = '$uid'");
        $name = $database->loadResult();
        
        return $name;
        }
        
    /**
     * This function will show the 5 last uploaded images
     */    
    function latestImages() {
    	global $rows;
		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		
    $lastweek  = mktime (0, 0, 0, date("m"),    date("d") - 7, date("Y"));
    $lastweek = date("Y-m-d H:m:s",$lastweek);
    $database->setQuery("SELECT * FROM #__rsgallery2_files WHERE date >= '$lastweek' and published=1 ORDER BY id DESC LIMIT 0,5");
    $rows = $database->loadObjectList();
    if (count($rows) > 0)
        {
        foreach ($rows as $row)
            {
            ?>
            <tr>
                <td><?php echo $row->name;?></td>
                <td><?php echo galleryUtils::getCatnameFromId($row->gallery_id);?></td>
                <td><?php echo $row->date;?></td>
                <td><?php echo galleryUtils::genericGetUsername($row->userid);?></td>
            </tr>
            <?php
            }
        }
    else
        {
        echo "<tr><td colspan=\"4\">".JText::_('No new entries')."</td></tr>";
        }
    }
    
    /**
     * replaces spaces with underscores
     * replaces other weird characters with dashes
     * @param string input text
     * @return cleaned up text
    **/
    function replaceStrangeChar($text){
        $text = str_replace(" ", "_", $text);
        $text = preg_replace('/[^a-z0-9_\-\.]/i', '_', $text);
        return $text;
    }
    
    /**
     * Retrieves file ID based on the filename
     * @param string filename
     * @return integer File ID
     */
    function getFileIdFromName($filename) {
    	$database =& JFactory::getDBO();
        $sql = "SELECT id FROM #__rsgallery2_files WHERE name = '$filename'";
        $database->setQuery($sql);
        $id = $database->loadResult();
        return $id;
    }
    
    /**
     * !!!!!!!!!!!!!!!!!!!! DUPLICATE FUNCTION !!!!!!!!!!!!!!!!!!!!!!
     * This is a duplicate from admin.rsgallery2.php
     * This is called both from the front- and backend. Better to put here I think
     * For now it remains in both admin.rsgallery2.php and config.rsgallery2.php
    */
    function reorderRSGallery ($tbl, $where = NULL ) {
       // reorders either the categories or images within a category
       // it is necessary to call this whenever a shuffle or deletion is performed
       $database =& JFactory::getDBO();
    
       $database->setQuery( "SELECT id, ordering FROM $tbl"
          . ($where ? "\nWHERE $where" : '')
          . "\nORDER BY ordering"
          );
       if (!($rows = $database->loadObjectList())) {
          return false;
       }
       // first pass, compact the ordering numbers
       $n=count( $rows );
    
       for ($i=0;  $i < $n; $i++) {
         $rows[$i]->ordering = $i+1;
         $database->setQuery( "UPDATE $tbl"
            . "\nSET ordering='".$rows[$i]->ordering."' WHERE id ='".$rows[$i]->id."'"
            );
         $database->query();
       }
       return true;
    }
    /**
     * Functions shows a warning box above the control panel is something is preventing
     * RSGallery2 from functioning properly
     */
    function writeWarningBox() {
    	global  $rsgConfig;
    	require_once(JPATH_RSGALLERY2_ADMIN.'/includes/img.utils.php');
    	//Detect image libraries
    	$html = '';
    	$count = 0;
		if ( ( !GD2::detect() ) and (!imageMagick::detect() ) and (!Netpbm::detect() ) ) {
  			$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"".JURI_SITE."/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;".JText::_('NO_IMGLIBRARY')."</p>";
		}
		
		//Check availability and writability of folders
		$folders = array(
			$rsgConfig->get('imgPath_display'),
			$rsgConfig->get('imgPath_thumb'),
			$rsgConfig->get('imgPath_original'),
			'/images/rsgallery',
			'/media'
			);
		foreach ($folders as $folder) {
			if (file_exists(JPATH_ROOT.$folder) && is_dir(JPATH_ROOT.$folder) )
				{
				$perms = substr(sprintf('%o', fileperms(JPATH_ROOT.$folder)), -4);
				if (!is_writable(JPATH_ROOT.$folder) )
					$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"".JURI_SITE."/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;<strong>".JPATH_ROOT.$folder."</strong> ".JText::_('is NOT writable!')."($perms)";
				}
			else
				{
				$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"".JURI_SITE."/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;<strong>".JPATH_ROOT.$folder."</strong> ".JText::_('FOLDER_NOTEXIST');	
				}
		}
		if ($html !== '') {
			?>
			<div style="clear: both; margin: 3px; margin-top: 10px; padding: 5px 15px; display: block; float: left; border: 1px solid #cc0000; background: #ffffcc; text-align: left; width: 50%;">
			<p style="color: #CC0000;"><?php echo JText::_('The following settings prevent RSGallery2 from working without errors:')?></p>
			<?php echo $html;?>
			<p style="color: #CC0000;text-align:right;"><a href="index2.php?option=com_rsgallery2"><?php echo JText::_('Refresh')?></a></p>		
			</div>
			<div class='rsg2-clr'>&nbsp;</div>		
			<?php
		}
	}
	
	/**
	 * Write downloadlink for image
	 * @param int image ID
	 * @param string Button or HTML link (button/link)
	 * @return HTML for downloadlink
	 */
	 function writeDownloadLink($id, $showtext = true, $type = 'button') {
	 	global $mainframe;
	 	echo "<div class=\"rsg2-toolbar\">";
	 	if ($type == 'button')
	 		{
	 		?>
	 		<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&task=downloadfile&id='.$id);?>">
	 		<img height="20" width="20" src="<?php echo JURI_SITE;?>/administrator/images/download_f2.png" alt="<?php echo JText::_('Download')?>">
	 		<?php
	 		if ($showtext == true) {
	 			?>
	 			<br /><span style="font-size:smaller;"><?php echo JText::_('Download')?></span>
	 			<?php
	 		}
	 		?>
	 		</a>
	 		<?php
	 		}
	 	else
	 		{
	 		?>
	 		<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&task=downloadfile&id='.$id);?>"><?php echo JText::_('Download')?></a>
	 		<?php
	 		}
	 echo "</div><div class=\"rsg2-clr\">&nbsp;</div>";
	 }
	 
	function writeGalleryStatus( $gallery ) {
		global $rsgConfig, $mainframe, $rsgAccess;
		$my =& JFactory::getUser();
		
		// return if status is not displayed
		if ( !$rsgConfig->get('displayStatus') )
			return;
		
		$owner = JHTML::tooltip(JText::_('You are the owner of this gallery'), 
				null, 
				'../../../components/com_rsgallery2/images/status_owner.png',null,null,0);
		$upload = JHTML::tooltip(JText::_('You can upload in this gallery'), 
				null, 
				'../../../components/com_rsgallery2/images/status_upload.png',null,null,0);
		
		$unpublished = JHTML::tooltip(JText::_('This gallery is NOT published'), 
				null, 
				'../../../components/com_rsgallery2/images/status_hidden.png',null,null,0);

		$html = "";
	
		$uid 		= $gallery->uid;
		$published 	= $gallery->published;

		//Check if user is owner of the gallery
		if ( $gallery->uid == $my->id )
			$html .= $owner;
		
		//Check if gallery is published
		if ($gallery->published == 0)
			$html .= $unpublished;
		
		if ( $rsgAccess->checkGallery('up_mod_img', $gallery->id) )
			$html .= $upload;

		return $html;
	}

	 function getChildList( $gallery_id ) {
	 	$database =& JFactory::getDBO();
	 	$array[] = $gallery_id;
	 	$sql = "SELECT * FROM #__rsgallery2_galleries WHERE parent = '$gallery_id'";
	 	$database->setQuery( $sql );
	 	$rows = $database->loadObjectList();
	 	foreach ($rows as $row) {
	 		$array[] = $row->id;
	 		$sql = "SELECT * FROM #__rsgallery2_galleries WHERE parent = '$row->id'";
		 	$database->setQuery( $sql );
		 	$rows = $database->loadObjectList();
		 	foreach ($rows as $row) {
		 		$array[] = $row->id;
		 	}
	 	}
	 	$list = implode(",", $array);
	 	return $list;
	 }
	 
	function showFontList() {
	 	global $rsgConfig;
	 	
	 	$selected = $rsgConfig->get('watermark_font');
	 	$fonts = JFolder::files(JPATH_RSGALLERY2_ADMIN . '/fonts', 'ttf');
	 	foreach ($fonts as $font) {
	 		$fontlist[] = JHTML::_("Select.option", $font );
	 	}
	 	$list = JHTML::_("select.genericlist", $fontlist, 'watermark_font', '', 'value', 'text', $selected );
	 	return $list;
	 	
	 }
	/**
	 * Writes selected amount of characters. If there are more, the tail will be printed,
	 * identifying there is more
	 * @param string Full text
	 * @param int Number of characters to display
	 * @param string Tail to print after substring is printed
	 * @return string Subtext, followed by tail
	 */
	function subText($text, $length= 20, $tail="...") {
		$text = trim($text);
		$txtl = strlen($text);
		jimport('joomla.filter.output');
		
		$tail = JHTML::tooltip(JFilterOutput::ampReplace($text), null, null, $tail, null, 0);
		if($txtl > $length) {
			for($i=1;$text[$length-$i]!=" ";$i++) {
				if($i == $length) {
					return substr($text,0,$length) . $tail;
				}
			}
			$text = substr($text,0,$length-$i+1) . $tail;
		}

		return $text;
	}
	
	/**
	 * Checks if a specific component is installed
	 * @param Component name
	 */
	function isComponentInstalled( $component_name ) {
		$database =& JFactory::getDBO();
		$sql = "SELECT COUNT(1) FROM #__components as a WHERE a.option = '$component_name'";
		$database->setQuery( $sql );
		$result = $database->loadResult();
		if ($result > 0) {
			$notice = 1;
		} else {
			$notice = 0;
		}
		return $notice;
	}
	
	/**
	 * Higlights text based on keywords
	 * @param string Text to search in.
	 * @param strinf Keywords to search for
	 */
	function highlight_keywords($string, $keywords, $color = "yellow") {
	    if ($keywords != "" || $keywords != NULL) {
	        $words = explode(" ", $keywords);
	        foreach ($words as $word) {
	            $position = 0;
	            while ($position !== false) {
	                $position = strpos(strtolower($string), strtolower($word), $position);
	                if ($position !== false) {
	                    $replace_string = substr($string, $position, strlen($word));
	                    if ($position == 0) {
	                        if (!ctype_alnum($string{strlen($word)})) {
	                            $replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
	                            $string = substr_replace($string, $replace_string, $position, strlen($word));
	                        }
	                    } elseif (!ctype_alnum($string{$position - 1}) && strlen($string) == $position + strlen($word)) {
	                        $replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
	                        $string = substr_replace($string, $replace_string, $position, strlen($word));
	                    } elseif (!ctype_alnum($string{$position - 1}) && !ctype_alnum($string{$position+strlen($word)})) {
	                        $replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
	                        $string = substr_replace($string, $replace_string, $position, strlen($word));
	                    }
	                    $position = $position + strlen($replace_string);
	                }
	            } 
	        }
	    }
	    return $string;
	}

	function isUserType($type = "Super Administrator") {
		global $my;
		if ($my->usertype == $type) {
			return true;
		} else {
			return false;
		}
	}
}//end class
?>