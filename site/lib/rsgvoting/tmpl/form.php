<script  type="text/javascript">
function saveVote(id, value) {
	var form = document.rsgvoteform;
	var saveVote = confirm(' <?php echo JText::_('Are you sure you want to vote?');?> ');
	
if (saveVote) {
	form.rating.value = value;
	form.submit();
	}
}
</script>

<form name="rsgvoteform" method="post" action="<?php echo JRoute::_('index.php?option=com_rsgallery2&page=inline&id='.$id);?>" id="rsgvoteform">
<table border="0" width="200">
<tr>
	<td><?php echo JText::_('Vote');?>:</td>
	<td>
	<ul class="star-rating">
		<li><a href="javascript:saveVote(<?php echo $id;?>, 1);" title="<?php echo JText::_('Rate this item 1 out of 5');?>" class="one-star">1</a></li>
		<li><a href="javascript:saveVote(<?php echo $id;?>, 2);" title="<?php echo JText::_('Rate this item 2 out of 5');?>" class="two-stars">2</a></li>
		<li><a href="javascript:saveVote(<?php echo $id;?>, 3);" title="<?php echo JText::_('Rate this item 3 out of 5');?>" class="three-stars">3</a></li>
		<li><a href="javascript:saveVote(<?php echo $id;?>, 4);" title="<?php echo JText::_('Rate this item 4 out of 5');?>" class="four-stars">4</a></li>
		<li><a href="javascript:saveVote(<?php echo $id;?>, 5);" title="<?php echo JText::_('Rate this item 5 out of 5');?>" class="five-stars">5</a></li>
	</ul>
	</td>
</tr>
</table>
<input type="hidden" name="rsgOption" value="rsgVoting" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="rating" value="" />
<input type="hidden" name="id" value="<?php echo $id;?>" />
</form>
