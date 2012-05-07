<?php
/**
* This file handles image manipulation functions RSGallery2
* @version $Id: img.utils.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

defined( '_JEXEC' ) or die( 'Access Denied' );

/**
* Image utilities class
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class imgUtils extends fileUtils{
    
    function allowedFileTypes(){
        return array("jpg",'jpeg',"gif","png");
    }

    /**
      * thumb and display are resized into jpeg regardless of what the original image was
      * @todo update these functions when the user is given an option as to what image type thumb and display are
      * @param string name of original image
      * @return filename of image
      */
    function getImgNameThumb($name){
        return $name . '.jpg';
    }
    
    /**
      * thumb and display are resized into jpeg regardless of what the original image was
      * @todo update these functions when the user is given an option as to what image type thumb and display are
      * @param string name of original image
      * @return filename of image
      */
    function getImgNameDisplay($name){
        return $name . '.jpg';
    }

    /**
      * @param string full path of source image
      * @param string name destination file (path is retrieved from rsgConfig)
      * @return true if successfull, false if error
      */
    function makeDisplayImage($source, $name='', $width){
        global $rsgConfig;

        if( $name=='' ){
            $parts = pathinfo( $source );
            $name = $parts['basename'];
        }
        $target = JPATH_DISPLAY . DS . imgUtils::getImgNameDisplay( $name );
       
        return imgUtils::resizeImage( $source, $target, $width );
    }   
    /**
      * @param string full path of source image
      * @param string name destination file (path is retrieved from rsgConfig)
      * @return true if successfull, false if error
      */
    function makeThumbImage($source, $name=''){
        global $rsgConfig;
        
        if( $name=='' ){
            $parts = pathinfo( $source );
            $name = $parts['basename'];
        }
        $target = JPATH_THUMB . DS . imgUtils::getImgNameThumb( $name );
        
        if ( $rsgConfig->get('thumb_style') == 1 && $rsgConfig->get('graphicsLib') == 'gd2'){
            return GD2::createSquareThumb( $source, $target, $rsgConfig->get('thumb_width') );
        } else {
            return imgUtils::resizeImage( $source, $target, $rsgConfig->get('thumb_width') );
        }
    }
    
    /**
      * generic image resize function
      * @param string full path of source image
      * @param string full path of target image
      * @param int width of target
      * @return true if successfull, false if error
      * @todo only writes in JPEG, this should be given as a user option
      */
    function resizeImage($source, $target, $targetWidth){
        global $rsgConfig;

        switch( $rsgConfig->get( 'graphicsLib' )){
            case 'gd2':
                return GD2::resizeImage($source, $target, $targetWidth);
                break;
            case 'imagemagick':
                return ImageMagick::resizeImage($source, $target, $targetWidth);
                break;
            case 'netpbm':
                return Netpbm::resizeImage($source, $target, $targetWidth);
                break;
            default:
                JError::raiseNotice('ERROR_CODE', JText::_('INVALID GRAPHICS LIBRARY') . $rsgConfig->get( 'graphicsLib' ));
				return false;
        }
    }

    /**
     * Takes an image file, moves the file and adds database entry
     * @param the verified REAL name of the local file including path
     * @param name of file according to user/browser or just the name excluding path
     * @param desired category
     * @param title of image, if empty will be created from $imgName
     * @param description of image, if empty will remain empty
     * @return returns true if successfull otherwise returns an ImageUploadError
     */
    function importImage($imgTmpName, $imgName, $imgCat, $imgTitle='', $imgDesc='') {
        global $rsgConfig;
		$my =& JFactory::getUser();
		$database =& JFactory::getDBO();

        //First move uploaded file to original directory
        $destination = fileUtils::move_uploadedFile_to_orignalDir( $imgTmpName, $imgName );

        if( is_a( $destination, 'imageUploadError' ) )
            return $destination;

        $parts = pathinfo( $destination );

		// If IPTC parameter in config is true and the user left either the image title
		// or description empty in the upload step we want to get that IPTC data.
		if ($rsgConfig->get( 'useIPTCinformation' )){
			if (($imgTitle == '') OR ($imgDesc == '')) {
				getimagesize( $destination , $imageInfo);
				if(isset($imageInfo['APP13'])) {
					$iptc = iptcparse($imageInfo['APP13']);
					//Get Iptc.Caption for the description (null if it does not exist)
					$IPTCcaption = $iptc["2#120"][0]; 
					//Get Iptc.ObjectName for the title
					$IPTCtitle = $iptc["2#005"][0]; 
					//If the field (description or title) in the import step is emtpy, and we have IPTC info, then use the IPTC info:
					if (($imgDesc == '') and !is_null($IPTCcaption)) {
						$imgDesc = $IPTCcaption;
					}
					if (($imgTitle == '') and !is_null($IPTCtitle)) {
						$imgTitle = $IPTCtitle;
					}
				}
			}
		}
		
        // fill $imgTitle if empty
        if( $imgTitle == '' ) 
            $imgTitle = substr( $parts['basename'], 0, -( strlen( $parts['extension'] ) + ( $parts['extension'] == '' ? 0 : 1 )));

        // replace names with the new name we will actually use
        $parts = pathinfo( $destination );
        $newName = $parts['basename'];
        $imgName = $parts['basename'];
        
        //Get details of the original image.
        $width = getimagesize( $destination );
        if( !$width ){
            imgUtils::deleteImage( $newName );
            return new imageUploadError( $destination, JText::_('NOT AN IMAGE OR CANNOT READ')." ". $destination );
        } else {
            //the actual image width and height and its max
            $height = $width[1];
			$width = $width[0];
			if ($height > $width) {
				$maxSideImage = $height;
			} else {
				$maxSideImage = $width;
			}
        }
        //Destination becomes original image, just for readability
        $original_image = $destination;
        
        // if original is wider or higher than display size, create a display image
        if( $maxSideImage > $rsgConfig->get('image_width') ) {
            $result = imgUtils::makeDisplayImage( $original_image, $newName, $rsgConfig->get('image_width') );
            if( !$result ){
                imgUtils::deleteImage( $newName );
				return new imageUploadError( $imgName, JText::_('ERROR CREATING DISPLAY IMAGE'). ": ".$newName);
            }
        } else {
            $result = imgUtils::makeDisplayImage( $original_image, $newName, $maxSideImage );
            if( !$result ){
                imgUtils::deleteImage( $newName );
                return new imageUploadError( $imgName, JText::_('ERROR CREATING DISPLAY IMAGE'). ": ".$newName);
                }
        }
           
        // if original is wider or higher than thumb, create a thumb image
        if( $maxSideImage > $rsgConfig->get('thumb_width') ){
            $result = imgUtils::makeThumbImage( $original_image, $newName );
            if( !$result){
                imgUtils::deleteImage( $newName );
                return new imageUploadError( $imgName, JText::_('ERROR CREATING THUMB IMAGE'). ": ".$newName);
            }
        }

        // determine ordering
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE gallery_id = '$imgCat'");
        $ordering = $database->loadResult() + 1;

        //Store image details in database
		$imgAlias = $database->getEscaped(JFilterOutput::stringURLSafe($imgTitle));
        $imgDesc = $database->getEscaped($imgDesc);
        $imgTitle = $database->getEscaped($imgTitle);
        
		$database->setQuery("INSERT INTO #__rsgallery2_files".
                " (title, name, descr, gallery_id, date, ordering, userid, alias) VALUES".
                " ('$imgTitle', '$newName', '$imgDesc', '$imgCat', now(), '$ordering', '$my->id', '$imgAlias')");
        
        if (!$database->query()){
            imgUtils::deleteImage( $newName );
            return new imageUploadError( $imgName, $database->stderr(true) );
        }
        
        //check if original image needs to be kept, otherwise delete it.
        if ( !$rsgConfig->get('keepOriginalImage') ) {
            JFile::delete( imgUtils::getImgOriginal( $newName, true ) );
        }
            
        return true;
    }

    /**
      * deletes all elements of image on disk and in database
      * @param string name of image
      * @return true if success or notice and false if error
      */
    function deleteImage($name){
        global  $rsgConfig;

		$database =& JFactory::getDBO();
		
        $thumb      = JPATH_THUMB . DS . imgUtils::getImgNameThumb( $name );
        $display    = JPATH_DISPLAY . DS . imgUtils::getImgNameDisplay( $name );
        $original   = JPATH_ORIGINAL . DS . $name;

        if( file_exists( $thumb )){
            if( !JFile::delete( $thumb )){
				JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING THUMB IMAGE') .": ". $thumb);
				return false;
			}
		}
        if( file_exists( $display )){
			if( !JFile::delete( $display )){
                JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING DISPLAY IMAGE') .": ". $display);
				return false;
			}
		}
        if( file_exists( $original )){
			if( !JFile::delete( $original )){
                JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING ORIGINAL IMAGE') .": ". $original);
				return false;
			}
		}
		
        $database->setQuery("SELECT gallery_id FROM #__rsgallery2_files WHERE name = '$name'");
        $gallery_id = $database->loadResult();
                
        $database->setQuery("DELETE FROM #__rsgallery2_files WHERE name = '$name'");
        if( !$database->query()){
            JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING DATABASE ENTRY FOR IMAGE') .": ". $name);
			return false;
		}
        galleryUtils::reorderRSGallery('#__rsgallery2_files', "gallery_id = '$gallery_id'");
        
        return true;
    }
    
    /**
      * @param string name of the image
      * @param boolean return a local path instead of URL
      * @return complete URL of the image
      */
    function getImgOriginal($name, $local=false){
        global  $rsgConfig, $mainframe ;
        
		$locale = $local? JPATH_ROOT : JURI_SITE;
        	
		//$locale = trim($locale, '/');	//Mirjam: removed trim, getimagesize in GD::resizeImage needs preceeding / in path
        // if original image exists return that, otherwise $keepOriginalImage is false and and we return the display image instead.
        if( file_exists( JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/' . $name )){
            return $locale . $rsgConfig->get('imgPath_original') . '/' . rawurlencode($name);
        }else {
            return $locale . $rsgConfig->get('imgPath_display') . '/' . rawurlencode( imgUtils::getImgNameDisplay( $name ));
        }
    }
    
    /**
      * @param string name of the image
      * @param boolean return a local path instead of URL
      * @return complete URL of the image
      */
    function getImgDisplay($name, $local=false){
		global  $rsgConfig,$mainframe;
        
        $locale = $local? JPATH_ROOT : JURI_SITE;
		$locale = trim($locale, '/');	
		
        // if display image exists return that, otherwise the original image width <= $display_width so we return the original image instead.
        if( file_exists( JPATH_ROOT.$rsgConfig->get('imgPath_display') . '/' . imgUtils::getImgNameDisplay( $name ))){
            return $locale . $rsgConfig->get('imgPath_display') . '/' . rawurlencode( imgUtils::getImgNameDisplay( $name ));
        }else {
            return $locale . $rsgConfig->get('imgPath_original') . '/' . rawurlencode($name);
        }
    }
    
    /**
      * @param string name of the image
      * @param boolean return a local path instead of URL
      * @return complete URL of the image
      */
    function getImgThumb($name, $local=false){
        global  $rsgConfig, $mainframe;
        $locale = $local? JPATH_ROOT : JURI_SITE;
		$locale = trim($locale, '/');	
		
        // if thumb image exists return that, otherwise the original image width <= $thumb_width so we return the original image instead.
        if( file_exists( JPATH_ROOT.$rsgConfig->get('imgPath_thumb') . '/' . imgUtils::getImgNameThumb( $name ))){
            return $locale  . $rsgConfig->get('imgPath_thumb') . '/' . rawurlencode( imgUtils::getImgNameThumb( $name ));
        }else {
            return $locale  . $rsgConfig->get('imgPath_original') . '/' . rawurlencode($name);
        }
    }
    
        /**
        @depreciated use rsgImage->showEXIF();
        @todo this class is for logic only!!!  take this html generation somewhere else.
          reminder: exif should be read from original image only.
    **/
    function showEXIF($imagefile){
        if(!function_exists('exif_read_data')) return false;

        if (!@exif_read_data($imagefile, 0, true))
        {
        ?>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="imageExif">
    <tr><td>No EXIF info available</td></tr>
    </table>
        <?php
        return false;
        } 
        $exif = exif_read_data($imagefile, 0, true);
        ?>
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="imageExif">
            <tr>
                <th>Section</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        <?php
                foreach ($exif as $key => $section):
                    foreach ($section as $name => $val):
        ?>
            <tr>
                <td class="exifKey"><?php echo $key;?></td>
                <td class="exifName"><?php echo $name;?></td>
                <td class="exifVal"><?php echo $val;?></td>
            </tr>
        <?php
                    endforeach;
                endforeach;
        ?>
        </table>
        <?php
    }
    
    /**
     * Shows a selectbox  with the filenames in the selected gallery
     * @param int Gallery ID
     * @param int Currently selected thumbnail
     * @return HTML representation of a selectbox
     * @todo Also offer the possiblity to select thumbs from subgalleries
     */
    function showThumbNames($id, $current_id, $selectname = 'thumb_id') {
        $database =& JFactory::getDBO();

		if( $id==null ){
			echo JText::_('NO IMAGES IN GALLERY YET');
			return;
		}
        $list = galleryUtils::getChildList( $id );
        //$sql = "SELECT name, id FROM #__rsgallery2_files WHERE gallery_id in ($list)";
        $sql = "SELECT a.name, a.id, b.name as gname FROM #__rsgallery2_files AS a " .
            "LEFT JOIN #__rsgallery2_galleries AS b ON a.gallery_id = b.id " .
            "WHERE gallery_id IN ($list) " .
            "ORDER BY gname, a.id ASC";
        $database->setQuery($sql);
        $list = $database->loadObjectList();

        if( $list==null ){
            echo JText::_('NO IMAGES IN GALLERY YET');
            return;
        }

        $dropdown_html = "<select name=\"$selectname\"><option value=\"0\" SELECTED>- Random thumbnail -</option>\n";
        if (!isset($current_id)) {
            $current_id = 0;
        }

        foreach ($list as $item) {
            $dropdown_html .= "<option value=\"$item->id\"";
            if ($item->id == $current_id)
                $dropdown_html .= " SELECTED>";
            else
                $dropdown_html .= ">";
            $dropdown_html .=  $item->name." (".$item->gname.")</option>\n";
        }
        echo $dropdown_html."</select>";
    }

}//End class

