<?php
/**
* Item class
* @version $Id: video.php 1010 2011-01-26 15:26:17Z mirjam $
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
class rsgItem_video extends rsgItem{
	/**
	 * rsgResource: display image of first frame of video for this item
	 */
	var $display = null;
	
	/**
	 * rsgResource: the original video
	 */
	var $original = null;

	function __construct( $type, $mimetype, &$gallery, $row){
		parent::__construct( $type, $mimetype, $gallery, $row );
		
		$this->_determineResources();
	}
	
	/**
	 * @return the thumbnail
	 * @todo: we need to return video humbnail
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
	
	function _determineResources(){
		global $rsgConfig;

		require_once( JPATH_RSGALLERY2_ADMIN.'/includes/video.utils.php' );
		
		$gallery_path = $this->gallery->getPath("/");

		$thumb = $rsgConfig->get('imgPath_thumb') . $gallery_path . videoUtils::getImgNameThumb( $this->name );
		$display = $rsgConfig->get('imgPath_display') . $gallery_path . videoUtils::getImgNameDisplay( $this->name );
		$original = $rsgConfig->get('imgPath_original') . $gallery_path . $this->name;
		
		if( !JFile::exists( JPATH_ROOT . $original )){
			$this->original = $display;
		}
		
		$this->thumb = new rsgResource( $thumb );
		$this->display = new rsgResource( $display );
		$this->original = new rsgResource( $original );
		
	}
}
