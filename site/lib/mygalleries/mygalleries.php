<?php
/**
* This file contains code for frontend My Galleries.
* @version $Id: mygalleries.php 1070 2012-03-25 11:40:51Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2012 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
global $mainframe,$rsgConfig;
$document=& JFactory::getDocument();

if($document->getType() == 'html') {
	$css = "<link rel=\"stylesheet\" href=\"".JURI::root()."components/com_rsgallery2/lib/mygalleries/mygalleries.css\" type=\"text/css\" />";	
	$document->addCustomTag($css);
	$css = "<link rel=\"stylesheet\" href=\"".JURI::root()."components/com_rsgallery2/templates/".$rsgConfig->template."/css/template.css\" type=\"text/css\" />";
	$document->addCustomTag($css);
}

//Load required class file
require_once( JPATH_RSGALLERY2_SITE . DS . 'lib' . DS . 'mygalleries' . DS . 'mygalleries.class.php' );

//Get parameters from URL and/or form
//$cid	= rsgInstance::getInt('cid', array(0) );//no longer neccessary?
//$cid	= rsgInstance::getInt('gid', $cid );//no longer neccessary?
$task   = rsgInstance::getVar('task', '' );
$id		= rsgInstance::getInt('id','' );
$gid	= rsgInstance::getInt('gid','' );	//Mirjam: In v1.13 catid was used where since v1.14 gid is used

switch( $task ){
    case 'saveUploadedItem':
    	saveuploadedItem();
    	break;
    case 'editItem':
    	editItem();
    	break;
    case 'deleteItem':
    	deleteItem();
    	break;
    case 'saveItem':
    	saveItem();
    	break;
    case 'newCat':
    	editCat(null);
    	break;
    case 'editCat':
    	editCat($gid);  		
    	break;
    case 'saveCat':
    	saveCat();
    	break;
    case 'deleteCat':
    	deleteCat();
    	break;
	default:
		showMyGalleries();
		break;
}

function showMyGalleries() {
	global $rsgConfig, $mainframe;
	$my = JFactory::getUser();
	$database = JFactory::getDBO();
	
	//Check if My Galleries is enabled in config, if not .............. 
	if ( !$rsgConfig->get('show_mygalleries') ) die(JText::_('Unauthorized access attempt to My Galleries!'));
	
	//Add gallery pathway items
	$pathway =& $mainframe->getPathway();
	$pathway->addItem( JText::_('my galleries') );

	//Set limits for pagenav
	$limit      = trim(rsgInstance::getInt( 'limit', 10 ) );
	$limitstart = trim(rsgInstance::getInt( 'limitstart', 0 ) );
	
	//Get total number of records for paging
	$database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_files WHERE userid = '$my->id'");
	$total = $database->loadResult();
	
	//New instance of mosPageNav
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );
	
	//Get all images
	$database->setQuery("SELECT * FROM #__rsgallery2_files" .
						" WHERE userid = '$my->id'" .
						" ORDER BY date DESC" .
						" LIMIT $pageNav->limitstart, $pageNav->limit");
	$images = $database->loadObjectList();
	//Get all galleries based on hierarchy (up to 20 levels deep, with level property to show the level)
	$rows = myGalleries::recursiveGalleriesList();

	if($my->id) {
		//User is logged in, show it all!
		myGalleries::viewMyGalleriesPage($rows, $images, $pageNav);
	} else {
		//Not logged in, back to main page
		$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2"), JText::_('MY GALLERIES ACCESSIBLE FOR LOGGED ON USERS') );
	}	
}

/**
 * Deletes an item through the frontend My Galleries part
 */
