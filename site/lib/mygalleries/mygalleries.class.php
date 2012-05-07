<?php
/**
* This file contains myGalleries class
* @version $Id: mygalleries.class.php 1070 2012-03-25 11:40:51Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2012 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class myGalleries {

   	function myGalleries() {

   	}
   	
    /**
     * This presents the main My Galleries page
     * @param array Result array with category details for logged in users
     * @param array Result array with image details for logged in users
     * @param array Result array with pagenav information
     */
    function viewMyGalleriesPage($rows, $images, $pageNav) {
        global $rsgConfig,$mainframe;
		$my = JFactory::getUser();
		$database = JFactory::getDBO();

        if (!$rsgConfig->get('show_mygalleries'))
            $mainframe->redirect( $this->myg_url,JText::_('User galleries was disabled by the administrator.'));
        ?>
		<div class="rsg2">
        <h2><?php echo JText::_('My galleries');?></h2>

        <?php
        //Show User information
        myGalleries::RSGalleryUSerInfo($my->id);
        
        //Start tabs
		jimport("joomla.html.pane");
        $tabs =& JPane::getInstance("Tabs");
        echo $tabs->startPane( 'tabs' );
        echo $tabs->startPanel( JText::_('My Images'), 'my_images' );
            myGalleries::showMyImages($images, $pageNav);
            myGalleries::showImageUpload();
        echo $tabs->endPanel();
        if ($rsgConfig->get('uu_createCat')) {
            echo $tabs->startPanel( JText::_('My galleries'), 'my_galleries' );
                myGalleries::showMyGalleries($rows);
                myGalleries::showCreateGallery(NULL);
            echo $tabs->endPanel();
        }
        echo $tabs->endPane();
        ?>
		</div>
        <div class='rsg2-clr'>&nbsp;</div>
        <?php
	}
	
	function showCreateGallery($rows) {
    	global $rsgConfig;
		$my = JFactory::getUser();
		$editor =& JFactory::getEditor();

    	//Load frontend toolbar class
    	require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
	    ?>
	    <script type="text/javascript">
	        function submitbutton(pressbutton) {
	            var form = document.form1;
	            if (pressbutton == 'cancel') {
	                form.reset();
	                return;
	            }
	        
				<?php echo $editor->save('description') ; ?>

				// do field validation
				if (form.parent.value == "-1") {
					alert( "<?php echo JText::_('You need to select a parent gallery'); ?>" );
				} else if (form.catname1.value == "") {
					alert( "<?php echo JText::_('You must provide a gallery name.'); ?>" );
				}
				else if (form.description.value == ""){
					alert( "<?php echo JText::_('You must provide a description.'); ?>" );
				}
				else{
					form.submit();
				}
			}
	    </script>
	    <?php
	    if ($rows) {
	        foreach ($rows as $row){
	            $catname        = $row->name;
	            $description    = $row->description;
	            $ordering       = $row->ordering;
	            $uid            = $row->uid;
	            $catid          = $row->id;
	            $published      = $row->published;
	            $user           = $row->user;
	            $parent         = $row->parent;
	        }
	    }
	    else{
	        $catname        = "";
	        $description    = "";
	        $ordering       = "";
	        $uid            = "";
	        $catid          = "";
	        $published      = "";
	        $user           = "";
	        $parent         = 0;
	    }
	    ?>
        <form name="form1" id="form1" method="post" action="<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=saveCat"); ?>">
        <table width="100%">
        <tr>
            <td colspan="2"><h3><?php echo JText::_('Create Gallery'); ?></h3></td>
        </tr>
        <tr>

            <td align="right">
                <div style="float: right;">
                        <?php
                        // Toolbar
                        mosToolBar::startTable();
                        mosToolBar::save();
                        mosToolBar::cancel();
                        mosToolBar::endtable();
                        ?>
                </div>
            </td>

        </tr>
        </table>
        <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
        <input type="hidden" name="ordering" value="<?php echo $ordering; ?>" />
        <table class="adminlist" border="1">
        <tr>
            <th colspan="2"><?php echo JText::_('Create Gallery'); ?></th>
        </tr>
        <tr>
            <td><?php echo JText::_('Top gallery');?></td>
            <td>
				<?php
                if (!$rsgConfig->get('acl_enabled')) {
                    galleryUtils::showCategories(NULL, $my->id, 'parent');
                } else {
                    galleryUtils::showUserGalSelectList('up_mod_img', 'parent');
                }

				?>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('Gallery name'); ?></td>
            <td align="left"><input type="text" name="catname1" size="30" value="<?php echo $catname; ?>" /></td>
        </tr>
        <tr>
            <td><?php echo JText::_('Description'); ?></td>
            <td align="left">
				<?php echo $editor->display( 'description',  $description , '100%', '200', '10', '20' ,false) ; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('Published'); ?></td>
            <td align="left"><input type="checkbox" name="published" value="1" <?php if ($published==1) echo "checked"; ?> /></td>
        </tr>
        </table>
        </form>
        <?php
	}
	
	/**
     * Displays details about the logged in user and the privileges he/she has
     * $param integer User ID from Joomla user table
     */
     function RSGalleryUserInfo($id) {
	     global $rsgConfig;
				$my = JFactory::getUser();

	     if ($my->usertype == "Super Administrator" OR $my->usertype == "Administrator") {
	        $maxcat = JText::_('COM_RSGALLERY2_UNLIMITED');
	        $max_images = JText::_('COM_RSGALLERY2_UNLIMITED');
	     } else {
	        $maxcat = $rsgConfig->get('uu_maxCat');
	        $max_images = $rsgConfig->get('uu_maxImages');
	     }
	     ?>
	     <table class="adminform" border="1">
	     <tr>
	        <th colspan="2"><?php echo JText::_('User information'); ?></th>
	     </tr>
	     <tr>
	        <td width="250"><?php echo JText::_('Username'); ?></td>
	        <td><?php echo $my->username;?></td>
	     </tr>
	     <tr>
	        <td><?php echo JText::_('User level'); ?></td>
	        <td><?php echo $my->usertype;?></td>
	     </tr>
	     <tr>
	        <td><?php echo JText::_('Maximum usergalleries'); ?></td>
	        <td><?php echo $maxcat;?>&nbsp;&nbsp;(<font color="#008000"><strong><?php echo galleryUtils::userCategoryTotal($my->id);?></strong></font> <?php echo JText::_('created)');?></td>
	     </tr>
	     <tr>
	        <td><?php echo JText::_('Maximum images allowed'); ?></td>
	        <td><?php echo $max_images;?>&nbsp;&nbsp;(<font color="#008000"><strong><?php echo galleryUtils::userImageTotal($my->id);?></strong></font> <?php echo JText::_('uploaded)'); ?></td>
	     </tr>
	     <tr>
	        <th colspan="2"></th>
	     </tr>
	     </table>
	     <br><br>
	     <?php
	}
	
	function showImageUpload() {
        global $rsgConfig;
		$my = JFactory::getUser();
		$editor = JFactory::getEditor();
        
        //Load frontend toolbar class
        require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
        ?>
        <script  type="text/javascript">
        function submitbuttonImage(pressbutton) {
            var form = document.uploadForm;
            if (pressbutton == 'cancel') {
                form.reset();
                return;
            }
				<?php echo $editor->save('descr') ; ?>

            // do field validation
            if (form.i_cat.value == "-1") {
                alert( "<?php echo JText::_('You must select a gallery.'); ?>" );
            } else if (form.i_cat.value == "0") {
                alert( "<?php echo JText::_('You must select a gallery.'); ?>" );
            } else if (form.i_file.value == "") {
                alert( "<?php echo JText::_('You must provide a file to upload.'); ?>" );
            } else {
				form.submit();
            }
        }
        
    </script>
        <form name="uploadForm" id="uploadForm" method="post" action="
<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=saveUploadedItem"); ?>" enctype="multipart/form-data">
		<div class="rsg2">
        <table border="0" width="100%">
            <tr>
                <td colspan="2"><h3>
<?php echo JText::_('Add Image');?></h3></td>
            </tr>
            <tr>

                <td align="right">
                    <div style="float: right;">
                    <table cellpadding="0" cellspacing="3" border="0" id="toolbar">
                    <tr height="60" valign="middle" align="center">
                        <td>
                            <a class="toolbar" href="javascript:submitbuttonImage('save');" >
                            <img src="<?php echo JURI::root();?>/images/save_f2.png"  alt="Save" name="save" title="Save" align="middle" /></a>
                        </td>
                        <td>
                            <a class="toolbar" href="javascript:submitbuttonImage('cancel');" >
                            <img src="<?php echo JURI::root();?>/images/cancel_f2.png"  alt="Cancel" name="cancel" title="Cancel" align="middle" /></a>
                        </td>
                    </tr>
                    </table>
                    </div>
                </td>

            </tr>
            <tr>
                <td>
                    <table class="adminlist" border="1">
                    <tr>
                        <th colspan="2"><?php echo JText::_('User Upload'); ?></th>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Gallery'); ?></td>
                        <td>
                            <?php 
                            /*echo galleryUtils::galleriesSelectList(null, 'i_cat', false);*/
                            
                            if (!$rsgConfig->get('acl_enabled')) {
                                galleryUtils::showCategories(NULL, $my->id, 'i_cat');
                            } else {
                                galleryUtils::showUserGalSelectList('up_mod_img', 'i_cat');
                            }
                            
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Filename') ?></td>
                        <td align="left"><input size="49" type="file" name="i_file" /></td>
                    </tr>
                    </tr>
                        <td><?php echo JText::_('Title') ?>:</td>
                        <td align="left"><input name="title" type="text" size="49" />
                    </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Description') ?></td>
                        <td align="left">
							<?php echo $editor->display( 'descr',  '' , '100%', '200', '10', '20' ,false) ; ?>
						</td>
                    </tr>
                    <?php
                    if ($rsgConfig->get('graphicsLib') == '')
                        { ?>
                    <tr>
                        <td><?php echo JText::_('Thumb:'); ?></td>
                        <td align="left"><input type="file" name="i_thumb" /></td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="cat" value="9999" />
                            <input type="hidden" name="uploader" value="<?php echo $my->id; ?>">
                        </td>
                    <tr>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
		</div>
        <?php
        }

    /**
     * Shows thumbnails for gallery and links to subgalleries if they exist.
     * @param integer Category ID
     * @param integer Columns per page
     * @param integer Number of thumbs per page
     * @param integer pagenav stuff
     * @param integer pagenav stuff
     */
    function RSShowPictures ($catid, $limit, $limitstart){
        global $rsgConfig;
		$my = JFactory::getUser();
		$database = JFactory::getDBO();

        $columns                    = $rsgConfig->get("display_thumbs_colsPerPage");
        $PageSize                   = $rsgConfig->get("display_thumbs_maxPerPage");
        //$my_id                      = $my->id;
    
        $database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE gallery_id='$catid'");
        $numPics = $database->loadResult();
        
        if(!isset($limitstart))
            $limitstart = 0;
        //instantiate page navigation
        $pagenav = new JPagination($numPics, $limitstart, $PageSize);
    
        $picsThisPage = min($PageSize, $numPics - $limitstart);
    
        if (!$picsThisPage == 0)
                $columns = min($picsThisPage, $columns);
                
        //Add a hit to the database
        if ($catid && !$limitstart)
            {
            galleryUtils::addCatHit($catid);
            }
        //Old rights management. If user is owner or user is Super Administrator, you can edit this gallery
        if(( $my->id <> 0 ) and (( galleryUtils::getUID( $catid ) == $my->id ) OR ( $my->usertype == 'Super Administrator' )))
            $allowEdit = true;
        else
            $allowEdit = false;

        $thumbNumber = 0;
        ?>
        <div class="rsg2-pageNav">
                <?php
                /*
                if( $numPics > $PageSize ){
                    echo $pagenav->writePagesLinks("index.php?option=com_rsgallery2&catid=".$catid);
                }
                */
                ?>
        </div>
        <br />
        <?php
        if ($picsThisPage) {
        $database->setQuery("SELECT * FROM #__rsgallery2_files".
                                " WHERE gallery_id='$catid'".
                                " ORDER BY ordering ASC".
                                " LIMIT $limitstart, $PageSize");
        $rows = $database->loadObjectList();
        
        switch( $rsgConfig->get( 'display_thumbs_style' )):
            case 'float':
                $floatDirection = $rsgConfig->get( 'display_thumbs_floatDirection' );
                ?>
                <ul id="rsg2-thumbsList">
                <?php foreach( $rows as $row ): ?>
                <li <?php echo "style='float: $floatDirection'"; ?> >
                    <a href="<?php  echo JRoute::_( "index.php?option=com_rsgallery2&page=inline&id=".$row->id."&catid=".$row->gallery_id."&limitstart=".$limitstart++ ); ?>">
                        <!--<div class="img-shadow">-->
                        <img border="1" alt="<?php echo htmlspecialchars(stripslashes($row->descr), ENT_QUOTES); ?>" src="<?php echo imgUtils::getImgThumb($row->name); ?>" />
                        <!--</div>-->
                        <span class="rsg2-clr"></span>
                        <?php if($rsgConfig->get("display_thumbs_showImgName")): ?>
                            <br /><span class='rsg2_thumb_name'><?php echo htmlspecialchars(stripslashes($row->title), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if( $allowEdit ): ?>
                    <div id='rsg2-adminButtons'>
                        <a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&page=edit_image&id=".$row->id); ?>"><img src="<?php echo JURI_SITE; ?>/administrator/images/edit_f2.png" alt=""  height="15" /></a>
                        <a href="#" onClick="if(window.confirm('<?php echo JText::_('Are you sure you want to delete this image?');?>')) location='<?php echo JRoute::_("index.php?option=com_rsgallery2&page=delete_image&id=".$row->id); ?>'"><img src="<?php echo JURI_SITE; ?>/administrator/images/delete_f2.png" alt=""  height="15" /></a>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
                </ul>
                <div class='rsg2-clr'>&nbsp;</div>
                <?php
                break;
            case 'table':
                $cols = $rsgConfig->get( 'display_thumbs_colsPerPage' );
                $i = 0;
                ?>
                <table id='rsg2-thumbsList'>
                <?php foreach( $rows as $row ): ?>
                    <?php if( $i % $cols== 0) echo "<tr>\n"; ?>
                        <td>
                            <!--<div class="img-shadow">-->
                                <a href="<?php echo JRoute::_( "index.php?option=com_rsgallery2&page=inline&id=".$row->id."&catid=".$row->gallery_id."&limitstart=".$limitstart++ ); ?>">
                                <img border="1" alt="<?php echo htmlspecialchars(stripslashes($row->descr), ENT_QUOTES); ?>" src="<?php echo imgUtils::getImgThumb($row->name); ?>" />
                                </a>
                            <!--</div>-->
                            <div class="rsg2-clr"></div>
                            <?php if($rsgConfig->get("display_thumbs_showImgName")): ?>
                            <br />
                            <span class='rsg2_thumb_name'>
                                <?php echo htmlspecialchars(stripslashes($row->title), ENT_QUOTES); ?>
                            </span>
                            <?php endif; ?>
                            <?php if( $allowEdit ): ?>
                            <div id='rsg2-adminButtons'>
                                <a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&page=edit_image&id=".$row->id); ?>"><img src="<?php echo JURI_SITE; ?>/administrator/images/edit_f2.png" alt=""  height="15" /></a>
                                <a href="#" onClick="if(window.confirm('<?php echo JText::_('Are you sure you want to delete this image?');?>')) location='<?php echo JRoute::_("index.php?option=com_rsgallery2&page=delete_image&id=".$row->id); ?>'"><img src="<?php echo JURI_SITE; ?>/administrator/images/delete_f2.png" alt=""  height="15" /></a>
                            </div>
                            <?php endif; ?>
                        </td>
                    <?php if( ++$i % $cols == 0) echo "</tr>\n"; ?>
                <?php endforeach; ?>
                <?php if( $i % $cols != 0) echo "</tr>\n"; ?>
                </table>
                <?php
                break;
            case 'magic':
                echo JText::_('Magic not implemented yet');
                ?>
                <table id='rsg2-thumbsList'>
                <tr>
                    <td><?php echo JText::_('Magic not implemented yet')?></td>
                </tr>
                </table>
                <?php
                break;
            endswitch;
            ?>
            <div class="rsg2-pageNav">
                    <?php
                    if( $numPics > $PageSize ){
                        echo $pagenav->writePagesLinks("index.php?option=com_rsgallery2&catid=".$catid);
                        echo "<br /><br />".$pagenav->writePagesCounter();
                    }
                    ?>
            </div>
            <?php
            }
        else {
            if (!$catid == 0)echo JText::_('No images in gallery');
        }
    }
    
    /**
     * This presents the list of galleries shown in My galleries
     * @param array $rows All galleries (no longer only for logged in users)
     */   
    function showMyGalleries($rows) {
	$my = JFactory::getUser();
	$database = JFactory::getDBO();
    //Set variables
    $count = count($rows);
    ?>
	<div class="rsg2">
    <table class="adminform" width="100%" border="1">
            <tr>
                <td colspan="4"><h3><?php echo JText::_('My galleries');?></h3></td>
            </tr>
            <tr>
                <th><div align="center"><?php echo JText::_('Gallery'); ?></div></th>
                <th width="75"><div align="center"><?php echo JText::_('Published'); ?></div></th>
                <th width="75"><div align="center"><?php echo JText::_('Delete'); ?></div></th>
                <th width="75"><div align="center"><?php echo JText::_('Edit'); ?></div></th>
            </tr>
            <?php
            if ($count == 0) {
                ?>
                <tr><td colspan="5"><?php echo JText::_('No User Galleries created'); ?></td></tr>
                <?php
            } else {
                //echo "This is the overview screen";
                foreach ($rows as $row) {
                    ?>
                    <script type="text/javascript">
						//<![CDATA[
						function deletePres(catid) {
							var yesno = confirm ("<?php echo JText::_('DELCAT_TEXT');?>");
							if (yesno == true) {
								location = "<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=deleteCat", false);?>"+"&gid="+catid;
							}
						}
						//]]>
                    </script>
                    <tr>
                        <td>
							<?php
							$indent = "";
							for ($i = 0; $i < $row->level; $i++) {
								$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
								if ($i == $row->level -1) $indent .= "<sup>|_</sup>";
							}
							echo $indent;
							?>
                        	<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&rsgOption=myGalleries&task=editCat&gid='.$row->id);?>">
                        		<?php echo stripslashes($row->name);?>
                        	</a>
                        </td>
                        <?php
                        if ($row->published == 1)
                            $img = "publish_g.png";
                        else
                            $img = "publish_r.png";?>
                            
                        <td><div align="center"><img src="<?php echo JURI_SITE;?>/administrator/images/<?php echo $img;?>" alt="" width="12" height="12" ></div></td>
                        <td>
                        	<a href="javascript:deletePres(<?php echo $row->id;?>);">
                        		<div align="center">
                        			<img src="<?php echo JURI_SITE;?>/administrator/images/publish_x.png" alt="" width="12" height="12" >
                        		</div>
                        	</a>
                        </td>
                        <td>
                        	<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&rsgOption=myGalleries&task=editCat&gid='.$row->id);?>">
                        		<div align="center">
                        			<img src="<?php echo JURI_SITE;?>/administrator/images/edit_f2.png" alt="" width="18" height="18" >
                        		</div>
                        	</a>
                        </td>
                    </tr>
                    <?php
				}	//foreach ($rows as $row) - end
			}	//If count not 0 - end
                    ?>
	</table>
	</div>
	<p></p>
    <?php
    }
    /**
     * This will show the images, available to the logged in users in the My Galleries screen
     * under the tab "My Images".
     * @param array Result array with image details for the logged in users
     * @param array Result array with pagenav details
     */
    function showMyImages($images, $pageNav) {
        global $rsgAccess;
        JHTML::_('behavior.tooltip');
        ?>
        <table width="100%" class="adminlist" border="1">
        <tr>
            <td colspan="4"><h3><?php echo JText::_('My Images'); ?></h3></td>
        </tr>
        <tr>
            <th colspan="4"><div align="right"><?php  echo $pageNav->getLimitBox(); ?></div></th>
        </tr>
        <tr>
            <th><?php echo JText::_('Name'); ?></th>
            <th><?php echo JText::_('Gallery'); ?></th>
            <th width="75"><?php echo JText::_('Delete'); ?></th>
            <th width="75"><?php echo JText::_('Edit'); ?></th>
        </tr>
        
        <?php
        if (count($images) > 0) {
             ?>
            <script type="text/javascript">
					//<![CDATA[
				function deleteImage(id)
				{
					var yesno = confirm ('<?php echo JText::_('Are you sure you want to delete this image?');?>');
					if (yesno == true) {
						location = 'index.php?option=com_rsgallery2&rsgOption=myGalleries&task=deleteItem&id='+id;
					}
				}
				//]]>
            </script>
            <?php
            foreach ($images as $image)
                {
                global $rsgConfig;
               ?>
                <tr>
                    <td>
                        <?php 
                        if (!$rsgAccess->checkGallery('up_mod_img', $image->gallery_id)) {
                            echo $image->name;
                        } else {
						//tooltip: tip, tiptitle, tipimage, tiptext, url, depreciated bool=1
						 echo JHTML::tooltip('<img src="'.JURI::root().$rsgConfig->get('imgPath_thumb').'/'.$image->name.'.jpg" alt="'.$image->name.'" />',
						 $image->name,
						 "",
						 htmlspecialchars($image->title,ENT_QUOTES,'UTF-8').'&nbsp;('.$image->name.')',	//turns into javascript safe so ENT_QUOTES needed with htmlspeciahlchars
					"index.php?option=com_rsgallery2&rsgOption=myGalleries&task=editItem&id=".$image->id,1);
						}
                        ?>
                    </td>
                    <td><?php echo galleryUtils::getCatnameFromId($image->gallery_id)?></td>
                    <td>
                        <?php
                        if (!$rsgAccess->checkGallery('del_img', $image->gallery_id)) {
                            ?>
                            <div align="center">
                                <img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/no_delete.png" alt="" width="12" height="12" >
                            </div>
                            <?php
                        } else {
                        ?>
                        <a href="javascript:deleteImage(<?php echo $image->id;?>);">
                            <div align="center">
                                <img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/delete.png" alt="" width="12" height="12" >
                            </div>
                        </a>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ( !$rsgAccess->checkGallery('up_mod_img', $image->gallery_id) ) {
                            ?>
                            <div align="center">
                                <img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/no_edit.png" alt="" width="15" height="15" >
                            </div>
                            <?php
                        } else {
                        ?>
                        <a href="<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=editItem&id=$image->id");?>">
                        <div align="center">
                            <img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/edit.png" alt="" width="15" height="15" >
                        </div>
                        </a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                }
            }
        else
            {
            ?>
            <tr><td colspan="4"><?php echo JText::_('No images in user galleries'); ?></td></tr>
            <?php
            }
            ?>
            <tr>
                <th colspan="4">
                	<div align="center">
                		<?php 
                			echo $pageNav->getPagesLinks();
                			echo "<br>".$pageNav->getPagesCounter();
                		?>
                	</div>
                </th>
            </tr>
            </table>
            <?php
    }
    
    function editItem($rows) {
        global $rsgConfig;
		$my = JFactory::getUser();
		$editor = JFactory::getEditor();
        require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
        foreach ($rows as $row) {
            $filename       = $row->name;
            $title          = htmlspecialchars($row->title, ENT_QUOTES);
            //$description    = $row->descr;
            $description    = htmlspecialchars($row->descr, ENT_QUOTES);
            $id             = $row->id;
            $limitstart     = $row->ordering - 1;
            $catid          = $row->gallery_id;
        }
		?>
    <script type="text/javascript">
        function submitbutton(pressbutton) {
            var form = document.form1;
            if (pressbutton == 'cancel') {
                form.reset();
                history.back();
                return;
            }

			<?php echo $editor->save('descr') ; ?>

			// do field validation
			if (form.catid.value == "0") {
				alert( "<?php echo JText::_('You must provide a gallery name.'); ?>" );
			}
			else if (form.descr.value == ""){
				alert( "<?php echo JText::_('You must provide a description.'); ?>" );
			}
			else{
				submitform( pressbutton );
			}
        }
    </script>
    <?php
        echo "<h3>".JText::_('Edit image')."</h3>";
        ?>
        <form name="form1" method="post" action="<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=saveItem"); ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <table width="100%">
            <tr>
                <td align="right">
                    <img onClick="form1.submit();" src="<?php echo JURI_SITE; ?>/administrator/images/save.png" alt="<?php echo JText::_('Upload') ?>"  name="upload" onMouseOver="document.upload.src='<?php echo JURI_SITE; ?>/administrator/images/save_f2.png';" onMouseOut="document.upload.src='<?php echo JURI_SITE; ?>/administrator/images/save.png';" />&nbsp;&nbsp;
                    <img onClick="history.back();" src="<?php echo JURI_SITE; ?>/administrator/images/cancel.png" alt="<?php echo JText::_('Cancel'); ?>"  name="cancel" onMouseOver="document.cancel.src='<?php echo JURI_SITE; ?>/administrator/images/cancel_f2.png';" onMouseOut="document.cancel.src='<?php echo JURI_SITE; ?>/administrator/images/cancel.png';" />
                </td>
            </tr>
        </table>
        <table class="adminlist" border="2" width="100%">
            <tr>
                <th colspan="3"><?php echo JText::_('Edit image'); ?></th>
            </tr>
            <tr>
                <td align="left"><?php echo JText::_('Category name'); ?></td>
                <td align="left">
                    <?php galleryUtils::showUserGalSelectList('up_mod_img', 'catid', $catid);?>
                </td>
                <td rowspan="2"><img src="<?php echo imgUtils::getImgThumb($filename); ?>" alt="<?php echo $title; ?>"  /></td>
            </tr>
            <tr>
                <td align="left"><?php echo JText::_('Filename'); ?></td>
                <td align="left"><strong><?php echo $filename; ?></strong></td>
            </tr>
            <tr>
                <td align="left"><?php echo JText::_('Title');?></td>
                <td align="left"><input type="text" name="title" size="30" value="<?php echo $title; ?>" /></td>
            </tr>
            <tr>
                <td align="left" valign="top"><?PHP echo JText::_('Description'); ?></td>
                <td align="left" colspan="2">
				<?php echo $editor->display( 'descr', stripslashes($description) , '100%', '200', '10', '20',false ) ; ?>
                </td>
            </tr>
            <tr>
                <th colspan="3">&nbsp;</th>
            </tr>
        </table>
        </form>
        <?php
    }
    
function editCat($rows = null) {
	//Mirjam: In v1.13 catid was used where since v1.14 gid is used, but locally in a function catid is fine
    global $rsgConfig;
	$my = JFactory::getUser();
	$editor =& JFactory::getEditor();

    //Load frontend toolbar class
    require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
    ?>
    <script type="text/javascript">
        function submitbutton(pressbutton) {
            var form = document.form2;
            if (pressbutton == 'cancel') {
                form.reset();
                history.back();
                return;
            }

         <?php echo $editor->save( 'description' ) ; ?>
        
        // do field validation
		if (form.parent.value < 0) {
            alert( "<?php echo JText::_('You must select a gallery.'); ?>" );
			return;
        }
        if (form.catname1.value == "") {
            alert( "<?php echo JText::_('You must provide a gallery name.'); ?>" );
        }
        else if (form.description.value == ""){
            alert( "<?php echo JText::_('You must provide a description.'); ?>" );
        }
        else{
            form.submit();
        }
        }
    </script>
    <?php
    if ($rows) {
        foreach ($rows as $row){
            $catname        = htmlspecialchars($row->name, ENT_QUOTES);
            $description    = htmlspecialchars($row->description, ENT_QUOTES);
            $ordering       = $row->ordering;
            $uid            = $row->uid;
            $catid          = $row->id;
            $published      = $row->published;
            $user           = $row->user;
            $parent         = $row->parent;
        }
    }
    else{
        $catname        = "";
        $description    = "";
        $ordering       = "";
        $uid            = "";
        $catid          = "";
        $published      = "";
        $user           = "";
        $parent         = "";
    }
    ?>
        <form name="form2" id="form2" method="post" action="<?php echo JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries&task=saveCat"); ?>">
        <table width="100%">
        <tr>
            <td colspan="2"><h3><?php echo JText::_('Create Gallery'); ?></h3></td>
        </tr>
        <tr>

            <td align="right">
                <div style="float: right;">
                    <img onClick="submitbutton('save');" src="<?php echo JURI_SITE; ?>/administrator/images/save.png" alt="<?php echo JText::_('Upload') ?>"  name="upload" onMouseOver="document.upload.src='<?php echo JURI_SITE; ?>/administrator/images/save_f2.png';" onMouseOut="document.upload.src='<?php echo JURI_SITE; ?>/administrator/images/save.png';" />&nbsp;&nbsp;
                    <img onClick="submitbutton('cancel')" src="<?php echo JURI_SITE; ?>/administrator/images/cancel.png" alt="<?php echo JText::_('Cancel'); ?>"  name="cancel" onMouseOver="document.cancel.src='<?php echo JURI_SITE; ?>/administrator/images/cancel_f2.png';" onMouseOut="document.cancel.src='<?php echo JURI_SITE; ?>/administrator/images/cancel.png';" />
                </div>
            </td>

        </tr>
        </table>
        <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
        <input type="hidden" name="ordering" value="<?php echo $ordering; ?>" />
        <table class="adminlist" border="1">
        <tr>
            <th colspan="2"><?php echo JText::_('Create Gallery'); ?></th>
        </tr>
        <tr>
            <td><?php echo JText::_('Top gallery');?></td>
            <td>
                <?php //galleryUtils::showCategories(NULL, $my->id, 'parent');?>
                <?php echo galleryUtils::galleriesSelectList( $parent, 'parent', false );?>
                <?php //galleryUtils::createGalSelectList( NULL, $listName='parent', true );?>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('Gallery name'); ?></td>
            <td align="left"><input type="text" name="catname1" size="30" value="<?php echo $catname; ?>" /></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo JText::_('Description'); ?>
                <?php
                echo $editor->display( 'description',  $description , '600', '200', '35', '15' ) ; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('Published'); ?></td>
            <td align="left"><input type="checkbox" name="published" value="1" <?php if ($published==1) echo "checked"; ?> /></td>
        </tr>
        </table>
        </form>
        <?php
    }

   /* 
	* Function recursiveGalleriesList gets a list of galleries with their id, parent en hierarchy level ordered by ordering and subgalleries grouped by their parent.
	* $id		Gallery parent number
	* $list		The list to return
	* $children	The 2dim. array with children
	* $maxlevel Maximum depth of levels
	* $level	Hierarchy level (e.g. sub gallery of root is level 1)
	* return	Array
	*/
	function recursiveGalleriesList(){
		$user = JFactory::getUser();

		//Function to help out
		function treerecurse($id,  $list, &$children, $maxlevel=20, $level=0) {
			//if there are children for this id and the max.level isn't reached
			if (@$children[$id] && $level <= $maxlevel) {
				//add each child to the $list and ask for its children
				foreach ($children[$id] as $v) {
					$id = $v->id;	//gallery id
					$list[$id] = $v;
					$list[$id]->level = $level;
					//$list[$id]->children = count(@$children[$id]);
					$list = treerecurse($id,  $list, $children, $maxlevel, $level+1);
				}
			}
			return $list;
		}
		// Get a list of all galleries (id/parent) ordered by parent/ordering
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__rsgallery2_galleries ORDER BY parent, ordering";
		$database->setQuery( $query );
		$allGalleries = $database->loadObjectList();
		// Establish the hierarchy by first getting the children: 2dim. array $children[parentid][]
		$children = array();
		if ( $allGalleries ) {
			foreach ( $allGalleries as $v ) {
				$pt     = $v->parent;
				$list   = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		// Get list of galleries with (grand)children in the right order and with level info
		$recursiveGalleriesList = treerecurse( 0, array(), $children, 20, 0 );
		return $recursiveGalleriesList;
	}

}//end class
?>
