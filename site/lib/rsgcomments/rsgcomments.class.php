<?php
/**
* Comments plugin for RSGallery2
* @version $Id: rsgcomments.class.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2007 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/
defined( '_JEXEC' ) or die( 'Access Denied.' );
/**
 * Class for the comments plugin
 * @author Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsgComments {
	var $_buttons;
	var $_emoticons;
	var $_support_emoticons;
	var $_support_pictures;
	var $_support_UBBcode;
	var $_hide;
/**
 * Constructor
 */
 
 function rsgComments() {
	global $mainframe;
 	$this->_buttons = array(
		"b" 	=> "ubb_bold.gif",
		"i" 	=> "ubb_italicize.gif",
		"u" 	=> "ubb_underline.gif",
		"url" 	=> "ubb_url.gif",
		"quote" => "ubb_quote.gif",
		"code" 	=> "ubb_code.gif",
		"img" 	=> "ubb_image.gif"
		);
	$this->_emoticons = array(
		":D" 			=> "icon_biggrin.gif",
		":)" 			=> "icon_smile.gif",
		":(" 			=> "icon_sad.gif",	
		":O" 			=> "icon_surprised.gif",
		":shock:" 		=> "icon_eek.gif",
		":confused:" 	=> "icon_confused.gif",
		"8)" 			=> "icon_cool.gif",
		":lol:" 		=> "icon_lol.gif",
		":x" 			=> "icon_mad.gif",
		":P" 			=> "icon_razz.gif",
		":oops:" 		=> "icon_redface.gif",
		":cry:" 		=> "icon_cry.gif",
		":evil:" 		=> "icon_evil.gif",
		":twisted:" 	=> "icon_twisted.gif",
		":roll:" 		=> "icon_rolleyes.gif",
		":wink:" 		=> "icon_wink.gif",
		":!:" 			=> "icon_exclaim.gif",
		":?:" 			=> "icon_question.gif",
		":idea:" 		=> "icon_idea.gif",
		":arrow:" 		=> "icon_arrow.gif"
		);	
	$this->_emoticons_path 		= JURI_SITE."/components/com_rsgallery2/lib/rsgcomments/emoticons/default/";
	$this->_support_emoticons 	= 1; //Need to retrieve this from Control Panel Settings
	$this->_support_pictures	= 1; //Need to retrieve this from Control Panel Settings
	$this->_support_UBBcode		= 1; //Need to retrieve this from Control Panel Settings
	$this->_hide				= 0; //Need to retrieve this from Control Panel Settings
 }
/**
 * Shows toolbar for BBCode editor
 */
function showButtons() {
	global $mainframe;
	//Define codes and corresponding images for toolbar

	echo "<div style='float: left;'>";
	foreach ($this->_buttons as $tag => $filename) {
		?>
		<a href='javascript:insertUBBTag("<?php echo $tag;?>")'><img border='0' src='<?php echo JURI_SITE;?>/components/com_rsgallery2/lib/rsgcomments/images/<?php echo $filename;?>' class='buttonBB' name='bb' alt='[<?php echo $tag;?>]' /></a>&nbsp;
		<?php
	}
	?>
	</div>
	<div style='float: left;'>
	<select name='menuColor' class='select' onchange='fontColor()'>
	  	<option><?php echo JText::_('-color-')?></option>
		<option><?php echo JText::_('aqua')?></option>
		<option><?php echo JText::_('black')?></option>
		<option><?php echo JText::_('blue')?></option>
		<option><?php echo JText::_('fuchsia')?></option>
		<option><?php echo JText::_('gray')?></option>
		<option><?php echo JText::_('green')?></option>
		<option><?php echo JText::_('lime')?></option>
		<option><?php echo JText::_('maroon')?></option>
		<option><?php echo JText::_('navy')?></option>
		<option><?php echo JText::_('olive')?></option>
		<option><?php echo JText::_('purple')?></option>
		<option><?php echo JText::_('red')?></option>
		<option><?php echo JText::_('silver')?></option>
		<option><?php echo JText::_('teal')?></option>
		<option><?php echo JText::_('white')?></option>
		<option><?php echo JText::_('yellow')?></option>
	</select>&nbsp;
	<select name='menuSize' class='select' onchange='fontSize()'>
		<option><?php echo JText::_('-size-')?></option>
		<option><?php echo JText::_('tiny')?></option>
		<option><?php echo JText::_('small')?></option>
		<option><?php echo JText::_('medium')?></option>
		<option><?php echo JText::_('large')?></option>
		<option><?php echo JText::_('huge')?></option>
	</select>
	</div>
	<?php
}

