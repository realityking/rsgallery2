<form action="index2.php" method="post" name="adminForm">
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<table cellpadding="1" cellspacing="1" border="0" width="100%">
	<tr>
		<td width="220">
			<span class="componentheading">&nbsp;</span>
		</td>
	</tr>
	</table>
	<table class="adminlist">
	<tr>
		<th width="5%" align="left">
		</th>
		<th width="95%" align="left">
			<?php echo $this->item->dir; ?>
		</th>
	</tr>
<?php

$k = 0;
for ($i = 0, $n = count($this->item->files); $i < $n; $i++) {
	$file = $this->item->files[$i];
?>
		<tr class="<?php echo 'row'. $k; ?>">
			<td width="5%">
				<input type="radio" id="cb<?php echo $i;?>" name="filename" value="<?php echo htmlspecialchars( $file ); ?>" onClick="isChecked(this.checked);" />
			</td>
			<td width="95%"><?php echo $file; ?></td>
		</tr>
<?php

$k = 1 - $k;
}
?>
	</table>
	<input type="hidden" name="task" value="editTemplate" />
	<input type="hidden" name="type" value="templateCSS" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_rsgallery2" />
	<input type="hidden" name="rsgOption" value="installer" />
	<input type="hidden" name="template" value="<?php echo $this->item->template; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
