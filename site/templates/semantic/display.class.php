<?php
/**
 * @version $Id: display.class.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package RSGallery2
 * @copyright (C) 2003 - 2010 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted Access' );

/**
 * Template class for RSGallery2
 * @package RSGallery2
 * @author Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsgDisplay_semantic extends rsgDisplay{

	function inline(){
		$this->display( 'inline.php' );
	}

	/**
	* Show main gallery page
	*/
	function showMainGalleries() {
		global $rsgConfig;
		
		$gallery =  rsgInstance::getGallery();
		$this->gallery = $gallery;
		
		//Get values for page navigation from URL
		$limit = rsgInstance::getInt( 'limitg', $rsgConfig->get('galcountNrs') );
		$limitstart = rsgInstance::getInt( 'limitstartg', 0 );
		//Get number of galleries including main gallery
		$this->kids = $gallery->kids();
		$kidCountTotal = count( $gallery->kids() );

		$this->pageNav = false;
		
		//Show page navigation if selected in backend
		if(( $rsgConfig->get('dispLimitbox') == 1 &&
		    $kidCountTotal > $limit )  ||
			$rsgConfig->get('dispLimitbox') == 2 )
			{
			require_once( JPATH_RSGALLERY2_ADMIN . '/includes/gpagination.php' );
			$this->kids = array_slice( $this->kids, $limitstart, $limit );
			$this->pageNav = new JGPagination($kidCountTotal, $limitstart, $limit );

		}
		$this->display( 'gallery.php' );
		
	}


    /***************************
		non page public functions
	***************************/
	/**
	 * Shows the gallery details block when set in the backend
	 */
	function _showGalleryDetails( $kid ) {
		global $rsgConfig;
		$slideshow = $rsgConfig->get('displaySlideshow') && $kid->itemCount() > 1;
		$owner 		= $rsgConfig->get('showGalleryOwner');
		$size 		= $rsgConfig->get('showGallerySize');
		$date 		= $rsgConfig->get('showGalleryDate');
		
		//Check if items need to be shown
		if ( ($slideshow + $owner + $size + $date) > 0 ) {
			?>
			<div class="rsg_gallery_details">
			<div class="rsg2_details">
			<?php
			if ($slideshow) {
				?>
				<a href='<?php echo JRoute::_("index.php?option=com_rsgallery2&page=slideshow&gid=".$kid->get('id')); ?>'><?php echo JText::_('Slideshow'); ?></a><br />
				<?php
			}
			
			if ($owner) {
				echo JText::_('Owner')." "; echo $kid->owner;?><br />
				<?php
			} 
			
			if ($size) {
				echo JText::_('Size')." "; echo galleryUtils::getFileCount($kid->get('id')). ' ' . JText::_('Images');?><br />
			<?php
			}
			
			if ($date) {
				echo JText::_('Created')." "; echo JHTML::_("date", $kid->date,"%d-%m-%Y" );?><br />
				<?php
			}
			?>
			</div>
			</div>
			<?php
		}
	}
    
    /***************************
		private functions
	***************************/

    /**
     * @todo this alternate gallery view needs to be moved to an html file and added as a template parameter
     */
    function _showDouble( $kids ) {
		global $rsgConfig;
        $i = 0;
		echo"<div class='rsg_double_fix'>";
        foreach ( $kids as $kid ) {
			$i++;
            ?>
            <div class="rsg_galleryblock_double_<?php echo $i?>">
				<div class="rsg2-galleryList-status"><?php echo $kid->status;?></div>
				<div class="rsg2-galleryList-thumb_double">
					<?php echo $kid->thumbHTML; ?>
				</div>
				<div class="rsg2-galleryList-text_double">
					<?php echo $kid->galleryName;?>
					<span class='rsg2-galleryList-newImages'>
						<sup><?php echo galleryUtils::newImages($kid->get('id')); ?></sup>
					</span>
					<?php echo $this->_showGalleryDetails( $kid );?>
					<div class="rsg2-galleryList-description"><?php echo $kid->description;?>
					</div>
				</div>
				<div class="rsg_sub_url"><?php $this->_subGalleryList( $kid ); ?>
				</div>
			</div>
            <?php
			if($i>1){
				$i = 0;
			}
        }
		echo "</div>";
    }
    
    /**
     * @todo this alternate gallery view needs to be moved to an html file and added as a template parameter
     */
    function _showBox( $kids, $subgalleries ) {
        ?>
		<div class="rsg_box_block">
            <?php
            $i = 0;
            foreach ( $kids as $kid ) {
                $i++;
				if($i>3){
					$i = 1;
					}
			 ?>
                <div class="rsg_box_box_<?php echo $i;?>">
                    <div class="rsg_galleryblock">
                    	<div>
							<div class="rsg2-galleryList-status"><?php echo $kid->status; ?></div>
                            <?php echo $kid->galleryName;?>
                            <sup><span class='rsg2-galleryList-newImages'><?php echo galleryUtils::newImages($kid->get('id')); ?></span></sup>
                            <div class='rsg2-galleryList-totalImages'>(<?php echo galleryUtils::getFileCount($kid->get('id')). JText::_(' Images');?>)</div>
                        </div>
						<div>
                        	<div class="rsg2-galleryList-thumb_box">
								<?php echo $kid->thumbHTML; ?>
                        	</div>
                        	<div class="rsg2-galleryList-text_box">
                          		<?php echo $this->_showGalleryDetails( $kid );?>
                        	</div>
                    	</div>
                        <div class="rsg2-galleryList-description_box">
                            	<?php echo $kid->description;?>
						</div>
                        <div class="rsg_sub_url"><?php $this->_subGalleryList( $kid ); ?></span>
                        </div>
                    </div>
                </div>
                <?php
            }
			?>
            </div>
        <?php
    }

	/**
	 * Shows thumbnails for gallery
	 */
	function showThumbs() {
		global $rsgConfig;
		$my = JFactory::getUser();

		$itemCount = $this->gallery->itemCount();

		$limit = $rsgConfig->get("display_thumbs_maxPerPage") ;
		$limitstart = rsgInstance::getInt( 'limitstart' );
		
		//instantiate page navigation
		jimport('joomla.html.pagination');
		$pagenav = new JPagination( $itemCount, $limitstart, $limit );//MK gaat goed: thumbs in gallery
	
		// increase the gallery hit counter
		$this->gallery->hit();
		
		if( !$this->gallery->itemCount() ){
			if( $this->gallery->id ){
				// if gallery is not the root gallery display the message
				echo JText::_('No images in gallery');
			}
			// no items to display, so we can return;
			return;
		}
		//Old rights management. If user is owner or user is Super Administrator, you can edit this gallery
		if(( $my->id <> 0 ) and (( $this->gallery->uid == $my->id ) OR ( $my->usertype == 'Super Administrator' )))
			$this->allowEdit = true;
		else
			$this->allowEdit = false;

		switch( $rsgConfig->get( 'display_thumbs_style' )){
			case 'float':
				$this->display( 'thumbs_float.php' );
			break;
			case 'table':
				$this->display( 'thumbs_table.php' );
			break;
		}
		?>
		<div class="rsg2-pageNav">
				<?php
				if( $itemCount > $limit ){
					echo $pagenav->getPagesLinks();
					echo "<br /><br />".$pagenav->getPagesCounter();
				}
				?>
		</div>
		<?php
	}
    
    /**
     * Shows main item
     */
	function showItem(){
		global $rsgConfig, $mainframe;
		
		$item = rsgInstance::getItem();

    	// increase hit counter
		$item->hit();
		
		?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td><h2 class='rsg2_display_name' align="center"><?php echo htmlspecialchars(stripslashes($item->title), ENT_QUOTES); ?></h2></td>
			</tr>
			<tr>
				<td>
				<div align="center">
					<?php
					$this->currentItem = $item;
					$this->display("item_" .  $item->type . ".php");
    				?>
				</div>
				</td>
			</tr>
			<tr>
				<td><?php $this->_writeDownloadLink( $item->id );?></td>
			</tr>
		</table>
		<?php
	}
 
	/**
	 * Show page navigation for Display image
	 */
	function showDisplayPageNav() {//MK this is where the images are shown with limit=1
		$gallery = rsgGalleryManager::get();
		$itemId = rsgInstance::getInt( 'id', 0 );
		if( $itemId != 0 ){
			// if the item id is set then we need to set the gid instead
			// having the id variable set in the querystring breaks the page navigation

			// i have not found any other way to remove a query variable from the router
			// JPagination uses the router to build the current route, so removing it from the 
			// request variables only does not work.
			$app	= &JFactory::getApplication();
			$router = &$app->getRouter();

			$router->_vars['gid'] = $gallery->id;
			unset($router->_vars['id']);
			
			// set the limitstart so the pagination knows what page to start from
			$itemIndex = $gallery->indexOfItem($itemId);
			$router->setVar("limitstart", $itemIndex);
			rsgInstance::setVar('limitstart', $itemIndex);
		}

		$pageNav = $gallery->getPagination();	
		$pageLinks = $pageNav->getPagesLinks(); 

		?>
		<div align="center">
			<?php echo $pageLinks; ?>
		</div>
		<?php
	}

	/**
	 * Shows details of image
	 */
	function showDisplayImageDetails() {
		global $rsgConfig, $rsgAccess;
		
		$gallery = rsgGalleryManager::get();

		// if no details need to be displayed then exit
		
		if (! ( $rsgConfig->get("displayDesc") || $rsgConfig->get("displayVoting") || $rsgConfig->get("displayComments") || $rsgConfig->get("displayEXIF") ))
			return;

		jimport("joomla.html.pane");
		
		$tabs =& JPane::getInstance("Tabs",array("useCookies" => true));
		echo $tabs->startPane( 'tabs' );
		
		if ( $rsgConfig->get("displayDesc") ) {
			echo $tabs->startPanel(JText::_('Description'), 'rs-description' );
			$this->_showDescription(); 
			echo $tabs->endPanel();
		}
		
		if ( $rsgConfig->get("displayVoting") ) {
			echo $tabs->startPanel(JText::_('Voting'), 'Voting' );
			$this->_showVotes();
			echo $tabs->endPanel();
		}
		
		if ( $rsgConfig->get("displayComments") ) {
			echo $tabs->startPanel(JText::_('Comments'), 'Comments' );
			$this->_showComments();
			echo $tabs->endPanel();
		}
	
		if ($rsgConfig->get("displayEXIF") ) {
			echo $tabs->startPanel(JText::_('EXIF'), 'EXIF' );
			$this->_showEXIF();
			echo $tabs->endPanel();
		}
		echo $tabs->endPanel();
		
	}

    /**
     * Show description
     */
	function _showDescription( ) {
		global $rsgConfig;
		$item = rsgInstance::getItem();
		
		if( $rsgConfig->get('displayHits')):
		?>
		<p class="rsg2_hits"><?php echo JText::_('Hits'); ?> <span><?php echo $item->hits; ?></span></p>
		<?php
		endif;
		
		if ( $item->descr ):
		?>
		<p class="rsg2_description"><?php  echo stripslashes($item->descr); ?></p>
		<?php
		endif;
	}

	
	/**
	* list sub galleries in a gallery
	* @param rsgGallery parent gallery
	*/
	function _subGalleryList( $parent ){
		$kids = $parent->kids();

		if( count( $kids ) == 0 ) return;
		
		echo JText::_('Subgalleries');
		
		$kid = array_shift( $kids );
		
		while( true ){
			?>
			<a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&gid=".$kid->id); ?>">
				<?php echo htmlspecialchars(stripslashes($kid->name), ENT_QUOTES); ?>
				(<?php echo $kid->itemCount(); ?>)</a><?php

			if( $kid = array_shift( $kids ))
				echo ', ';
			else
				break;
		}
	}
}
