<?php
/**
* This file handles image manipulation functions RSGallery2
* @version $Id: audio.utils.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

defined( '_JEXEC' ) or die( 'Access Denied' );

/**
* Image utilities class
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class audioUtils extends fileUtils{
    function allowedFileTypes(){
        return array('mp3');
    }

    /**
     * Takes an image file, moves the file and adds database entry
     * @param the verified REAL name of the local file including path
     * @param name of file according to user/browser or just the name excluding path
     * @param desired category
     * @param title of image, if empty will be created from $name
     * @param description of image, if empty will remain empty
     * @return returns true if successfull otherwise returns an ImageUploadError
     */
    function importImage($tmpName, $name, $cat, $title='', $desc='') {
        global $rsgConfig;
		$database =& JFactory::getDBO();
		$my =& JFactory::getUser();

        $destination = fileUtils::move_uploadedFile_to_orignalDir( $tmpName, $name );
        
        if( is_a( $destination, imageUploadError ) )
            return $destination;

        $parts = pathinfo( $destination );
        $newName = $parts['basename'];

        // fill $title if empty
        if( $title == '' ) 
            $title = substr( $parts['basename'], 0, -( strlen( $parts['extension'] ) + ( $parts['extension'] == '' ? 0 : 1 )));

        // determine ordering
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE gallery_id = '$cat'");
        $ordering = $database->loadResult() + 1;
        
        //Store image details in database
        $alias = mysql_real_escape_string(JFilterOutput::stringURLSafe($title));
		$desc = mysql_real_escape_string($desc);
        $title = mysql_real_escape_string($title);
        $database->setQuery("INSERT INTO #__rsgallery2_files".
                " (title, name, descr, gallery_id, date, ordering, userid, alias) VALUES".
                " ('$title', '$newName', '$desc', '$cat', now(), '$ordering', '$my->id', '$alias')");
        
        if (!$database->query()){
			audioUtils::deleteAudio( $parts['basename'] );
            return new imageUploadError( $parts['basename'], $database->stderr(true) );
        }

        return true;
    }
    
     function getAudio($name, $local=false){
        global  $rsgConfig;
        
        $locale = $local? JPATH_ROOT : JURI_SITE;
        
        // if thumb image exists return that, otherwise the original image width <= $thumb_width so we return the original image instead.
        if( file_exists( JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/' . audioUtils::getAudioName( $name ))){
            return $locale  . $rsgConfig->get('imgPath_original') . '/' . audioUtils::getAudioName( $name );
        }else {
            return;
        }
    }
    
    /**
    * deletes all elements of image on disk and in database
    * @param string name of image
    * @return true if success or notice and false if error
    */
	function deleteAudio($name){
        global $database, $rsgConfig;
        
        $original   = JPATH_ORIGINAL . DS . $name;
        
        if( file_exists( $original )){
            if( !unlink( $original )){
				JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING ORIGINAL IMAGE').": ".$original);
				return false;
			}
		}
        $database->setQuery("SELECT gallery_id FROM #__rsgallery2_files WHERE name = '$name'");
        $gallery_id = $database->loadResult();
                
        $database->setQuery("DELETE FROM #__rsgallery2_files WHERE name = '$name'");
        if( !$database->query()){
            JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING DATABASE ENTRY FOR IMAGE').": ".$name);
			return false;
		}
		
        galleryUtils::reorderRSGallery('#__rsgallery2_files', "gallery_id = '$gallery_id'");
        
        return true;
    }
      
    function getAudioName($name){
        return $name;
    }
}