/**
  * abstract image library class
  * @package RSGallery2
  */
class genericImageLib{
    /**
      * resize source to targetWidth and output result to target
      * @param string full path of source image
      * @param string full path of target image
      * @param int width of target
      * @return true if successfull, false if error
      */ 
    function resizeImage($source, $target, $targetWidth){
		JError::raiseNotice('ERROR_CODE', JText::_('ABSTRACT IMAGE LIBRARY CLASS NO RESIZE AVAILABLE'));
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
 * NETPBM handler class
 * @package RSGallery2
 */
class Netpbm extends genericImageLib{
    /**
     * image resize function
     * @param string full path of source image
     * @param string full path of target image
     * @param int width of target
     * @return true if successfull, PEAR_Error if error
     * @todo only writes in JPEG, this should be given as a user option
     */
    function resizeImage($source, $target, $targetWidth){
        global $rsgConfig;
        
        // if path exists add the final /
        $netpbm_path = $rsgConfig->get( "netpbm_path" );
        $netpbm_path = $netpbm_path==''? '' : $netpbm_path.'/';
        
        $cmd = $netpbm_path . "anytopnm $source | " .
            $netpbm_path . "pnmscale -width=$targetWidth | " .
            $netpbm_path . "pnmtojpeg -quality=" . $rsgConfig->get( "jpegQuality" ) . " > $target";
        @exec($cmd);
    }

    /**
      * detects if image library is available
      * @return false if not detected, user friendly string of library name and version if detected
      */
    function detect($shell_cmd = '', $output = '', $status = ''){
        @exec($shell_cmd. 'jpegtopnm -version 2>&1',  $output, $status);
        if(!$status){
            if(preg_match("/netpbm[ \t]+([0-9\.]+)/i",$output[0],$matches)){
                return $matches[0];
            }
            else
            	return false;
        }
    }
}
/**
 * ImageMagick handler class
 * @package RSGallery2
 */
class ImageMagick extends genericImageLib{
    /**
     * image resize function
     * @param string full path of source image
     * @param string full path of target image
     * @param int width of target
     * @return true if successfull, false if error
     * @todo only writes in JPEG, this should be given as a user option
     */
    function resizeImage($source, $target, $targetWidth){
        global $rsgConfig;
        
        // if path exists add the final /
        $impath = $rsgConfig->get( "imageMagick_path" );
        $impath = $impath==''? '' : $impath.'/';
        
        $cmd = $impath."convert -resize $targetWidth $source $target";
        exec($cmd, $results, $return);
        if( $return > 0 ){
			JError::raiseNotice('ERROR_CODE', JText::_('IMAGE COULD NOT BE MADE WITH IMAGEMAGICK').": ".$target);
			return false;
			}
        else 
        	return true;
    }

    /**
     * detects if image library is available
     * @return false if not detected, user friendly string of library name and version if detected
     */
    function detect( $output = '', $status = '' ){
        global $rsgConfig;
    
        // if path exists add the final /
        $impath = $rsgConfig->get( "imageMagick_path" );
        $impath = $impath==''? '' : $impath.'/';
    
        @exec($impath.'convert -version',  $output, $status);
        if(!$status){
            if(preg_match("/imagemagick[ \t]+([0-9\.]+)/i",$output[0],$matches)){
                return $matches[0];
            } else {
                return false;
            }
        }
    }
}
/**
 * GD2 handler class
 * @package RSGallery2
 */
class GD2 extends genericImageLib{
    
    /**
     * image resize function
     * @param string full path of source image
     * @param string full path of target image
     * @param int width of target
     * @return true if successfull, false if error
     * @todo only writes in JPEG, this should be given as a user option
     * @todo use constants found in http://www.php.net/gd rather than numbers
     */
    function resizeImage($source, $target, $targetWidth){
        global $rsgConfig;
        // an array of image types
        
        $imageTypes = array( 1 => 'gif', 2 => 'jpeg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp', 7 => 'tiff', 8 => 'tiff', 9 => 'jpc', 10 => 'jp2', 11 => 'jpx', 12 => 'jb2', 13 => 'swc', 14 => 'iff', 15 => 'wbmp', 16 => 'xbm');
        $source = rawurldecode( $source );//fix: getimagesize does not like %20
		$target = rawurldecode( $target );//fix: getimagesize does not like %20
        $imgInfo = getimagesize( $source );

        if( !$imgInfo ){
			JError::raiseNotice('ERROR_CODE', $source ." ". JText::_('IS NOT A VALID IMAGE OR IMAGENAME'));
			return false;
        }
        list( $sourceWidth, $sourceHeight, $type, $attr ) = $imgInfo;

        // convert $type into a usable string
        $type = $imageTypes[$type];
        
        // check if we can read this type of file
        if( !function_exists( "imagecreatefrom$type" )){
            JError::raiseNotice('ERROR_CODE', JText::_('GD2 does not support reading image type').' '.$type);
			return false;
        }
		// Determine target sizes: the $targetWidth that is put in this function is actually
		// the size of the largest side of the image, with that calculate the other side:
		// - landscape: function input $targetWidth is the actual $targetWidht
		// - portrait: function input $targetWidth is the height to achieve, so switch!
		if( $sourceWidth > $sourceHeight ) {	//landscape
			$targetHeight = ( $targetWidth / $sourceWidth ) * $sourceHeight;
		} else {								//portrait or square
			$targetHeight = $targetWidth;
			$targetWidth = ( $targetHeight / $sourceHeight ) * $sourceWidth;
		}

        // load source image file into a resource
        $loadImg = "imagecreatefrom" . $type;
        $sourceImg = $loadImg( $source );
        if( !$sourceImg ){
			JError::raiseNotice('ERROR_CODE', JText::_('ERROR READING SOURCE IMAGE').': '.$source);
			return false;        
        }
        // create target resource
        $targetImg = imagecreatetruecolor( $targetWidth, $targetHeight );
        
        // resize from source resource image to target
        if( !imagecopyresampled(
            $targetImg,
            $sourceImg,
            0,0,0,0,
            $targetWidth, $targetHeight,
            $sourceWidth, $sourceHeight
        )){
		JError::raiseNotice('ERROR_CODE', JText::_('ERROR RESIZING IMAGE').': '.$source);
		return false;  
		}	
        // write the image
        if( !imagejpeg( $targetImg, $target, $rsgConfig->get('jpegQuality'))){
			JError::raiseNotice('ERROR_CODE', JText::_('ERROR WRITING TARGET IMAGE').': '.$target);
			return false; 
		}
        //Free up memory
        imagedestroy($sourceImg);
        imagedestroy($targetImg);
		return true;
    }
    
    /**
      * Creates a square thumbnail by first resizing and then cutting out the thumb
      * @param string Full path of source image
      * @param string Full path of target image
      * @param int width of target
      * @return true if successfull, false if error
      */
    function createSquareThumb( $source, $target, $width ) {
        global $rsgConfig;
        
        //Create a square image, based on the set width
        $t_width  = $width;
        $t_height = $width;
        
        //Get details on original image
        $imgdata = getimagesize($source);
        $width_orig     = $imgdata[0];
        $height_orig    = $imgdata[1];
        $ext            = $imgdata[2];
        
        switch($ext)
            {
            case 1:
                $image = imagecreatefromgif($source);
                break;
            case 2:
                $image = imagecreatefromjpeg($source);
                break;
            case 3:
                $image = imagecreatefrompng($source);
                break;
            }
    
        $width  = $t_width;    //New width
        $height = $t_height;   //New height
        list($width_orig, $height_orig) = getimagesize($source);
        
        if ($width_orig < $height_orig) {
          $height = ($t_width / $width_orig) * $height_orig;
        } else {
           $width = ($t_height / $height_orig) * $width_orig;
        }
        
        //if the width is smaller than supplied thumbnail size
        if ($width < $t_width) {
            $width = $t_width;
            $height = ($t_width/ $width_orig) * $height_orig;;
            }
        
        //if the height is smaller than supplied thumbnail size
        if ($height < $t_height) {
            $height = $t_height;
            $width = ($t_height / $height_orig) * $width_orig;
            }
    
        //Resize the image
        $thumb = imagecreatetruecolor($width , $height); 
        if ( !imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig)){
			JError::raiseNotice('ERROR_CODE', JText::_('ERROR RESIZING IMAGE').": ".$source);
            return false;
        }
        //Create the cropped thumbnail
        $w1 =($width/2) - ($t_width/2);
        $h1 = ($height/2) - ($t_height/2);
        $thumb2 = imagecreatetruecolor($t_width , $t_height);
        if ( !imagecopyresampled($thumb2, $thumb, 0,0, $w1, $h1, $t_width , $t_height ,$t_width, $t_height) ){
            JError::raiseNotice('ERROR_CODE', JText::_('ERROR CROPPING IMAGE').": ".$source);
            return false;
		}
        
        // write the image
        if( !imagejpeg( $thumb2, $target, $rsgConfig->get('jpegQuality'))) {
            JError::raiseNotice('ERROR_CODE', JText::_('ERROR WRITING TARGET IMAGE').": ".$target);
            return false;
        } else {
        	//Free up memory
        	imagedestroy($thumb);
        	imagedestroy($thumb2);
        	return true;
        }
    }
    
    /**
      * detects if image library is available
      * @return false if not detected, user friendly string of library name and version if detected
      */
    function detect(){
        $GDfuncList = get_extension_funcs('gd');
        ob_start();
        @phpinfo(INFO_MODULES);
        $output=ob_get_contents();
        ob_end_clean();
        $matches[1]='';
        if(preg_match("/GD Version[ \t]*(<[^>]+>[ \t]*)+([^<>]+)/s",$output,$matches)){
            $gdversion = $matches[2];
        }
        if( $GDfuncList ){
            if( in_array('imagegd2',$GDfuncList) ){
                return 'gd2 '. $gdversion;
            }
            else{
//                 return 'gd1 '. $gdversion);
                return false;
            }
        }
        else return false;
    }
}


/**
* Image watermarking class
* @package RSGallery2
* @author Ronald Smit <webmaster@rsdev.nl>
*/
class waterMarker extends GD2 {
    var $imagePath; 					//valid absolute path to image file
    var $waterMarkText; 				//the text to draw as watermark
    var $font 			= "arial.ttf"; 	//font file to use for drawing text. need absolute path
    var $size 			= 10; 			//font size
    var $angle 			= 45; 			//angle to draw watermark text
    var $imageResource; 				//to store the image resource after completion of watermarking
    var $imageType		= "jpg"; 		//this could be either of png, jpg, jpeg, bmp, or gif (if gif then output will be in png)
    var $shadow 		= false; 		//if set to true then a shadow will be drawn under every watermark text
    var $antialiased 	= true; 		//if set to true then watermark text will be drawn anti-aliased. this is recommended
	var $imageTargetPath = '';		//full path to where to store the watermarked image to
    /**
     * this function draws the watermark over the image
     */
    function mark($imagetype = 'display'){
    global $rsgConfig;
    
    //get basic properties of the image file
    list($width, $height, $type, $attr) = getimagesize($this->imagePath); 
        
        switch ($this->imageType) {
                case "png":
                    $createProc = "imagecreatefrompng";
                    $outputProc = "imagepng";
                    break;
                case "gif";
                    $createProc = "imagecreatefromgif";
                    $outputProc = "imagepng";
                    break;
                case "bmp";
                    $createProc = "imagecreatefrombmp";
                    $outputProc = "imagebmp";
                    break;
                case "jpeg":
                case "jpg":
                    $createProc = "imagecreatefromjpeg";
                    $outputProc = "imagejpeg";
                    break;
        }
        
		//create the image with generalized image create function
        $im = $createProc($this->imagePath); 
		
   		//create copy of image
		$im_copy = ImageCreateTrueColor($width,$height) ;
		ImageCopy ($im_copy,$im,0,0,0,0,$width,$height) ;
   		
        $grey           = imagecolorallocate($im, 180, 180, 180); //color for watermark text
        $shadowColor    = imagecolorallocate($im, 130, 130, 130); //color for shadow text

        if (!$this->antialiased) {
            $grey           *= -1; //grey = grey * -1
            $shadowColor    *= -1; //shadowColor = shadowColor * -1
        }
            
        /**
         * Determines the position of the image and returns x and y
         * (1 = Top Left    ; 2 = Top Center    ; 3 = Top Right)
         * (4 = Left        ; 5 = Center        ; 6 = Right)
         * (7 = Bottom Left ; 8 = Bottom Center ; 9 = Bottom Right)
         * @return x and y coordinates
         */
        $position 	= $rsgConfig->get('watermark_position');
        if ( $rsgConfig->get('watermark_type') == 'text' ) {
        	$bbox 		= imagettfbbox($rsgConfig->get('watermark_font_size'), $rsgConfig->get('watermark_angle'), JPATH_RSGALLERY2_ADMIN."/fonts/arial.ttf", $rsgConfig->get('watermark_text'));
        	$textW 		= abs($bbox[0] - $bbox[2]) + 20;
        	$textH 		= abs($bbox[7] - $bbox[1]) + 20;
        } else {
        	//Get dimensions for watermark image
        	list($w, $h, $t, $a) = getimagesize(JPATH_ROOT . DS . 'images' . DS . 'rsgallery' . DS . $rsgConfig->get('watermark_image'));
        	$textW	= $w + 20;
        	$textH 	= $h + 20;
        }

        list($width, $height, $type, $attr) = getimagesize($this->imagePath); //get basic properties of the image file
        switch ($position) {
        case 1://Top Left
            $newX = 20;
            $newY = 0 + $textH;
            break;
        case 2://Top Center
            $newX = ($width/2) - ($textW/2);
            $newY = 0 + $textH;
            break;
        case 3://Top Right
            $newX = $width - $textW;
            $newY = 0 + $textH;
            break;
        case 4://Left
            $newX = 20;
            $newY = ($height/2) + ($textH/2);
            break;
        case 5://Center
            $newX = ($width/2) - ($textW/2);
            $newY = ($height/2) + ($textH/2);
            break;
        case 6://Right
            $newX = $width - $textW;
            $newY = ($height/2) + ($textH/2);
            break;
        case 7://Bottom left
            $newX = 20;
            $newY = $height - ($textH/2);
            break;
        case 8://Bottom Center
            $newX = ($width/2) - ($textW/2);
            $newY = $height - ($textH/2);
            break;
        case 9://Bottom right
            $newX = $width - $textW;
            $newY = $height - ($textH/2);
            break;
        }
        
        if ($rsgConfig->get('watermark_type') == 'image') {
			//Merge watermark image with image
        	$watermark = imagecreatefrompng( JPATH_ROOT . DS . 'images' . DS . 'rsgallery' . DS . $rsgConfig->get('watermark_image') );
        	ImageCopyMerge ( $im, $watermark,  $newX + 1, $newY + 1, 0, 0, $w, $h, $rsgConfig->get('watermark_transparency') );
        } else {
        	//draw shadow text over image
	        imagettftext($im, $this->size, $this->angle, $newX+1, $newY+1, $shadowColor, $this->font, $this->waterMarkText); 
	        //draw text over image
	        imagettftext($im, $this->size, $this->angle, $newX, $newY, $grey, $this->font, $this->waterMarkText);
	        //Merge copy and original image
			ImageCopyMerge ( $im, $im_copy, 0, 0, 0, 0, $width, $height, $rsgConfig->get('watermark_transparency') ); 
        }
            
        $fh = fopen($this->imageTargetPath ,'wb');
        fclose($fh);
		
        //deploy the image with generalized image deploy function
		$this->imageResource = $outputProc($im, $this->imageTargetPath, 100);
        imagedestroy($im);
        imagedestroy($im_copy);
        if (isset($watermark)) {
        	imagedestroy($watermark);
        }
		
    }
     
