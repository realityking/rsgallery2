<form action="index2.php" method="post" name="adminForm">
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<div class="col50">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>

			<table class="admintable">
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Name' ); ?>:
				</td>
				<td>
					<strong>
						<?php echo JText::_($this->item->row->name); ?>
					</strong>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Version' ); ?>:
				</td>
				<td>
					<strong>
						<?php echo JText::_($this->item->row->version); ?>
					</strong>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Description' ); ?>:
				</td>
				<td>
					<?php echo JText::_($this->item->row->description); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Author' ); ?>:
				</td>
				<td>
					<?php echo JText::_($this->item->row->author) .  "(" .JText::_($this->item->row->authorEmail) . ")" ; ?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Author url' ); ?>:
				</td>
				<td>
					<?php echo JText::_($this->item->row->authorUrl); ?>
				</td>
			</tr>

			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Copyright' ); ?>:
				</td>
				<td>
					<?php echo JText::_($this->item->row->copyright); ?>
				</td>
			</tr>

			</table>
		</fieldset>
	</div>

	<div class="col50">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Parameters' ); ?></legend>
			<?php echo $this->isParamWriteable() ; ?>
			<table class="admintable">
			<tr>
				<td>
	<?php

					if (!is_null($this->item->params)) {
						echo $this->item->params->render();
					} else {
						echo '<i>' . JText :: _('No Parameters') . '</i>';
					}
	?>
				</td>
			</tr>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="type" value="<?php echo $this->item->type; ?>" />
	<input type="hidden" name="task" value="editTemplate" />
	<input type="hidden" name="option" value="com_rsgallery2" />
	<input type="hidden" name="rsgOption" value="installer" />
	<input type="hidden" name="template" value="<?php echo $this->item->template; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>