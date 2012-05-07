<form action="index2.php" method="post" name="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<table class="adminform">
	<tr>
		<th>
			<?php echo $this->item->path; ?>
		</th>
	</tr>
	<tr>
		<td>
			<textarea style="width:100%;height:500px" cols="110" rows="25" name="csscontent" class="inputbox"><?php echo $this->item->content; ?></textarea>
		</td>
	</tr>
	</table>

	<div class="clr"></div>

	<input type="hidden" name="template" value="<?php echo $this->item->template; ?>" />
	<input type="hidden" name="type" value="templateCSS" />
	<input type="hidden" name="rsgOption" value="installer" />
	<input type="hidden" name="option" value="com_rsgallery2" />
	<input type="hidden" name="filename" value="<?php echo $this->item->filename;?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