function deleteItem() {
	global $rsgAccess, $mainframe;
	$my = JFactory::getUser();
	$database = JFactory::getDBO();
	$id = rsgInstance::getInt( 'id'  , '');
	if ($id) {		
		//Get gallery id
		$gallery_id = galleryUtils::getCatidFromFileId($id);
		
		//Check if file deletion is allowed in this gallery
		if ($rsgAccess->checkGallery('del_img', $gallery_id )) {
			$filename 	= galleryUtils::getFileNameFromId($id);
			imgUtils::deleteImage($filename);
			$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries"), JText::_('Image is deleted') );
		} else {
			$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries"), JText::_('USERIMAGE_NOTOWNER') );
		}
	} else {
		//No ID sent, no delete possible, back to my galleries
		$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries"), JText::_('No Id provided. Contact component developer') );
	}
}

function editItem() {
	$database = JFactory::getDBO();
	$id = rsgInstance::getInt('id'  , null);
	if ($id) {
		$database->setQuery("SELECT * FROM #__rsgallery2_files WHERE id = '$id'");
		$rows = $database->loadObjectList();
		myGalleries::editItem($rows);
	}
}

function saveItem() {
	global $mainframe;
	$database = JFactory::getDBO();
	
	//Set redirect URL
	$redirect = JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries", false);
	
	$id 	= rsgInstance::getInt( 'id'  , '');
	$title 	= rsgInstance::getstring( 'title'  , '');
	$descr 	= rsgInstance::getVar( 'descr'  , '', 'post', 'string', JREQUEST_ALLOWRAW);
	$catid 	= rsgInstance::getInt( 'catid'  , '');

	//escape strings for sql query
	$title 	= $database->getEscaped($title);
	$descr 	= $database->getEscaped($descr);
	
	$database->setQuery("UPDATE #__rsgallery2_files SET ".
			"title = '$title', ".
			"descr = '$descr', ".
			"gallery_id = '$catid' ".
			"WHERE id= '$id'");

	if ($database->query()) {
		$mainframe->redirect(JRoute::_( $redirect ), JText::_('Details saved succesfully') );
	} else {
		//echo JText::_('Error: ').mysql_error();
		$mainframe->redirect( $redirect , JText::_('Could not update image details') );
	}
}

