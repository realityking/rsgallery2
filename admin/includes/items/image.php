<?php
/**
* Item class
* @version $Id: image.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

/**
* The generic item class
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgItem_image extends rsgItem{
	/**
	 * rsgResource: display image for this item
	 */
	var $display = null;

	/**
	 * rsgResource: the original image
	 */
	var $original = null;

	function __construct( $type, $mimetype, &$gallery, $row){
		parent::__construct( $type, $mimetype, $gallery, $row );
		
		$this->_determineResources();
	}
	
	/**
	 * @return the thumbnail
	 */
	function thumb(){
		return $this->thumb;
	}
	
	/**
	 * @return the display image
	 */
	function display(){
		return $this->display;
	}
	
	/**
	 * @return the original image
	 */
	function original(){
		return $this->original;
	}

	/**
	 * @todo check if exif_read_data() fails
	 * @return EXIF data
	 */
	function exif(){
		if(!function_exists('exif_read_data')) return false;

		$exif = exif_read_data( $this->original->filePath(), 0, true);

		return $exif;
	}

	function _determineResources(){
		global $rsgConfig;
		
		$gallery_path = $this->gallery->getPath("/");

		$thumb = $rsgConfig->get('imgPath_thumb') . $gallery_path . imgUtils::getImgNameThumb( $this->name );
		$display = $rsgConfig->get('imgPath_display') . $gallery_path . imgUtils::getImgNameDisplay( $this->name );
		$original = $rsgConfig->get('imgPath_original') . $gallery_path . $this->name;
		
		if( file_exists( JPATH_ROOT . $original )){
			// original image exists
			$this->original = new rsgResource( $original );
		}
		else{
			// original image does not exist, therefore display and thumb MUST exist
			$this->display = new rsgResource( $display );
			$this->thumb = new rsgResource( $thumb );

			$this->original =& $this->display;
			return;
		}
		
		// if original was smaller than thumb or display those won't exist
		if( !file_exists( JPATH_ROOT . $thumb )){
			$this->thumb =& $this->original;
			$this->display =& $this->original;
		}
		elseif( !file_exists( JPATH_ROOT . $display )){
			$this->thumb = new rsgResource( $thumb );
			$this->display =& $this->original;
		}
		else{
			$this->thumb = new rsgResource( $thumb );
			$this->display = new rsgResource( $display );
		}
	}
}