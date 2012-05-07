<tr class="<?php echo "row".$this->item->index % 2; ?>" <?php echo $this->item->style; ?>>
	<td><?php echo $this->pagination->getRowOffset( $this->item->index ); ?></td>
	<td>
		<input type="radio" id="cb<?php echo $this->item->index;?>" name="template" value="<?php echo $this->item->id; ?>" onclick="isChecked(this.checked);" <?php echo $this->item->cbd; ?> />
		<span class="bold"><?php echo $this->item->name; ?></span>
	</td>
	<td align="center"><?php if($this->item->isDefault){ echo JHTML::_('image.administrator', 'tick.png'); }?> </td>
	<td align="center"><?php echo @$this->item->version != '' ? $this->item->version : '&nbsp;'; ?></td>
	<td><?php echo @$this->item->creationdate != '' ? $this->item->creationdate : '&nbsp;'; ?></td>
	<td>
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'Author Information' );?>::<?php echo $this->item->author_information; ?>">
			<?php echo @$this->item->author != '' ? $this->item->author : '&nbsp;'; ?>
		</span>
	</td>
</tr>