/**
 * Shows block of smilies for BBCode editor
 */
function showSmilies() {
	global $mainframe;
	
	$i = 0;
	foreach ($this->_emoticons as $tag => $filename) {
		?>
		<span class='emoticonseparator'>
			<span class='emoticon'>
				<a href='javascript:emoticon("<?php echo $tag;?>")'><img src='<?php echo JURI_SITE;?>/components/com_rsgallery2/lib/rsgcomments/emoticons/default/<?php echo $filename;?>' border='0' alt='' /></a>
			</span>
		</span>
		<?php
		$i++;
		if ($i % 3 == 0) {
			?>
			<div class='emoticonseparator'></div>
			<?php
		}
	}
}
/**
 * Retrieves raw text and converts bbcode to HTML
 */
function parseUBB($html, $hide = 0) {
    $html = str_replace(']www.', ']http://www.', $html);
    $html = str_replace('=www.', '=http://www.', $html);
    $patterns = array('/\[b\](.*?)\[\/b\]/i',
        '/\[u\](.*?)\[\/u\]/i',
        '/\[i\](.*?)\[\/i\]/i',
        '/\[url=(.*?)\](.*?)\[\/url\]/i',
        '/\[url\](.*?)\[\/url\]/i',
        '#\[email\]([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#',
        '#\[email=([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/email\]#',
        '/\[font=(.*?)\](.*?)\[\/font\]/i',
        '/\[size=(.*?)\](.*?)\[\/size\]/i',
        '/\[color=(.*?)\](.*?)\[\/color\]/i');
    $replacements = array('<b>\\1</b>',
        '<u>\\1</u>',
        '<i>\\1</i>',
        '<a href=\'\\1\' title=\'Visit \\1\'>\\2</a>',
        '<a href=\'\\1\' title=\'Visit \\1\'>\\1</a>',
        '<a href=\'mailto:\\1\'>\\1</a>',
        '<a href=\'mailto:\\1\'>\\3</a>',
        '<span style=\'font-family: \\1\'>\\2</span>',
        '<span style=\'font-size: \\1\'>\\2</span>');
    if ($hide) 
    	$replacements[] = '\\2';
    else 
    	$replacements[] = '<span style=\'color: \\1\'>\\2</span>';
    $html = preg_replace($patterns, $replacements, $html);
    return $html;
    }

/**
 * Replaces emoticons code with emoticons 
 */
function parseEmoticons($html) {
    foreach ($this->_emoticons as $ubb => $icon) {
        $html = str_replace($ubb, "<img src='" . $this->_emoticons_path . $icon . "' border='0' alt='' />", $html);
    }
    return $html;
}

/**
 * Parses an image element to HTML
 */
function parseImgElement($html) {
        return preg_replace('/\[img\](.*?)\[\/img\]/i', '<img src=\'\\1\' alt=\'Posted image\' />', $html);
}

/**
 * Parse a quote element to HTML
 */
function parseQuoteElement($html) {
        $q1 = substr_count($html, "[/quote]");
        $q2 = substr_count($html, "[quote=");
        if ($q1 > $q2) $quotes = $q1;
        else $quotes = $q2;
        $patterns = array("/\[quote\](.+?)\[\/quote\]/is",
            "/\[quote=(.+?)\](.+?)\[\/quote\]/is");
        $replacements = array(
						"<div class='quote'><div class='genmed'><b>".JText::_('Quote')."</b></div><div class='quotebody'>\\1</div></div>",
            			"<div class='quote'><div class='genmed'><b>\\1".JText::_('Wrote')."</b></div><div class='quotebody'>\\2</div></div>"
            			);
        while ($quotes > 0) {
            $html = preg_replace($patterns, $replacements, $html);
            $quotes--;
        }
        return $html;
    }

