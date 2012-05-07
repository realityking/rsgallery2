<?php
/**
* This file handles image manipulation functions RSGallery2
* @version $Id: video.utils.php 1013 2011-02-07 17:06:58Z mirjam $
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
class videoUtils extends fileUtils{
    function allowedFileTypes(){
		global $rsgConfig;
		
		// check if a converter is configured 
		// and return an empty array if it is not
		if( $rsgConfig->get( "videoConverter_path" ) == ''){
			return array();
		}
		else {
			return array('flv', 'avi', 'mpg');
		}
    }
    
    /**
      * thumb and display are resized into jpeg of first frame of video
      * @param string name of original image
      * @return filename of image
      */
    function getImgNameThumb($name){
        return $name . '.jpg';
    }
    
    /**
      * thumb and display are resized into jpeg of first frame of video
      * @param string name of original image
      * @return filename of image
      */
    function getImgNameDisplay($name){
		global $rsgConfig;
        return $name . '.' . $rsgConfig->get("videoConverter_extension");
    }
    
//    function getVideoName($name){
//        return $name . '.flv';
//    }
    
//    function getImgPreviewName($name){
//        return $name . '.jpg';
//    }
    
    /**
     * Takes an image file, moves the file and adds database entry
     * @param the verified REAL name of the local file including path
     * @param name of file according to user/browser or just the name excluding path
     * @param desired category
     * @param title of image, if empty will be created from $name
     * @param description of image, if empty will remain empty
     * @todo deleteImage (video)
     * @return returns true if successfull otherwise returns an ImageUploadError
     */
    function importImage($tmpName, $name, $cat, $title='', $desc='') {
        global $rsgConfig;
		$my =& JFactory::getUser();
		$database =& JFactory::getDBO();

        $destination = fileUtils::move_uploadedFile_to_orignalDir( $tmpName, $name );
        
        if( is_a( $destination, imageUploadError ) )
            return $destination;

		$parts = pathinfo( $destination );
        // fill $imgTitle if empty
        if( $imgTitle == '' ) 
            $imgTitle = substr( $parts['basename'], 0, -( strlen( $parts['extension'] ) + ( $parts['extension'] == '' ? 0 : 1 )));

        // replace names with the new name we will actually use
        $parts = pathinfo( $destination );
        $newName = $parts['basename'];
        $imgName = $parts['basename'];
        
        //Destination becomes original video, just for readability
        $original_video = $destination;
		$result = true;
		
		do{
			// New video will be located in display folder
			$newVideo = JPATH_DISPLAY . DS . $newName . "." . $rsgConfig->get("videoConverter_extension");
			$result = Ffmpeg::convertVideo( $original_video, $newVideo );
			if( !$result ){
				$result = new imageUploadError( $imgName, "error converting video: <pre>" . print_r( $result->getMessage(), true) ."</pre>" );
				break;
			}
			
			// get first frame of the video to genetrate a thumbnail from
			$videoPreviewImage =  JPATH_ORIGINAL . DS . $newName . ".png";
			$result = Ffmpeg::capturePreviewImage( $original_video, $videoPreviewImage );
			if( !$result ){
				$result = new imageUploadError( $imgName, "error capturing preview image: <pre>" . print_r( $result->getMessage(), true) ."</pre>" );
				break;
			}
			
			//Get details of the original image.
			$width = getimagesize( $videoPreviewImage );
			if( !$width ){
				$result = new imageUploadError( $videoPreviewImage, "not an image OR can't read $videoPreviewImage" );
				break;
			} else {
				//the actual image width
				$width = $width[0];
			}
			
			$result = imgUtils::makeThumbImage( $videoPreviewImage, $newName );
			// remove the temporary preview image
			JFile::delete($videoPreviewImage);
			if( !( $result )){
				$result = new imageUploadError( $imgName, JText::_('ERROR CREATING THUMB IMAGE'). ": ".$videoPreviewImage);
				break;
			}
			
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
				$result = new imageUploadError( $parts['basename'], $database->stderr(true) );
				break;
			}
		} while(false);

		if($result !== true){
			// clean up
			if(JFile::exists($newVideo)) JFile::delete($newVideo); 
			if(JFile::exists($videoPreviewImage)) JFile::delete($videoPreviewImage);
			imgUtils::deleteImage( $newName );
		}
		
		return $result;
    }

}
/**
  * abstract image library class
  * @package RSGallery2
  */