function saveUploadedItem() {
	global $rsgConfig, $rsgAccess, $mainframe;
	$database = JFactory::getDBO();
	//Set redirect URL
	$redirect = JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries",false);
	
	//Get category ID to check rights
	$i_cat = rsgInstance::getVar( 'i_cat'  , '');
	
	//Get maximum number of images to upload
	$max_images = $rsgConfig->get('uu_maxImages');
	
	//Check if user can upload in this gallery
	if ( !$rsgAccess->checkGallery('up_mod_img', $i_cat) ) die('Unauthorized upload attempt!');
	
	//Check if number of images is not exceeded
	$count = 0;
	if ($count > $max_images) {
		//Notify user and redirect
	} else {
		//Go ahead and upload
		$upload = new fileHandler();
		
		//Get parameters from form
		$i_file = rsgInstance::getVar( 'i_file', null, 'files', 'array'); 
		$i_cat = rsgInstance::getInt( 'i_cat'  , ''); 
		$title = rsgInstance::getVar( 'title'  , ''); 
		$descr = rsgInstance::getVar( 'descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$uploader = rsgInstance::getVar( 'uploader'  , ''); 
		
		//Get filetype
		$file_ext = $upload->checkFileType($i_file['name']);

		//Check whether directories are there and writable
		$check = $upload->preHandlerCheck();
		if ($check !== true ) {
			$mainframe->redirect( $redirect , $check);
		}

		switch ($file_ext) {
			case 'zip':
        		if ($upload->checkSize($i_file) == 1) {
            		$ziplist = $upload->handleZIP($i_file);
            		
            		//Set extract dir for uninstall purposes
            		$extractdir = JPATH_ROOT . DS . "media" . DS . $upload->extractDir . DS;
            		
            		//Import images into right folder
            		for ($i = 0; $i<sizeof($ziplist); $i++) {
            			$import = imgUtils::importImage($extractdir . $ziplist[$i], $ziplist[$i], $i_cat);
            		}
            		
            		//Clean mediadir
            		fileHandler::cleanMediaDir( $upload->extractDir );
            		
            		//Redirect
            		$mainframe->redirect( $redirect , JText::_('Item uploaded succesfully!') );
            		
        		} else {
            		//Error message
            		$mainframe->redirect( $redirect , JText::_('ZIP-file is too big!'));
        		}
				break;
			case 'image':
				//Check if image is too big
				if ($i_file['error'] == 1)
					$mainframe->redirect( $redirect , JText::_('Image size is too big for upload') );
				
				$file_name = $i_file['name'];
				if ( move_uploaded_file($i_file['tmp_name'], JPATH_ROOT . DS ."media" . DS . $file_name) ) {
					//Import into database and copy to the right places
					$imported = imgUtils::importImage(JPATH_ROOT . DS ."media" . DS . $file_name, $file_name, $i_cat, $title, $descr);
					
					if ($imported == 1) {
						if (file_exists( JPATH_ROOT . DS ."media" . DS . $file_name ))
							unlink( JPATH_ROOT . DS ."media" . DS . $file_name );
					} else {
						$mainframe->redirect( $redirect , 'Importing image failed! Notify RSGallery2. This should never happen!');
					}
					$mainframe->redirect( $redirect , JText::_('Item uploaded succesfully!') );
				} else {
					$mainframe->redirect( $redirect , JText::_('UPLOAD FAILED BACK TO UPLOADSCREEN') );
				}
				break;
			case 'error':
				$mainframe->redirect( $redirect , JText::_('WRONG IMAGE FORMAT. WE WILL REDIRECT YOU TO THE UPLOAD SCREEN') );
				break;
		}
	}
}

function editCat($catid) {
	//Mirjam: In v1.13 catid was used where since v1.14 gid is used, but locally in a function catid is fine
	global $rsgConfig;
	$my = JFactory::getUser();
	$database = JFactory::getDBO();

	if ($catid) {
		//Edit category
		$database->setQuery("SELECT * FROM #__rsgallery2_galleries WHERE id ='$catid'");
		$rows = $database->LoadObjectList();
		myGalleries::editCat($rows);
	} else {
		//Check if maximum number of usercats are already made
		$count = galleryUtils::userCategoryTotal($my->id);
		if ($count >= $rsgConfig->get('uu_maxCat') ) {
			$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&page=my_galleries"), JText::_('MAX_USERCAT_ALERT') );
		} else {
			//New category
			myGalleries::editCat();
		}
	}
}

function saveCat() {
	global $rsgConfig, $mainframe;
	
	$my = JFactory::getUser();
	$database = JFactory::getDBO();

	//If gallery creation is disabled, unauthorized attempts die here.
	if (!$rsgConfig->get('uu_createCat')) die ("User category creation is disabled by administrator.");
	
	//Set redirect URL
	$redirect = JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries", false);
	
	$parent 		= rsgInstance::getVar( 'parent'  , 0);
	$id 			= rsgInstance::getInt( 'catid'  , null);
	$catname1 		= rsgInstance::getstring( 'catname1'  , null);
	$description 	= rsgInstance::getVar( 'description'  , null, 'post', 'string', JREQUEST_ALLOWRAW);
	$published 		= rsgInstance::getInt( 'published'  , 0);
	$ordering 		= rsgInstance::getInt( 'ordering'  , null);
	$maxcats        = $rsgConfig->get('uu_maxCat');	

	//escape strings for sql query
	$alias			= $database->getEscaped(JFilterOutput::stringURLSafe($catname1));
	$catname1 		= $database->getEscaped($catname1);
	$description 	= $database->getEscaped($description);

	if ($id) {
		$database->setQuery("UPDATE #__rsgallery2_galleries SET ".
			"name = '$catname1', ".
			"description = '$description', ".
			"published = '$published', ".
			"parent = '$parent' ".
			"WHERE id = '$id' ");
		if ($database->query()) {
			$mainframe->redirect( $redirect , JText::_('Gallery details updated!') );
		} else {
			$mainframe->redirect( $redirect , JText::_('Could not update gallery details!') );
		}
	} else {
		//New category
		$userCatTotal = galleryUtils::userCategoryTotal($my->id);
		if (!isset($parent))
			$parent = 0;
		if ($userCatTotal >= $maxcats) {
			?>
				<script type="text/javascript">
				//<![CDATA[
				alert('<?php echo JText::_('MAX_USERCAT_ALERT');?>');
				location = '<?php echo JRoute::_("index.php?option=com_rsgallery2&page=my_galleries", false); ?>';
				//]]>
				</script>
				<?php
			//$mainframe->redirect( $redirect ,JText::_('MAX_USERCAT_ALERT'));
			
		} else {
			//Create ordering, start at last position
			$database->setQuery("SELECT MAX(ordering) FROM #__rsgallery2_galleries WHERE uid = '$my->id'");
			$ordering = $database->loadResult() + 1;
			//Insert into database
			$database->setQuery("INSERT INTO #__rsgallery2_galleries ".
				"(name, description, alias, ordering, parent, published, user, uid, date) VALUES ".
				"('$catname1','$description','$alias','$ordering','$parent','$published','1' ,'$my->id', now())");
				
			if ($database->query()) {
				//Create initial permissions for this gallery
				$database->setQuery("SELECT id FROM #__rsgallery2_galleries WHERE name = '$catname1' LIMIT 1");
				$gallery_id = $database->loadResult();
				$acl = new rsgAccess();
				if ( $acl->createDefaultPermissions($gallery_id) )
					$mainframe->redirect( $redirect , JText::_('New gallery created!') );
			} else {
				$mainframe->redirect( $redirect , JText::_('ALERT_NONEWCAT') );
			}
		}
	}
	//$mainframe->redirect( $redirect  );
}

function deleteCat() {
	global $rsgConfig, $mainframe;
	$my = JFactory::getUser();
	$database = JFactory::getDBO();

	//Get values from URL
	$catid = rsgInstance::getInt( 'gid'  , null);//Mirjam: catid is gid as of v1.14

	//Set redirect URL
	$redirect = JRoute::_("index.php?option=com_rsgallery2&rsgOption=myGalleries",false);
	
	//Get category details
	$database->setQuery("SELECT * FROM #__rsgallery2_galleries WHERE id = '$catid'");
	$rows = $database->LoadObjectList();
	foreach ($rows as $row) {
		$uid = $row->uid;
		$parent = $row->parent;
	}
		
	//Check if gallery has children
	$database->setQuery("SELECT COUNT(1) FROM #__rsgallery2_galleries WHERE parent = '$catid'");
	$count = $database->loadResult();
	if ($count > 0) {
		$mainframe->redirect( $redirect ,JText::_('USERCAT_SUBCATS'));
	}
	
	//No children from here, so lets continue
	if ($uid == $my->id OR $my->usertype == 'Super Administrator') {
		//Delete images
		$database->setQuery("SELECT name FROM #__rsgallery2_files WHERE gallery_id = '$catid'");
		$result = $database->loadResultArray();
		$error = 0;
		foreach ($result as $filename) {
			if ( !imgUtils::deleteImage($filename) ) 
				$error++;
		}
		
		//Error checking
		if ($error == 0) {
			//Gallery can be deleted
			$database->setQuery("DELETE FROM #__rsgallery2_galleries WHERE id = '$catid'");
			if ( !$database->query() ) {
				//Error message, gallery could not be deleted
				$mainframe->redirect( $redirect ,JText::_('Gallery could not be deleted!'));
			} else {
				//Ok, goto mainpage
				$mainframe->redirect( $redirect ,JText::_('Gallery deleted!'));
			}
		} else {
			//There were errors. Gallery will not be deleted
			$mainframe->redirect( $redirect ,JText::_('Gallery could not be deleted!'));
		}
	} else {
		//Abort and return to mainscreen
		$mainframe->redirect( $redirect ,JText::_('USER_CAT_NOTOWNER'));
	}
}
?>