function code_unprotect($val) {
    $val = str_replace("{ : }", ":", $val);
    $val = str_replace("{ ; }", ";", $val);
    $val = str_replace("{ [ }", "[", $val);
    $val = str_replace("{ ] }", "]", $val);
    $val = str_replace(array("\n\r", "\r\n"), "\r", $val);
    $val = str_replace("\r", '&#13;', $val);
	//return filter($val, true);
	return $val;
    }

function parseCodeElement($html) {
	if (preg_match_all('/\[code\](.+?)\[\/code\]/is', $html, $replacementI)) {
		foreach($replacementI[0] as $val) $html = str_replace($val, $this->code_unprotect($val), $html);
    }
    $pattern = array();
    $replacement = array();
    $pattern[] = "/\[code\](.+?)\[\/code\]/is";
    $replacement[] = "<div class='code'><div class='genmed'><b>".JText::_('Code')."</b></div><div class='codebody'><pre>\\1</pre></div></div>";
    return preg_replace($pattern, $replacement, $html);
    }

/**
 * Parse a BB-encoded message to HTML
 */
function parse( $html ) {
        
		//$html = $this->_comment;
        if ($this->_support_emoticons) $html = $this->parseEmoticons($html);
        if ($this->_support_pictures) $html = $this->parseImgElement($html);
        if ($this->_support_UBBcode) {
            $html = $this->parseUBB($html, $this->_hide);
            $html = $this->parseCodeElement($html);
            $html = $this->parseQuoteElement($html);
            $html = stripslashes($html);
        }
        if ($this->_hide) $html = "<span class='hide'>$html</span>";
		return str_replace('&#13;', "\r", nl2br($html));
    }
/**
 * Shows the form for the 
 */
function editComment( $item_id ) {
	global $rsgConfig, $mainframe ;
	$my =& JFactory::getUser();/* JPATH_SITE is only there to accomodate SecurityImages for now*/
	$doc =& JFactory::getDocument();
	$doc->addScript(JURI_SITE."/components/com_rsgallery2/lib/rsgcomments/js/client.js");
	$doc->addStyleSheet(JURI_SITE."/components/com_rsgallery2/lib/rsgcomments/rsgcomments.css");

	if( ! $rsgConfig->get( 'comment_allowed_public' )){
		if( ! $my->id )
			return;
	}
	?>
	<script type="text/javascript">
        function submitbutton(pressbutton) {
            var form = document.rsgcommentform;
            if (pressbutton == 'cancel') {
                form.reset();
                return;
            }
        
        // do field validation
        if (form.tname.value == "") {
            alert( '<?php echo JText::_('You should enter your name'); ?>' );
        }
        else if (form.tcomment.value == ""){
            alert( '<?php echo JText::_('No comment entered'); ?>' );
        }
        else{
            form.submit();
        }
        }
    </script>
    
	<form name="rsgcommentform" method="post" action="<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=rsgComments&task=save");?>">
	<table border="0" width="100%" class="adminForm">
	<tr>
		<td colspan="2"><h2><?php echo JText::_('Add Comment');?></h2></td>
	</tr>
	<tr>
		<td><?php echo JText::_('Your Name');?>:</td>
		<td><input name='tname' type='text' class='inputbox' size='40' value='<?php if (!$my->username == '') echo $my->username;?>' /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('Title');?>:</td>
		<td><input name='ttitle' type='text' class='inputbox' size='40'/></td>
	</tr>
	<tr>
		<td><?php echo JText::_('Comment text');?>:</td>
		<td><div class='buttoncontainer'><?php rsgComments::showButtons();?></div></td>
	</tr>
	<tr>
		<td><?php rsgComments::showSmilies();?></td>
		<td><textarea name='tcomment' class='inputbox' cols='40' rows='10'></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<?php
			//Implement security images only for 
			if ( $rsgConfig->get('comment_security') == 1 ) {
				?>
			<img src="<?php echo JRoute::_("index.php?option=com_securityimages&task=displayCaptcha"); ?>">  
			<br />  
			<?php echo JText::_('Enter what you see in the image above:');?><input type="text" name="securityImageRSGallery2" />  
			<?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">
			<input type="button" class="button" value="<?php echo JText::_('COM_RSGALLERY2_POST');?>" onclick="submitbutton('save')" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="item_id" value="<?php echo $item_id;?>" />
	<input type="hidden" name="rsgOption" value="rsgComments" />
	<input type="hidden" name="catid" value="<?php echo rsgInstance::getInt('catid'  , null);?>" />
	</form>
	<a name="comment2"></a>
	<?php
}

