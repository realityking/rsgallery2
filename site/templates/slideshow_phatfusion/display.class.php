<?php
/**
 * @version $Id: display.class.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package RSGallery2
 * @copyright (C) 2003 - 2007 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted Access' );

/**
 * Slideshow class for RSGallery2
 * Based on Phatfusion from phatfusion.net
 * @package RSGallery2
 * @author Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsgDisplay_slideshow_phatfusion extends rsgDisplay{

	function showSlideShow(){
		global $rsgConfig;
		
		$gallery = rsgGalleryManager::get();
		
		// show nothing if there are no items
		if( ! $gallery->itemCount() )
			return;
		
		$k = 0;
		$text = "";
		foreach ($gallery->items() as $item){
			if( $item->type != 'image' ) return;

			$display = $item->display();
			$thumb = $item->thumb();

			$text .= "<a href=\"".$display->url()."\" class=\"slideshowThumbnail\"><img src=\"".$thumb->url()."\" border=\"0\" /></a>";
			$k++;
		}
		$this->slides = $text;
		$this->galleryname = $gallery->name;
		$this->gid = $gallery->id;
		
		$this->display('slideshow.php');
	}
}