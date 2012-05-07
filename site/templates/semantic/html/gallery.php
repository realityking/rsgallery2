<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
echo('<!-- using template parameter: testParameter = ' . $this->params->get('testParameter') .' -->');
//Show My Galleries link
if ($rsgConfig->get('show_mygalleries')) {
	echo $this->showRsgHeader();
}
//show search box
$this->showSearchBox();

//Show introduction text
?>
<div class="intro_text"><?php echo $this->gallery->description; ?></div>
<?php
//Show limitbox
if( $this->pageNav ):
?>
	<div class="rsg2-pagenav-limitbox">
		<?php echo $this->getGalleryLimitBox(); ?>
	</div>
<?php
endif;

foreach( $this->kids as $kid ):
?>
<div class="rsg_galleryblock">
	<div class="rsg2-galleryList-status"><?php echo $kid->status;?></div>
	<div class="rsg2-galleryList-thumb">
		<?php echo $kid->thumbHTML; ?>
	</div>
	<div class="rsg2-galleryList-text">
		<?php echo $kid->galleryName;?>
		<span class='rsg2-galleryList-newImages'>
			<sup><?php if( $this->gallery->hasNewImages() ) echo JText::_('New!'); ?></sup>
		</span>
		<?php echo $this->_showGalleryDetails( $kid );?>
		<div class="rsg2-galleryList-description"><?php echo stripslashes($kid->description);?>
		</div>
	</div>
	<div class="rsg_sub_url_single">
		<?php $this->_subGalleryList( $kid ); ?>
	</div>
</div>
<?php
endforeach;
?>
<div class="rsg2-clr"></div>
<?php
if($this->gallery->id == 0){

	// show random and latest only in the top gallery 
	
	//Show block with random images 
	$this->showImages("random", 3);
	//Show block with latest images
	$this->showImages("latest", 3);
}
if( $this->pageNav ):
?>

<div class="rsg2-pageNav">
	<?php echo $this->getGalleryPageLinks(); ?>
	<br/>
	<?php echo $this->getGalleryPagesCounter(); ?>
</div>
<div class='rsg2-clr'> </div>
<?php endif; ?>