function showComments( $item_id ) {
	global $database;
	
	// Get the current JUser object
	$user = &JFactory::getUser();
	$deleteComment = false;

	// user is admin or super admin and can delete the comment
	if( $user->get('gid') > 22 ):
		$deleteComment = true;
	?>
	<script type="text/javascript">
	//<![CDATA[
	function delComment(id, item_id, catid) {
		var delCom = confirm('<?php echo JText::_("Delete Comment")?>' + id );
		
		if (delCom) {
			window.location = '<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=rsgComments&task=delete", false); ?>&id='+id+'&item_id='+item_id+'&catid='+catid;
		}
	}
	//]]>
	</script>
	<?php
	endif;

	$comments = rsgComments::_getList( $item_id );

	if (count($comments) > 0) {
		?>
		<div id="comment">
		<table width="100%" class="comment_table">
			<tr>
				<td class="title" width="25%"><?php echo JText::_('Comments')?></td>
				<td class="title" width="50%"><?php echo JText::_('comments added')?></td>
				<td class="title"><div class="addcomment"><a class="special" href="#comment2"><?php echo JText::_('Add Comment');?></a></div></td>
			</tr>
		</table>
		<br />
		</div>
		<?php
		foreach ($comments as $comment) {
			$catid = galleryUtils::getCatIdFromFileId($comment['item_id']);
			?>
			<div id="comment">
			<table width="100%" class="comment_table">
			<tr>
				<td colspan="2" class="title"><span class='posttitle'><?php echo $comment['subject'];?></span></td>
			</tr>
			<tr>
                <td valign="top" width="100"><span class="postusername"><?php echo $comment['user_name'] ;?></span></td></td>
				<td valign="top" class="content_area">
				<?php echo JHTML::_("date",$comment['datetime']);?>
				<hr />
				<?php echo rsgComments::parse( $comment['comment'] );?>
				<?php if ( $deleteComment ): ?>
					<div style="float:right;"><a href="javascript:void(0);" onclick="javascript:delComment(<?php echo $comment['id'];?>, <?php echo $comment['item_id'];?>, <?php echo $catid;?>);"><?php echo JText::_('Delete Comment')?></a></div>
				<?php endif; ?>
				</td>
			</tr>
			</table>
			<br />
			</div>
			<?php
		}
	} else {

		?>
		<div id="comment">
		<table width="100%" class="comment_table">
			<tr>
				<td class="title"><span class='posttitle'><?php echo JText::_('No comments yet!')?></span></td>
			</tr>
		</table>
		</div>
		<?php
	}
}

/**
 * returns a comment object for a specific id
 * @param id
 */
function _get( $id ){
    $database =& JFactory::getDBO();
	//Check value type
    if( !is_numeric( $id )) die("item id is not a number: $id");
    
    //Retrieve 
    $database->setQuery("SELECT * FROM #__rsgallery2_comments ".
                        "WHERE id = '$id' " .
                        "AND published = '1' ".
                        "ORDER BY ordering ASC ");

    $results = $database->loadAssocList();

    if( count($results)==0){
        $row = 0;
    } else {
    	$row = $results[0];
    }

	return $row;
}
/**
 * Returns an array of comment objects for a specific item_id
 * @param int item_id
 */
function _getList( $item_id ) {
	$database =& JFactory::getDBO();
	
	$result = array();
	$sql = "SELECT * FROM #__rsgallery2_comments " .
			"WHERE item_id = '$item_id' " .
			"ORDER BY datetime DESC";
	$database->setQuery( $sql );
	$result = $database->loadAssocList();
	/*
	foreach ($result as $id)
		$comment[] = $id;	
	
	return $comment;
	*/
	return $result;
}
}//end class
?>
