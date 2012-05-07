<?php
/**
* Galleries option for RSGallery2 - HTML display code
* @version $Id: galleries.html.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Explain what this class does
 * @package RSGallery2
 */
class html_rsg2_galleries{
    /**
     * show list of galleries
     */
    function show( &$rows, &$lists, &$search, &$pageNav ){
        global $rsgOption;
		$option = JRequest::getCmd('option');
		
		$my =& JFactory::getUser();
		JHTML::_("behavior.mootools");
		
		//Create 'lookup array' to find whether or not galleries with the same parent
		// can move up/down in their order: $orderLookup[id parent][#] = id child
		$orderLookup = array();
		foreach ($rows as $row) {
			$orderLookup[$row->parent][] = $row->id;
		}  		
		
        ?>
        <form action="index.php" method="post" name="adminForm">
        <table border="0" width="100%">
        <tr>
            <td width="50%">
            &nbsp;
            </td>
            <td nowrap="true" width="50%" align="right">
            <?php echo JText::_('Max Levels')?>
            <?php echo $lists['levellist'];?>
            <?php echo JText::_('Filter')?>:
            <input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
            </td>
        </tr>
        </table>

        <table class="adminlist">
        <thead>
        <tr>
            <th width="1%">
            ID
            </th>
            <th width="1%">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
            </th>
            <th class="Name">
            <?php echo JText::_('Name')?>
            </th>
            <th width="5%">
            <?php echo JText::_('Published')?>
            </th>
            <th colspan="2" width="5%">
            <?php echo JText::_('Reorder')?>
            </th>
			<th width="2%">
				<?php echo JText::_('Order')?>
			</th>
			<th width="2%">
				<?php echo JHtml::_('grid.order',  $rows); ?>
			</th>
			<th width="4%"><?php echo JText::_('Items')?></th>
            <th width="25%">
            <?php echo JText::_('Hits')?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        $k = 0;
        for ($i=0, $n=count( $rows ); $i < $n; $i++) {
            $row = &$rows[$i];

            $link   = "index2.php?option=$option&rsgOption=$rsgOption&task=editA&hidemainmenu=1&id=". $row->id;

            $task   = $row->published ? 'unpublish' : 'publish';
            $img    = $row->published ? 'publish_g.png' : 'publish_x.png';
            $alt    = $row->published ? 'Published' : 'Unpublished';

            $checked    = JHTML::_('grid.checkedout', $row, $i );
			
			//Use the $orderLookup array to determine if for the same 
			// parent one can still move up/down. First look up the parent info.
			$orderkey = array_search($row->id, $orderLookup[$row->parent]);
			$showMoveUpIcon		= isset($orderLookup[$row->parent][$orderkey - 1]);
			$showMoveDownIcon	= isset($orderLookup[$row->parent][$orderkey + 1]);
			
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
                    echo stripslashes($row->name);
                } else {
                    ?>
                    <a href="<?php echo $link; ?>" name="Edit Gallery">
                    <?php echo stripslashes($row->treename); ?>
                    </a>
                    <?php
                }
                ?>
				<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&rsgOption=images&gallery_id='.$row->id); ?>" >
					<img src="images/forward_f2.png" width="12" height="12" style="margin: 0px 20px" alt="<?php echo JText::_('ITEMS'); ?>" />
				</a>				
                </td>
                <td align="center">
                <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                <img src="images/<?php echo $img;?>" width="12" height="12"  alt="<?php echo $alt; ?>" />
                </a>
                </td>
                <td>
					<?php echo $pageNav->orderUpIcon( $i, $showMoveUpIcon ); ?>
                </td>
                <td>
					<?php echo $pageNav->orderDownIcon( $i, $n , $showMoveDownIcon); ?>
                </td>
                <td colspan="2" align="center">
                <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
                </td>
                <td align="center">
                <?php $gallery = rsgGalleryManager::get( $row->id ); echo $gallery->itemCount()?>
                </td>
                <td align="left">
                <?php echo $row->hits; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
        	<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
        </tr>
        </tfoot>
        </table>
        
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    /**
     * warns user what will be deleted
     */
    function removeWarn( $galleries ){
        global $rsgOption, $option;
        ?>
        <form action="index2.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />

<!--         these are the galleries the user has chosen to delete: -->
        <?php foreach( $galleries as $g ): ?>
            <input type="hidden" name="cid[]" value="<?php echo $g->get('id'); ?>" />
        <?php endforeach; ?>
        
        <h2>The following will be deleted:</h2>
        <div style='text-align: left;' >

        <?php html_rsg2_galleries::printTree( $galleries ); ?>
        
        </div>
        </form>
        <?php
    }
    function printTree( $galleries ){
        echo "<ul>";

        foreach( $galleries as $g ){
            // print gallery details
            echo "<li>". $g->get('name') ." (". count($g->itemRows()) ." images)";
            html_rsg2_galleries::printTree( $g->kids() );
            echo "</li>";
        }
        echo "</ul>";
    }

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param rsgGallery The gallery object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function edit( &$row, &$lists, &$params, $option ) {
		global $rsgOption, $rsgAccess, $rsgConfig;

		jimport("joomla.filter.output");
		$my =& JFactory::getUser();
		$editor =& JFactory::getEditor();
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES );
	
		$task = rsgInstance::getVar( 'task'  , '');
		
		JHTML::_('behavior.formvalidation');
		JHTML::_("Behavior.mootools");
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
	
			// do field validation
			if (form.name.value == ""){
				alert("<?php echo JText::_('YOU MUST PROVIDE A GALLERY NAME.');?>");
			} else {
				<?php echo $editor->save('description') ; ?>
				submitform( pressbutton );
			}
		}
	
