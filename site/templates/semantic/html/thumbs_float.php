<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$floatDirection = $rsgConfig->get( 'display_thumbs_floatDirection' );
?>

<ul id="rsg2-thumbsList">
<?php 
foreach( $this->gallery->currentItems() as $item ):
		if( $item->type == 'audio' )
			continue;  // we only handle images

		$thumb = $item->thumb(); ?>
		
	<li <?php echo "style=\"float:$floatDirection;\""; ?> >
		<a href="<?php echo JRoute::_( "index.php?option=com_rsgallery2&page=inline&id=".$item->id ); ?>">
			<!--<div class="img-shadow">-->
			<img alt="<?php echo htmlspecialchars(stripslashes($item->descr), ENT_QUOTES); ?>" src="<?php echo $thumb->url(); ?>" />
			<!--</div>-->
			<span class="rsg2-clr"></span>
			<?php if($rsgConfig->get("display_thumbs_showImgName")): ?>
				<br /><span class='rsg2_thumb_name'><?php echo htmlspecialchars(stripslashes($item->title), ENT_QUOTES); ?></span>
			<?php endif; ?>
		</a>
		<?php if( $this->allowEdit ): ?>
		<div id="rsg2-adminButtons">
			<a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&page=edit_image&id=".$item->id); ?>"><img src="<?php echo JURI::base(); ?>/administrator/images/edit_f2.png" alt="" height="15" /></a>
			<a href="#" onClick="if(window.confirm('<?php echo JText::_('Are you sure you want to delete this image?');?>')) location='<?php echo JRoute::_("index.php?option=com_rsgallery2&page=delete_image&id=".$item->id); ?>'"><img src="<?php echo JURI::base(); ?>/administrator/images/delete_f2.png" alt=""  height="15" /></a>
		</div>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
<div class='rsg2-clr'>&nbsp;</div>