    /**
     * Function that takes an image and returns the url to watermarked image
     * @param string Name of the image in question
     * @param string Font used for watermark
     * @param string Text size in pixels
     * @param int Vertical spacing between text
     * @param int Horizontal spacing between text
     * @param boolean Shadow text yes or no
     * @return url to watermarked image
     */
    function showMarkedImage($imagename, $imagetype = 'display', $font="arial.ttf", $shadow = true){
    global $rsgConfig, $mainframe;

		$pepper = 'RSG2Watermarked';
		$salt = JApplication::getCfg('secret');
		$filename = $imagetype.md5($pepper.$imagename.$salt).'.jpg';
		
		if(!JFile::exists(JPATH_WATERMARKED. DS . $filename)){
			if ( $imagetype == 'display') {
				$imagepath = JPATH_DISPLAY . DS . $imagename.".jpg";
			} else {
				$imagepath = JPATH_ORIGINAL . DS . $imagename;
			}
			$imark = new waterMarker();
			$imark->waterMarkText = $rsgConfig->get('watermark_text');
			$imark->imagePath = $imagepath;
			$imark->font= JPATH_RSGALLERY2_ADMIN. DS . "fonts" . DS . $rsgConfig->get('watermark_font');
			$imark->size = $rsgConfig->get('watermark_font_size');
			$imark->shadow= $shadow;
			$imark->angle = $rsgConfig->get('watermark_angle');
			$imark->imageTargetPath = JPATH_WATERMARKED . DS . $filename;
			$imark->mark($imagetype); //draw watermark
		}
		return trim(JURI_SITE,'/') . $rsgConfig->get('imgPath_watermarked') . '/' . $filename ;
    
	}
}//END CLASS WATERMARKER
?>