		function selectAll() {
			if(document.adminForm.checkbox0.checked) {
				for (i = 0; i < 12; i++) {
					document.getElementById('p' + i).checked=true;
				}
			} else {
				for (i = 0; i < 12; i++) {
					document.getElementById('p' + i).checked=false;
				}
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm" class="form-validate">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo JText::_('Gallery')?>:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
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
					<?php echo JText::_('Details')?>
					</th>
				</tr>
				<tr>
					<td width="20%" align="right">
					<?php echo JText::_('Name')?>
					</td>
					<td width="80%">
					<input class="text_area required" type="text" name="name" size="50" maxlength="250" value="<?php echo stripslashes($row->name);?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" align="right">
					<?php echo JText::_('COM_RSGALLERY2_ALIAS')?>
					</td>
					<td width="80%">
					<input class="text_area" type="text" name="alias" size="50" maxlength="250" value="<?php echo stripslashes($row->alias);?>" />
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo JText::_('Owner');?>
					</td>
					<td>
					<?php echo $lists['uid']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo JText::_('Description')?>
					</td>
					<td>
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					echo $editor->display ( 'description',  stripslashes($row->description) , '100%', '300', '10', '20' ,false) ; ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo JText::_('Parent Item');?>
					</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo JText::_('Gallery thumbnail');?>
					</td>
					<td>
					<?php echo imgUtils::showThumbNames($row->id, $row->thumb_id); ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo JText::_('Ordering');?>
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo JText::_('Published');?>
					</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1">
					<?php echo JText::_('Parameters');?>
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table><br/>
				<table class="adminform">
				<?php
				if ($rsgConfig->get('acl_enabled')) {
					?>
					<tr>
						<th colspan="1"><?php echo JText::_('Permissions')?></th>
					</tr>	                
					<?php
					if ( !isset($row->id) ) {
					?>
	
					<tr>
						<td><?php echo JText::_('_RSGALLERY_GAL_DEF_PERM_CREATE')?></td>
					</tr>
					<?php
					} else {
						$perms = $rsgAccess->returnPermissions($row->id);
	
						if ( !$perms ) {
							?>
							<tr>
								<td colspan="6"><?php echo JText::_('RSGALLERY_GAL_NO_PERM_FOUND')?></td>
							</tr>
							<?php	
						} else {
							?>
							<tr>
								<td>
								<table class="adminform" border="0" width="100%">
								<tr>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Usertype')?></span></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('View<br/>Gallery</span>')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Upload/Edit<br/>images</span>')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Delete Image</span>')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Modify<br/>Gallery</span>')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Delete<br/>Gallery</span>')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('View votes')?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo JText::_('Vote')?></td>
								</tr>
								<tr>
									<td><span style="font-weight:bold;"><?php echo JText::_('Public</span>')?></td>
									<td><input id="p0" type="checkbox" name="perm[0]" value="1" <?php if ($perms->public_view == 1) echo "CHECKED";?>></td>
									<td><input id="p1" type="checkbox" name="perm[1]" value="1" <?php if ($perms->public_up_mod_img == 1) echo "CHECKED";?>></td>
									<td><input id="p2" type="checkbox" name="perm[2]" value="1" <?php if ($perms->public_del_img == 1) echo "CHECKED";?>></td>
									<td><input id="p3" type="checkbox" name="perm[3]" value="1" <?php if ($perms->public_create_mod_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p4" type="checkbox" name="perm[4]" value="1" <?php if ($perms->public_del_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p5" type="checkbox" name="perm[5]" value="1" <?php if ($perms->public_vote_view == 1) echo "CHECKED";?>></td>
									<td><input id="p6" type="checkbox" name="perm[6]" value="1" <?php if ($perms->public_vote_vote == 1) echo "CHECKED";?>></td>
								</tr>
								<tr>
									<td><span style="font-weight:bold;"><?php echo JText::_('Registered</span>')?></td>
									<td><input id="p7" type="checkbox" name="perm[7]" value="1" <?php if ($perms->registered_view == 1) echo "CHECKED";?>></td>
									<td><input id="p8" type="checkbox" name="perm[8]" value="1" <?php if ($perms->registered_up_mod_img == 1) echo "CHECKED";?>></td>
									<td><input id="p9" type="checkbox" name="perm[9]" value="1" <?php if ($perms->registered_del_img == 1) echo "CHECKED";?>></td>
									<td><input id="p10" type="checkbox" name="perm[10]" value="1" <?php if ($perms->registered_create_mod_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p11" type="checkbox" name="perm[11]" value="1" <?php if ($perms->registered_del_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p12" type="checkbox" name="perm[12]" value="1" <?php if ($perms->registered_vote_view == 1) echo "CHECKED";?>></td>
									<td><input id="p13" type="checkbox" name="perm[13]" value="1" <?php if ($perms->registered_vote_vote == 1) echo "CHECKED";?>></td>
								</tr>
								<tr>
									<td colspan="6"><input type="checkbox" name="checkbox0" value="true" onClick='selectAll()'><?php echo ' '.JText::_('Select/Deselect All')?></td>
								</tr>
								</table>
								</td>
							</tr>
							<?php
						}
					}
				}
				?>
				</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}