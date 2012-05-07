<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$cols = $rsgConfig->get( 'display_thumbs_colsPerPage' );
$i = 0;
?>

<table id="rsg2-thumbsList" border="0">
	<?php foreach( $this->gallery->currentItems() as $item ):
		if( $item->type == 'audio' )
			continue;  // we only handle images

		$thumb = $item->thumb();
		
		if( $i % $cols== 0) echo "<tr>\n";
		?>
			<td>
				<div class="shadow-box">
				<div class="img-shadow">
				<a href="<?php echo JRoute::_( "index.php?option=com_rsgallery2&page=inline&id=".$item->id ); ?>">
					<img src="<?php echo $thumb->url();?>" alt="<?php echo htmlspecialchars(stripslashes($item->descr), ENT_QUOTES); ?>"/>
				</a>
				</div>
				</div>
				<div class="rsg2-clr"></div>
				<?php if($rsgConfig->get("display_thumbs_showImgName")): ?>
				<br />
				<span class="rsg2_thumb_name">
					<?php echo htmlspecialchars(stripslashes($item->title), ENT_QUOTES); ?>
				</span>
				<?php endif; ?>
				<?php if( $this->allowEdit ): ?>
				<div id="rsg2-adminButtons">
					<a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&page=edit_image&id=".$item->id); ?>"><img src="<?php echo JURI::base(); ?>/administrator/images/edit_f2.png" alt="" height="15" /></a>
					<a href="#" onClick="if(window.confirm('<?php echo JText::_('Are you sure you want to delete this image?');?>')) location='<?php echo JRoute::_("index.php?option=com_rsgallery2&page=delete_image&id=".$item->id); ?>'"><img src="<?php echo JURI::base(); ?>/administrator/images/delete_f2.png" alt="" height="15" /></a>
				</div>
				<?php endif; ?>
			</td>
		<?php if( ++$i % $cols == 0) echo "</tr>\n"; ?>
	<?php endforeach; ?>
	<?php if( $i % $cols != 0) echo "</tr>\n"; ?>
</table>