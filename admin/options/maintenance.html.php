<?php
/**
* Maintenance option for RSGallery2 - HTML display code
* @version $Id: maintenance.html.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Handles HTML screens for image option 
 * @package RSGallery2
 */
class html_rsg2_maintenance {

	function showMaintenanceCP() {
		?>
		<div id="rsg2-thisform">
		<div id='cpanel'>
			<?php
			$link = 'index2.php?option=com_rsgallery2&amp;rsgOption=maintenance&amp;task=consolidateDB';
		html_rsg2_maintenance::quickiconBar( $link, 'blockdevice.png', JText::_('MAINT_CONSOLDB'), JText::_('MAINT_CONSOLDB_TXT') );

			$link = 'index2.php?option=com_rsgallery2&amp;rsgOption=maintenance&amp;task=regenerateThumbs';
		html_rsg2_maintenance::quickiconBar( $link, 'menu.png', JText::_('MAINT_REGEN'), JText::_('MAINT_REGEN_TXT') );

			$link = 'index2.php?option=com_rsgallery2&amp;rsgOption=maintenance&amp;task=optimizeDB';
		html_rsg2_maintenance::quickiconBar( $link, 'db_optimize.png', JText::_('MAINT_OPTDB'), JText::_('MAINT_OPTDB_TXT') );
		?>
		</div>
		<div class='rsg2-clr'>&nbsp;</div>
		</div>
		<?php
	}
	
	 /**
      * Used by showCP to generate buttons
      * @param string URL for button link
      * @param string Image name for button image
      * @param string Text to show in button
      */
	function quickiconBar( $link, $image, $title, $text = "" ) {
	    ?>
	    <div style="float:left;">
	    <div class="icon-bar">
	        <a href="<?php echo $link; ?>">
	            <div class="iconimage">
	                <div class="rsg2-icon"><img src="<?php echo JURI_SITE;?>/administrator/components/com_rsgallery2/images/<?php echo $image;?>" alt="alternate text" /></div>
					<div class="rsg2-text">
						<span class="maint-title"><?php echo $title;?></span>
						<span class="maint-text"><?php echo $text;?></span></div>
	            </div>
	        </a>
	    </div>
	    </div>
	    <div class='rsg2-clr'>&nbsp;</div>
	    <?php
	}
	
	function regenerateImages($lists) {
		global $rsgConfig;
		?>
		<script language="Javascript">
	        function submitbutton(pressbutton){
	            var form = document.adminForm;
	            
	            if (pressbutton != 'cancel'){
	                submitform( pressbutton );
	                return;
	            } else {
	                window.history.go(-1);
	                return;
	                
	            }
	        }
	    </script>
		<form name="adminForm" method="post" action="index2.php">
		<table width="500">
		<tr>
			<td>
			<table class="adminform">
				<tr>
					<td valign="top" width="300"><p><?php echo JText::_('SELECT_GALLERIES_TO_REGENERATE_THUMBNAILS_FROM')?></p></td>
					<td valign="top">
						<fieldset>
						<legend>Select galleries</legend>
						<?php echo $lists['gallery_dropdown'];?>
						</fieldset>
						<p>
							<span style="font-weight: bold;">New width:</span>&nbsp;
							<?php echo $rsgConfig->get('thumb_width');?>&nbsp;pixels<br />
						</p>
					</td>
					<td width="10">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr>
			</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="com_rsgallery2" />
		<input type="hidden" name="rsgOption" value="maintenance" />
	    <input type="hidden" name="task" value="" />
		</form>
		<?php
	}
	
	function consolidateDbInformX($option){
	    // inform user of purpose of this function, then provide a proceed button
		?>
	    <script language="Javascript">
	        function submitbutton(pressbutton){
	            if (pressbutton != 'cancel'){
	                submitform( pressbutton );
	                return;
	            } else {
	                window.history.go(-1);
	                return;
	            }
	        }
	    </script>
	    <form action="index2.php" method="post" name="adminForm">
	    <table class="adminform" cellpadding="4" cellspacing="0" border="0" width="98%" align="center">
	        <tr>
	            <td>&nbsp;</td>
	        </tr>
	        <tr>
		<td><?php echo JText::_('CONSOLIDATE_DB');?></td>
	        </tr>
	        <tr>
	            <td>
	                <div align="center">
	                <input type="button" name="consolidate_db_go" value="<?php echo JText::_('Proceed') ?>" class="button" onClick="submitbutton('consolidate_db_go');" />
	                <input type="button" name="cancel" value="<?php echo JText::_('Cancel') ?>" class="button" onClick="submitbutton('cancel');" />
	                </div>
	            </td>
	        </tr>
	    </table>
	    <input type="hidden" name="option" value="<?php echo $option;?>" />
	    <input type="hidden" name="task" value="" />
	    </form>
	<?php
	}
	
