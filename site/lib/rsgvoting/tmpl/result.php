	<table border="0" width="200">
	<tr>
		<td><?php echo JText::_('Rating');?>:</td>
		<td><?php echo rsgVoting::calculateAverage($id);?>&nbsp;/&nbsp;<?php echo rsgVoting::getVoteCount($id);?>&nbsp;<?php echo JText::_('votes');?></td>
   	</tr>
	</table>