class genericVideoLib{
    /**
     * video conversion to flv function
     * @param string full path of source video
     * @param string full path of target video (FLV)
     * @return true if successfull, notice and false if error
     * @todo not final yet
     */
    function convertVideo($source, $target){	
		JError::raiseNotice('ERROR_CODE', JText::_('VIDEO ABSTRACT IMAGE LIB NO RESIZE'));
		return false;
    }

	/**
     * preview image capture function
     * @param string full path of source video
     * @param string full path of target image (PNG)
     * @return true if successfull, notice and false if error
     * @todo not final yet
     */
    function capturePreviewImage($source, $target){
		JError::raiseNotice('ERROR_CODE', JText::_('VIDEO ABSTRACT IMAGE LIB NO RESIZE'));
		return false;
    }    
    /**
      * detects if image library is available
      * @return false if not detected, user friendly string of library name and version if detected
      */
    function detect(){
        return false;
    }
}
/**
 * FFMPEG handler class
 * @package RSGallery2
 */
class Ffmpeg extends genericVideoLib{
    /**
     * video conversion to flv function
     * @param string full path of source video
     * @param string full path of target video (FLV)
     * @return true if successfull, notice and false if error
     * @todo not final yet
     */
    function convertVideo($source, $target){
        global $rsgConfig;
        
		$videoConverter_path = $rsgConfig->get( "videoConverter_path" );
		$videoConverter_param = $rsgConfig->get( "videoConverter_param" );

		// check if there are spaces in the source and target path
		if(stripos($source," ") != -1) $source = '"' . $source . '"';
		if(stripos($target," ") != -1) $target = '"' . $target . '"';
		
		$param = str_replace("{input}", $source, $videoConverter_param);
		$param = str_replace("{output}", $target, $param);
		
		$cmd = $videoConverter_path . ' ' . $param;
		$output = array();
		$return = null;
		exec($cmd, &$output, &$return);

		if($return == 0){
			return true;
		}
		else{
			JError::raiseNotice('ERROR_CODE', JText::_('VIDEO CONVERSION TO FVL ERROR'));
			return false;
		}
    }
    
    /**
     * preview image capture function
     * @param string full path of source video
     * @param string full path of target image (PNG)
     * @return true if successfull, notice and false if error
     * @todo not final yet
     */
    function capturePreviewImage($source, $target){
        global $rsgConfig;
        
		$videoConverter_path = $rsgConfig->get( "videoConverter_path" );
		$videoConverter_thumbParam = $rsgConfig->get( "videoConverter_thumbParam" );
		
		// check if there are spaces in the source and target path
		if(stripos($source," ") != -1) $source = '"' . $source . '"';
		if(stripos($target," ") != -1) $target = '"' . $target . '"';
		
		$param = str_replace("{input}", $source, $videoConverter_thumbParam);
		$param = str_replace("{output}", $target, $param);
		
		$cmd = $videoConverter_path . ' ' . $param;
		$output = array();
		$return = null;
		exec($cmd, &$output, &$return);

		if($return == 0){
			return true;
		}
		else{
			JError::raiseNotice('ERROR_CODE', JText::_('VIDEO CAPTURE PREVIEW IMAGE ERROR'));
			return false;
		}
	}

    /**
      * detects if image library is available
      * @return false if not detected, user friendly string of library name and version if detected
      */
    function detect($shell_cmd = '', $output = '', $status = ''){

		global $rsgConfig;
		
		$videoConverter_path = $rsgConfig->get( "videoConverter_path" );
		
		return JFile::exists($videoConverter_path);

    }
} // END CLASS FFMPEG
?>