	function consolidateDB($db_name, $file_display, $file_original, $file_thumb, $files_total) {
	    global $rsgConfig;
	    require_once( JPATH_RSGALLERY2_ADMIN.'/config.rsgallery2.php' );
	    $file_diff = array_diff($files_total, $db_name);
	    ?>
	    <script language="Javascript">
	    function db_create() {
	    	alert('Database creation does not work yet!');
	    }
	    
	    function db_createX() {
	    	var form = document.adminForm;
				form.t_id.value = t_id;
				form.g_id.value = g_id;
				form.submit();
	    }
	    </script>
	    <form method="post" action="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=createDbEntries" name="adminForm">
	    <input type="hidden" name="t_id" value="" />
	    <input type="hidden" name="g_id" value="" />
	    <table width="100%" border="0">
	    	<tr>
	    	<td width="15%">&nbsp;</td>
	    	<td width="70%">
			    <table class="adminlist" border="0">
			    <tr>
			    	<td colspan="9" align="center">
				    	<div style="clear: both; margin: 3px; margin-top: 10px; padding: 5px 15px; display: block; float: left; border: 1px solid #cc0000; background: #ffffcc; text-align: left; width: 80%;">
		    				<p style="color: #CC0000;">
		    				<img src="<?php echo JURI_SITE;?>/includes/js/ThemeOffice/warning.png" alt="Warning icon" />
							NOTICE:<br />Experimental at this stage. Single image regeneration works. <br /> Database entries do NOT work!.
							<?php //echo JText::_('CONSDB_NOTICE');?>
							</p>
						</div>
						<div class='rsg2-clr'>&nbsp;</div>
			    	</td>
			    </tr>
			    <tr>
			    	<th>#</th>
			        <th><?php echo JText::_('Filename');?></th>
			        <th align="center"><?php echo JText::_('In<br>database');?></th>
			        <th align="center"><?php echo JText::_('Display<br>folder');?></th>
		        	<th align="center"><?php echo JText::_('Original<br>folder');?></th>
			        <th align="center"><?php echo JText::_('Thumb<br>folder');?></th>
			        <th>&nbsp;</th>
			        <th align="center"><?php echo JText::_('Image');?></th>
			        <th align="center"><?php echo JText::_('Action');?></th>
			    </tr>
			    <tr>
			        <td colspan="9">&nbsp;</td>
			    </tr>
			    <?php
			    $yes    = "<td align=\"center\"><img src=\"".JURI_SITE."administrator/images/tick.png\" alt=\"".JText::_('Image in folder')."\" border=\"0\"></td>";
				$no     = "<td align=\"center\"><img src=\"".JURI_SITE."administrator/images/publish_x.png\" alt=\"".JText::_('Image NOT in folder')."\" border=\"0\"></td>";
			    $z = 0;
			    $c = 0;
			    //Check database and crossreference against filesystem
			    foreach ($db_name as $name)
			        {
			        $c++;
			        $i = 0;
			        $fid = galleryUtils::getFileIdFromName($name);
			        $html = "<tr><td><input type=\"checkbox\" id=\"cb$c\" name=\"xid[]\" value=\"$name\" onclick=\"isChecked(this.checked);\" /></td><td>$name</td>".$yes;
			        if (in_array($name, $file_display )) {
			            $i++;
			            $html .= $yes;
			            $display = true;
			        } else {
			            $z++;
			            $html .= $no;
			            $display = false;
					}
			        if (in_array($name, $file_original )) {
			            $i++;
			            $html .= $yes;
			            $original = true; 
			        } else {
			            $z++;
			            $html .= $no;
			            $original = false;
					}
			        if (in_array($name, $file_thumb )) {
			            $i++;
			            $html .= $yes;
			            $thumb = true;
					} else {
			            $z++;
			            $html .= $no;
			            $thumb = false;
					}
					
			        if ($i < 3) {
			            echo $html;
			            ?>
			            <td>&nbsp;</td>
			            <td>
			            	<img src="<?php echo imgUtils::getImgThumb( $name );?>" name="image" width="<?php echo $rsgConfig->get('thumb_width')?>" alt="<?php echo $name;?>"/>
			            </td>
			            <td align="center">
			                <a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=deleteImages&name=<?php echo $name;?>"><?php echo '[&nbsp;'.JText::_('Delete_from_database').'&nbsp;]'?></a><br />
			                <?php
			                if ($original == true OR $display == true) {
			                    ?>
			                    <a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=createImages&id=<?php echo $fid;?>"><?php echo '[&nbsp;'.JText::_('Create_missing_images').'&nbsp;]'?></a>
			                    <?php
			                    }
			                    ?>
			            </td></tr>
			            <?php
			        } else {
			            continue;
					}
				}
			    ?>
			    </tr>
			    
			    <?php
			    $zz = 0;
			    $t = 0;
			    //Check filesystem and crossreference against database
			    foreach ($file_diff as $diff) {
			        $t++;
			        $y = 0;
			        
			        $html2 = "<tr><td><input type=\"checkbox\" id=\"cb$t\" name=\"xid[]\" value=\"$t\" onclick=\"isChecked(this.checked);\" /></td><td><font color=\"#FF0000\">$diff</font></td>$no";
			        if (in_array($diff, $file_display ))
			            {
			            $y++;
			            $html2 .= $yes;
			            $display2 = true;
			            }
			        else
			            {
			            $zz++;
			            $html2 .= $no;
			            $display2 = false;
			            }
			        if (in_array($diff, $file_original ))
			            {
			            $y++;
			            $html2 .= $yes;
			            $original2 = true;
			            }
			        else
			            {
			            $zz++;
			            $html2 .= $no;
			            $original2 = false;
			            }
			        if (in_array($diff, $file_thumb ))
			            {
			            $y++;
			            $html2 .= $yes;
			            $thumb2 = true;
			            }
			        else
			            {
			            $zz++;
			            $html2 .= $no;
			            $thumb2 = false;
			            }
			        if ($y < 4)
			            {
			            echo $html2;
			            ?>
			            <td>
			            	<?php echo galleryUtils::galleriesSelectList(NULL,'gallery_id[]', false, false);?>
			            	<input type="hidden" name="name[]" value="<?php echo $diff;?>" />
			            </td>
			            <td>
			            	<img src="<?php echo imgUtils::getImgThumb( $diff );?>" name="image" width="<?php echo $rsgConfig->get('thumb_width')?>" />
			            </td>
			            <td align="center">
			                <a href="javascript:void();" onClick="javascript:db_create();"><?php echo '[&nbsp;'.JText::_('Create_Database_entry').'&nbsp;]';?></a><br />
			                <a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=deleteImages&name=<?php echo $diff;?>"><?php echo '[&nbsp;'.JText::_('Delete_images'),'&nbsp;]'?></a>&nbsp;
			                <?php
			                if ($original2 == true AND $display2 == true AND $thumb2 == true)
			                    {
			                    continue;
			                    }
			                else
			                    {
			                    ?>
			                    <br /><a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=createImages&name=<?php echo $diff;?>"><?php echo '[&nbsp;'.JText::_('Create missing images').'&nbsp;]';?></a>
			                    <?php
			                    }
			                    ?>
			            </td>
			            <?php
			            }
			        else
			            {
			            continue;
			            }
			        }
			        if ($t == 0 AND $z == 0)
			        	echo "<tr><td colspan=\"8\"><font color=\"#008000\"><strong>".JText::_('No inconsistencies in database')."</strong></font></td>";
			    ?>
			    </tr>
			    <tr>
			        <th colspan="9" align="center">
			        	<a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=consolidateDB">Refresh</a>
			        </th>
			    </tr>
			    <!--
			    <tr>
			    	<td colspan="2"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo (count( $db_name ) + count( $file_diff )); ?>);" /></td>
			    	<td colspan="5"> With selection:<br /> 
			    		<a href="javascript:void();" onClick="javascript:alert('<?php echo JText::_('Not working yet') ?>');"><?php echo JText::_('Delete from filesystem') ?></a>&nbsp;|&nbsp; 
			    		<a href="javascript:void();" onClick="javascript:alert('<?php echo JText::_('Not working yet') ?>');"><?php echo JText::_('Create missing images')?></a>&nbsp;|&nbsp;
			    		<a href="javascript:void();" onClick="javascript:alert('<?php echo JText::_('Not working yet') ?>');"><?php echo JText::_('Create database entries')?></a>
			    	</td>
			
			    </tr>
			    -->
			    </table>
	    </td>
	    <td width="15%">&nbsp;</td>
	    </tr>
	    </table>
	    </form>
	    <?php
	}
}
?>