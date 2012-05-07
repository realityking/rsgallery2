<?php
/**
* Images option for RSGallery2 - HTML display code
* @version $Id: images.html.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Handles HTML screens for image option 
 * @package RSGallery2
 */
class html_rsg2_images {

	function showImages( $option, &$rows, &$lists, &$search, &$pageNav ) {
		global $rsgOption, $option, $rsgConfig;
		$my = JFactory::getUser();
		?>
 		<form action="index2.php" method="post" name="adminForm">
		<table border="0" width="100%">
		<tr>
			<td align="left" width="50%">
			&nbsp;
			</td>
			<td align="right" width="50%">
				<?php echo JText::_('Copy/Move:')?>
				<?php echo $lists['move_id'];?>
				<?php echo JText::_('Filter:')?>
				<input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
				<?php echo $lists['gallery_id'];?>
				
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="5">ID</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title"><?php echo JText::_('Title (filename)')?><?php echo JText::_( 'Num' ); ?></th>
			<th width="5%"><?php echo JText::_('Published')?></th>
			<th colspan="2" width="5%"><?php echo JText::_('Reorder')?></th>
			<th width="2%"><?php echo JText::_('Order')?></th>
			<th width="2%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">
				<img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" />
			</a>
			</th>
			<th width="15%" align="left"><?php echo JText::_('Gallery')?></th>
			<th width="5%"><?php echo JText::_('Hits')?></th>
			<th width=""><?php echo JText::_('Date & time')?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_rsgallery2&rsgOption='.$rsgOption.'&task=editA&hidemainmenu=1&id='. $row->id;

			$task 	= $row->published ? 'unpublish' : 'publish';
			$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
			$alt 	= $row->published ? 'Published' : 'Unpublished';

			$checked 	= JHTML::_('grid.checkedout', $row, $i );

			$row->cat_link 	= 'index2.php?option=com_rsgallery2&rsgOption=galleries&task=editA&hidemainmenu=1&id='. $row->gallery_id;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $row->id; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->title;
				} else {
					$gallery = rsgGalleryManager::getGalleryByItemID($row->id);
					if($gallery !== null){
						if (is_a( $gallery->getItem($row->id), 'rsgItem_audio' ) ) {
							$type = 'audio';
						} else {
							$type = 'image';
						}
					}
					echo JHTML::tooltip('<img src="'.JURI_SITE.$rsgConfig->get('imgPath_thumb').'/'.$row->name.'.jpg" alt="'.$row->name.'" />',
					 JText::_('Edit Images'),
					 $row->name,
					 htmlspecialchars(stripslashes($row->title), ENT_QUOTES).'&nbsp;('.$row->name.')',
					 $link,
					1);
				}
				?>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i, ($row->gallery_id == @$rows[$i-1]->gallery_id) ); ?>
				</td>
	  			<td>
				<?php echo $pageNav->orderDownIcon( $i, $n, ($row->gallery_id == @$rows[$i+1]->gallery_id) ); ?>
				</td>
				<td colspan="2" align="center">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
				<a href="<?php echo $row->cat_link; ?>" title="Edit Category">
				<?php echo $row->category; ?>
				</a>
				</td>
				<td align="left">
				<?php echo $row->hits; ?>
				</td>
				<td align="left">
				<?php echo $row->date;?>
				</td>
			</tr>
			</tbody>
			
			<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosWeblink The weblink object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function editImage( &$row, &$lists, &$params, $option ) {
		global $rsgOption;
		jimport("joomla.filter.output");

		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES );
		JHTML::_('behavior.formvalidation');
		$editor =& JFactory::getEditor();
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "<?php echo JText::_('PLEASE PROVIDE A VALID IMAGE TITLE');?>" );
			} else if (form.gallery_id.value <= "0"){
				alert( "<?php echo JText::_('YOU MUST SELECT A GALLERY.');?>" );
			} else {
				<?php echo $editor->save( 'descr' ) ;?>
				submitform( pressbutton );
			}
		}
		</script>
		
		<form action="index2.php" method="post" name="adminForm" id="adminForm" class="form-validate" >
		<table class="adminheading">
			<tr>
				<th><?php echo JText::_('Item')?>:<small><?php echo $row->id ? JText::_('Edit') : JText::_('New');?></small></th>
			</tr>
		</table>

		<table width="100%">
			<tr>
				<td width="60%" valign="top">
					<table class="adminform">
						<tr>
							<th colspan="2"><?php echo JText::_('Details')?></th>
						</tr>
						<tr>
							<td width="20%" align="right"><?php echo JText::_('Name')?></td>
							<td width="80%">
								<input class="text_area required" type="text" name="title" size="50" maxlength="250" value="<?php echo $row->title;?>" />
							</td>
						</tr>
						<tr>
							<td width="20%" align="right"><?php echo JText::_('COM_RSGALLERY2_ALIAS')?></td>
							<td width="80%">
								<input class="text_area" type="text" name="alias" size="50" maxlength="250" value="<?php echo $row->alias;?>" />
							</td>
						</tr>

						<tr>
							<td width="20%" align="right"><?php echo JText::_('Filename')?></td>
							<td width="80%"><?php echo $row->name;?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo JText::_('Gallery')?></td>
							<td><?php echo $lists['gallery_id']; ?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo JText::_('Description')?></td>
							<td>
								<?php
								// parameters : areaname, content, hidden field, width, height, rows, cols
								echo $editor->display('descr',  $row->descr , '100%', '200', '10', '20' ,false) ; ?>
							</td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo JText::_('Ordering')?></td>
							<td><?php echo $lists['ordering']; ?></td>
						</tr>
						<tr>
							<td valign="top" align="right"><?php echo JText::_('Published')?></td>
							<td><?php echo $lists['published']; ?></td>
						</tr>
					</table>
				</td>
				<td width="40%" valign="top">
					<table class="adminform">
						<tr>
							<th colspan="1"><?php echo JText::_('Item preview')?></th>
						</tr>
						<tr>
							<td>
								<div align="center">
								<?php
								$item = rsgGalleryManager::getItem( $row->id );

								$original = $item->original();
								$thumb 		= $item->thumb();

								switch($item->type){
									case "audio":{
									?>
									<object type="application/x-shockwave-flash" width="400" height="15" data="<?php echo JURI_SITE ?>/components/com_rsgallery2/flash/xspf/xspf_player_slim.swf?song_title=<?php echo $row->name?>&song_url=<?php echo audioUtils::getAudio($row->name)?>"><param name="movie" value="<?php echo JURI_SITE ?>/components/com_rsgallery2/flash/xspf/xspf_player_slim.swf?song_title=<?php echo $item->title;?>&song_url=<?php echo $original->url();?>" /></object>
									<?php
										break;
									}
									case "video":{
										// OS flv player from http://www.osflv.com
									?>
									<object type="application/x-shockwave-flash" 
											width="400" 
											height="300" 
											data="<?php echo JURI_SITE ?>/components/com_rsgallery2/flash/player.swf?movie=<?php echo $display->name; ?>" >
											<param name="movie" value="<?php echo JURI_SITE ?>/components/com_rsgallery2/flash/player.swf?movie=<?php echo $display->name; ?>" />
											<embed src="<?php echo JURI_SITE ?>/components/com_rsgallery2/flash/player.swf?movie=<?php echo $display->url(); ?>" 
													width="400" 
													height="340" 
													allowFullScreen="false" 
													type="application/x-shockwave-flash">
									</object>
									<?php
										break;
									}
									case "image":{
										$display	= $item->display();
									?>
										<img src="<?php echo $display->url() ?>" alt="<?php echo htmlspecialchars( stripslashes( $item->descr ), ENT_QUOTES );?>" />
									<?php
										break;
									}
									default:
									{
										?> Unsuported item <?php
										break;	
									}
								}
								?>
									<br />
								</div>
							</td>
						</tr>
					</table>
					<table class="adminform">
						<tr>
							<th colspan="1"><?php echo JText::_('Parameters')?></th>
						</tr>
						<tr>
							<td><?php echo $params->render();?>&nbsp;</td>
						</tr>
					</table>
					<table class="adminform">
						<tr>
							<th colspan="1"><?php echo JText::_('Links to image')?></th>
						</tr>
						<tr>
							<td>
								<table width="100%" class="imagelist">
									<?php if ( $item->type == 'image' || $item->type == "video" ) {?>
									<tr>
										<td width="40%" align="right" valign="top"> <a href="<?php echo $thumb->url();?>" target="_blank" alt="<?php echo $item->name;?>"><?php echo JText::_('Thumb'); ?></a>:</td>
										<td><input type="text" name="thumb_url" class="text_area" size="50" value="<?php echo $thumb->url();?>" /></td>
									</tr>
									<tr>
										<td width="40%" align="right" valign="top"><a href="<?php echo $display->url();?>" target="_blank" alt="<?php echo $item->name;?>"><?php echo JText::_('Display'); ?></a>:</td>
										<td ><input type="text" name="display_url" class="text_area" size="50" value="<?php echo $display->url();?>" /></td>
									</tr>
									<?php }?>
									<tr>
										<td width="40%" align="right" valign="top"><a href="<?php echo $original->url();?>" target="_blank" alt="<?php echo $item->name;?>"><?php echo JText::_('Original'); ?></a>:</td>
										<td><input type="text" name="original_url" class="text_area" size="50" value="<?php echo $original->url();?>" /></td>
									</tr>
								</table>		
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="name" value="<?php echo $row->name; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosWeblink The weblink object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function uploadImage( $lists, $option ) {
		global $rsgOption;
		JHTML::_('behavior.formvalidation');
		$editor =& JFactory::getEditor();
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
        
			// do field validation
			if (form.gallery_id.value <= 0){
				alert( "<?php echo JText::_('You must select a gallery.')?>" );
			} else if (form.images.value == ''){
				alert( "<?php echo JText::_('No file was selected in one or more fields.')?>" );
			} else {
					<?php echo $editor->save('descr') ; ?>
				submitform( pressbutton );
			}
		}
		</script>
		<?php 
		//translated text into javascript -> javascript to .php file
		/*<script type="text/javascript" src="<?php echo JURI_SITE;?>/administrator/components/com_rsgallery2/includes/script.php"></script>*/
		require_once(JPATH_RSGALLERY2_ADMIN . '/includes/script.php');
		?>
		<form action="index2.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo JText::_('Item')?>:
			<small>
			<?php echo JText::_('Upload')?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo JText::_('Upload details')?>
					</th>
				</tr>
				<tr>
					<td width="20%" align="right"></td>
					<td width="80%"><?php echo $lists['gallery_id']?></td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo JText::_('Generic Description')?>
					</td>
					<td>
				<?php echo $editor->display( 'descr',  '' , '100%', '200', '10', '20' ,false ) ; ?>
					</td>
				</tr>
				</table>
				<br />
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo JText::_('Item files')?>
					</th>
				</tr>
				<tr>
					<td  width="20%" valign="top" align="right">
					<?php echo JText::_('Items')?>
					</td>
					<td width="80%">
						<?php echo JText::_('Title')?>&nbsp;<input class="text" type="text" id= "title" name="title[]" value="" size="60" maxlength="250" /><br /><br />
						<?php echo JText::_('File')?>&nbsp;<input type="file" size="48" id="images" name="images[]" class="required" /><br /><hr />
    					<span id="moreAttachments"></span>
    					<a href="javascript:addAttachment(); void(0);"><?php echo JText::_('(more files)')?></a><br />
    					<noscript><input type="file" size="48" name="images[]" /><br /></noscript>
					</td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1">
					<?php echo JText::_('Parameters')?>
					</th>
				</tr>
				<tr>
					<td>
					<?php /*echo $params->render();*/?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
	
	function batchupload($option) {
        global $rsgConfig, $task, $rsgOption;
        $FTP_path = $rsgConfig->get('ftp_path');
        $size = round( ini_get('upload_max_filesize') * 1.024 );
        ?>
        <script language="javascript" type="text/javascript">

        function submitbutton(pressbutton) {
            var form = document.adminForm;
 
            for (i=0;i<document.forms[0].batchmethod.length;i++) {
                if (document.forms[0].batchmethod[i].checked) {
                    upload_method = document.forms[0].batchmethod[i].value;
                    }
            }
            
            for (i=0;i<document.forms[0].selcat.length;i++) {
                if (document.forms[0].selcat[i].checked) {
                    selcat_method = document.forms[0].selcat[i].value;
                    }
            }
        if (pressbutton == 'controlPanel') {
        	location = "index2.php?option=com_rsgallery2";
        	return;
        }
        if (pressbutton == 'batchupload')
            {
            // do field validation
            if (upload_method == 'zip')
                {
                if (form.zip_file.value == '')
                    {
                    alert( "<?php echo JText::_('ZIP-upload selected but no file chosen');?>" );
                    }        
               else if (form.xcat.value <= '0' & selcat_method == '1')
                    {
                    alert("<?php echo JText::_('Please choose a category first');?>");
                    }
                else
                    {
                    form.submit();
                    }
                }
            else if (upload_method == 'ftp')
            	{
            	if (form.ftppath.value == '')
            		{
            		alert( " <?php echo JText::_('FTP upload chosen but no FTP-path provided');?>" );	
            		}
            	else if (form.xcat.value == '0' & selcat_method == '1')
            		{
					alert("<?php echo JText::_('Please choose a category first');?>");
            		}
            	else
            		{
            		form.submit();
            		}
            	}
            }
        }
        </script>

        <form name="adminForm" action="index2.php" method="post" enctype="multipart/form-data">
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td>
                <table class="adminform">
                <tr>
                    <th colspan="3"><font size="4"><?php echo JText::_('Step 1');?></font></th>
                </tr>
                <tr>
                    <td width="200"><strong><?php echo JText::_('Specify upload method');?></strong>
                    <?php
                    echo JHTML::tooltip( JText::_('BATCH_METHOD_TIP'), JText::_('Specify upload method') );
                    ?>
                    </td>
                    <td width="200">
                        <input type="radio" value="zip" name="batchmethod" CHECKED/>
                        <?php echo JText::_('ZIP-file'); ?></td>
                    <td>
                        <input type="file" name="zip_file" size="20" />
                        <div style=color:#FF0000;font-weight:bold;font-size:smaller;>
                        <?php echo JText::_('Upload limit is').' ' . $size .' '.JText::_('Megabytes (set in php.ini)');?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="radio" value="ftp" name="batchmethod" />
                        <?php echo JText::_('FTP-path');?> <?php echo JHTML::tooltip( JText::_('BATCH_FTP_PATH_OVERL'), JText::_('FTP-path') ); ?>
					</td>
                    <td>
                        <input type="text" name="ftppath" value="<?php echo $FTP_path; ?>" size="30" />
						<br/><?php echo JText::sprintf('FTP_BASE_PATH', JPATH_SITE) . '/'; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;<br /></td>
                </tr>
                <tr>
                <td valign="top"><strong><?php echo JText::_('Specify gallery');?></strong></td>
                    <td valign="top">
                        <input type="radio" name="selcat" value="1" CHECKED/>&nbsp;&nbsp;<?php echo JText::_('Yes_all_items_in');?>&nbsp;
                    </td>
                    <td valign="top">
                        <?php echo galleryUtils::galleriesSelectList( null, 'xcat', false );?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2"><input type="radio" name="selcat" value="0" />&nbsp;&nbsp;<?php echo JText::_('No, specify gallery per image in step 2'); ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;<br /></td>
                </tr>
                <tr class="row1">
                    <th colspan="3">
                        <div align="center" style="visibility: hidden;">
                        <input type="button" name="something" value="<?php echo JText::_('Next -->');?>" onClick="submitbutton('batchupload');" />
                        </div>
                        </th>
                </tr>
                </table>
            </td>
            <td width="300">&nbsp;</td>
        </tr>
        </table>
        <input type="hidden" name="uploaded" value="1" />
        <input type="hidden" name="option" value="com_rsgallery2" />
        <input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
        <input type="hidden" name="task" value="batchupload" />
        <input type="hidden" name="boxchecked" value="0" />
        </form>
        <?php
        }

	function batchupload_2( $ziplist, $extractDir ){
		/* Info for javascript on input element names and values:
		Step 2
		Button: Upload --> 	task=save_batchupload
		Delete checkbox name: 	delete[1]
		Item title field name:	ptitle[]
		Gallery select name:	category[]
		Description area name:	descr[]
		*/
        global $rsgOption;
        JHTML::_('behavior.mootools');
		
		$database = JFactory::getDBO();
        //Get variables from form
        $selcat 		= rsgInstance::getInt('selcat'  , null);
        $ftppath 		= rsgInstance::getVar('ftppath'  , null);
        $xcat 			= rsgInstance::getInt('xcat'  , null);
        $batchmethod 	= rsgInstance::getVar('batchmethod'  , null);
		
        ?>
		<script language="javascript" type="text/javascript">
        <!--
        function submitbutton(pressbutton) {
            var form = document.adminForm,
				missingCat = false,
				categories = $$('#adminForm input[name^=category]', '#adminForm select[name^=category]');
           
            for (i=0 ; i<categories.length ; i++) {
				if (categories[i].value <= 0) {
					alert("<?php echo JText::_('All images must be part of a galery');?>"+' (#'+i+')');
					return;
					missingCat = true;
					break;
				}
            }

			if (pressbutton == 'save_batchupload'){
				if (missingCat == true) {
					alert("<?php echo JText::_('All images must be part of a galery');?>");
				}
				else {
					form.submit();
				}
			}
        }
        //-->
        </script>

        <form action="index2.php" method="post" name="adminForm" id="adminForm">
        <table class="adminform">
        <tr>
            <th colspan="5" class="sectionname"><font size="4"><?php echo JText::_('Step 2');?></font></th>
        </tr>
        <tr>
        <?php
		
        // Initialize k (the column reference) to zero.
        $k = 0;
        $i = 0;

        foreach ($ziplist as $filename) {
        	$k++;
        	//Check if filename is dir
        	if ( is_dir(JPATH_ROOT . '/media/' . $extractDir . '/' . $filename) ) {
        		continue;
        	} else {
        		//Check if file is allowed
        		$allowed_ext = array('gif','jpg','png');
        		$allowedVideo_ext = array('flv','avi','mov');
        		$ext = fileHandler::getImageType( JPATH_ROOT . '/media/' . $extractDir . '/' . $filename );
				if ( in_array($ext, $allowedVideo_ext) ) {
        			// build preview image
					$basePath = JPATH_SITE . '/media/' . $extractDir . '/';
					require_once( JPATH_RSGALLERY2_ADMIN . 'includes/video.utils.php' );
					Ffmpeg::capturePreviewImage( $basePath . $filename, $basePath . $filename . '.png');
					$displayImage = $filename . '.png';
					$i++;
				}
				else{
					if ( !in_array($ext, $allowed_ext) ) {
        				continue;
        			} else {
						$displayImage = $filename;
        				$i++;
        			}
				}
        	}
            ?>
            <td align="center" valign="top" bgcolor="#CCCCCC">
                <table class="adminform" border="0" cellspacing="1" cellpadding="1">
                    <tr>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="2" align="right"><?php echo JText::_('Delete');?> #<?php echo $i - 1;?>: <input type="checkbox" name="delete[<?php echo $i - 1;?>]" value="true" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2"><img src="<?php echo JURI_SITE . "/media/" . $extractDir . "/" . $displayImage;?>" alt="" border="1" width="100" align="center" /></td>
                    </tr>
                    <input type="hidden" value="<?php echo $filename;?>" name="filename[]" />
                    <tr>
                        <td><?php echo JText::_('Title');?></td>
                        <td>
                            <input type="text" name="ptitle[]" size="15" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Gallery');?></td>
                        <td><?php
                            if ($selcat == 1 && $xcat !== '0')
                                {
                                ?>
                                <input type="text" name="cat_text" value="<?php echo htmlspecialchars(stripslashes(galleryUtils::getCatnameFromId($xcat)));?>" readonly />
                                <input type="hidden" name="category[]" value="<?php echo $xcat;?>" />
                                <?php
                                }
                            else
                                {
								echo galleryUtils::galleriesSelectList( null, 'category[]', false );
                                }
                                ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Description');?></td>
                        <td><textarea cols="15" rows="2" name="descr[]"></textarea></td>
                    </tr>
                </table>
            </td>
            <?php
            if ($k == 5)
                {
                echo "</tr><tr>";
                $k = 0;
                }
            }
            ?>
			</table>

			<input type="hidden" name="teller" value="<?php echo $i;?>" />
			<input type="hidden" name="extractdir" value="<?php echo $extractDir;?>" />
			<input type="hidden" name="option" value="com_rsgallery2" />
        	<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
			<input type="hidden" name="task" value="save_batchupload" />

			</form>
        <?php
	}
}
