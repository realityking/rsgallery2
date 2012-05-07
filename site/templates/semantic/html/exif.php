<?php defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="imageExif">
	<tr>
		<th><?php echo JText::_('Section'); ?></th>
		<th><?php echo JText::_('Name'); ?></th>
		<th><?php echo JText::_('Value'); ?></th>
	</tr>
<?php
		foreach ($this->exif as $key => $section):
			foreach ($section as $name => $val):
?>
	<tr>
		<td class="exifKey"><?php echo JText::_($key);?></td>
		<td class="exifName"><?php echo JText::_($name);?></td>
		<td class="exifVal"><?php echo JText::_($val);?></td>
	</tr>
<?php
			endforeach;
		endforeach;
?>
</table>