<?php
/**
* Item class
* @version $Id: audio.php 1010 2011-01-26 15:26:17Z mirjam $
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
class rsgItem_audio extends rsgItem{
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
	 * @todo: we need to return a nice generic audio thumbnail
	 */
	function thumb(){
		return $this->thumb;
	}
	
	/**
	 * @return the original image
	 */
	function original(){
		return $this->original;
	}
	
	function _determineResources(){
		global $rsgConfig;
		
		$original = $rsgConfig->get('imgPath_original') . DS . $this->name;
		
		
		if( file_exists( JPATH_ROOT . $original )){
			// original image exists
			$this->original = new rsgResource( $original );
		} else {
			return;
		}
	}
}