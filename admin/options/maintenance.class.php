<?php
/**
* Maintenance class for RSGallery2
* @version $Id: maintenance.class.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2007 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

/**
 * Maintenance class for RSGallery2
 * @package RSGallery2
 * @author Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsg2_maintenance {

    function rsg2_maintenance() {
    	
    }
    
    /**
     * Samples a random thumb from the specified gallery and compares dimensions against Config settings
     * @param Integer Gallery ID
     * @return Boolean True if size has changed, false if not.
     */
    function thumbSizeChanged( $gid ) {
    	global $rsgConfig;
    	$gallery = rsgGalleryManager::_get( $gid );
    	$images = $gallery->items();
    	foreach ($images as $image) {
    		$imgname[] = $image->name;
    	}
    	$image = array_rand($imgname);

    	$imgdata = getimagesize( imgUtils::getImgThumb($imgname[$image], true) );
    	if ( $imgdata[0] == $rsgConfig->get('thumb_width') ) {
    		return false;
    	} else {
    		return true;
    	}
    }
}

class rsg2_consolidate extends rsg2_maintenance {
	
	function consolidateDB() {
	    global  $rsgConfig;
		$database =& JFactory::getDBO();
	    //Load all image names from DB in array
	    $sql = "SELECT name FROM #__rsgallery2_files";
	    $database->setQuery($sql);
	    $names_db = rsg2_consolidate::arrayToLower($database->loadResultArray());
	
	    $files_display  = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_display'));
	    $files_original = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_original'));
	    $files_thumb    = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_thumb'));
	    $files_total    = array_unique(array_merge($files_display,$files_original,$files_thumb));
	    
	    html_rsg2_maintenance::consolidateDB($names_db, $files_display, $files_original, $files_thumb, $files_total);
    }
    
    /**
	 * Fills an array with the filenames, found in the specified directory
	 * @param string Directory from Joomla root
	 * @return array Array with filenames
	 */
	function getFilenameArray($dir){
	    global $rsgConfig;
	    
	    //Load all image names from filesystem in array
	    $dh  = opendir(JPATH_ROOT.$dir);
	    //Files to exclude from the check
	    
	    $exclude = array('.', '..', 'Thumbs.db', 'thumbs.db');
	    $allowed = array('jpg','gif');
	    $names_fs = array();
	    
	    while (false !== ($filename = readdir($dh))) {
	        $ext = explode(".", $filename);
	        $ext = array_reverse($ext);
	        $ext = strtolower($ext[0]);
	        if (!is_dir(JPATH_ROOT.$dir."/".$filename) AND !in_array($filename, $exclude) AND in_array($ext, $allowed))
	            {
	            if ($dir == $rsgConfig->get('imgPath_display') OR $dir == $rsgConfig->get('imgPath_thumb'))
	                {
	                //Recreate normal filename, eliminating the extra ".jpg"
	                $names_fs[] = substr(strtolower($filename), 0, -4);
	                }
	            else
	                {
	                $names_fs[] = strtolower($filename);
	                }
	            }
	        else
	            {
	            //Do nothing
	            continue;
	            }
	        }
	    closedir($dh);
	    return $names_fs;
	    
	}
	
	/**
	 * Changes all values of an array to lowercase
	 * @param array mixed case mixed or upper case values
	 * @return array lower case values
	 */
	function arrayToLower($array) {
	    $array = explode("|", strtolower(implode("|",$array)));
	    return $array;
	}
}
?>