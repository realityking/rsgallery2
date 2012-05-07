<?php
/**
* Images option for RSGallery2
* @version $Id: images.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( $rsgOptions_path . 'images.html.php' );
require_once( $rsgOptions_path . 'images.class.php' );
require_once( JPATH_RSGALLERY2_ADMIN . '/admin.rsgallery2.html.php' );

$cid = JRequest::getVar("cid", array(), 'default', 'array' );

switch ($task) {
	case 'new':
		editImage( $option, 0 );
		break;
	
	case 'batchupload':
		HTML_RSGallery::RSGalleryHeader('', JText::_('Upload ZIP-file'));
		batchupload($option);
		HTML_RSGallery::RSGalleryFooter();
		break;
		
	case 'save_batchupload':
		save_batchupload();
        break;
        
	case 'upload':
		uploadImage( $option );
		break;
		
	case 'save_upload':	
		saveUploadedImage( $option );
		break;
		
	case 'edit':
		editImage( $option, $cid[0] );
		break;

	case 'editA':
		editImage( $option, $id );
		break;

	case 'save':
		saveImage( $option );
		break;

	case 'remove':
		removeImages( $cid, $option );
		break;

	case 'publish':
		publishImages( $cid, 1, $option );
		break;

	case 'unpublish':
		publishImages( $cid, 0, $option );
		break;

	case 'approve':
		break;

	case 'cancel':
		cancelImage( $option );
		break;

	case 'orderup':
		orderImages( intval( $cid[0] ), -1, $option );
		break;

	case 'orderdown':
		orderImages( intval( $cid[0] ), 1, $option );
		break;
	
	case 'saveorder':
		saveOrder( $cid );
		break;
	
	case 'reset_hits':
		resetHits( $cid );
		break;
	
	case 'copy_images':
		copyImage( $cid, $option );
		break;
		
	case 'move_images':
		moveImages( $cid, $option );
		break;
		
	case 'showImages':
		showImages( $option );
		break;
		
	default:
		showImages( $option );
}

/**
* Compiles a list of records
* @param database A database connector object
*/
function showImages( $option ) {
	global  $mainframe, $mosConfig_list_limit;

	$database = JFactory::getDBO();
	
	$gallery_id 		= intval( $mainframe->getUserStateFromRequest( "gallery_id{$option}", 'gallery_id', 0 ) );
	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');	
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search 	= $database->getEscaped( trim( strtolower( $search ) ) );

	$where = array();

	if ($gallery_id > 0) {
		$where[] = "a.gallery_id = $gallery_id";
	}
	if ($search) {
		$where[] = "LOWER(a.title) LIKE '%$search%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(1)"
	. "\n FROM #__rsgallery2_files AS a"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( JPATH_ADMINISTRATOR . '/includes/pageNavigation.php' );
	$pageNav = new JPagination( $total, $limitstart, $limit  );

	$query = "SELECT a.*, cc.name AS category, u.name AS editor"
	. "\n FROM #__rsgallery2_files AS a"
	. "\n LEFT JOIN #__rsgallery2_galleries AS cc ON cc.id = a.gallery_id"
	. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY a.gallery_id, a.ordering"
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// build list of categories
	$javascript 	= 'onchange="document.adminForm.submit();"';
	$lists['gallery_id']			= galleryUtils::galleriesSelectList( $gallery_id, 'gallery_id', false, $javascript );
	$lists['move_id']			= galleryUtils::galleriesSelectList( $gallery_id, 'move_id', false, '' );
	html_rsg2_images::showImages( $option, $rows, $lists, $search, $pageNav );
}

/**
* Compiles information to add or edit
* @param integer The unique id of the record to edit (0 if new)
*/
function editImage( $option, $id ) {
	$my = JFactory::getUser();
	$database = JFactory::getDBO();
	
	$lists = array();

	$row = new rsgImagesItem( $database );
	// load the row from the db table
	$row->load( (int)$id );

	// fail if checked out not by 'me'
	if ($row->isCheckedOut( $my->id )) {
		$mainframe->redirect( "index2.php?option=$option&rsgOption=$rsgOption", "The module $row->title is currently being edited by another administrator." );
	}

	if ($id) {
		$row->checkout( $my->id );
	} else {
		// initialise new record
		$row->published = 1;
		$row->approved 	= 1;
		$row->order 	= 0;
		$row->gallery_id 	= intval( rsgInstance::getInt( 'gallery_id', 0 ) );
	}

	// build the html select list for ordering
	$query = "SELECT ordering AS value, title AS text"
	. "\n FROM #__rsgallery2_files"
	. "\n WHERE gallery_id = " . (int) $row->gallery_id
	. "\n ORDER BY ordering"
	;
	$lists['ordering'] 			= JHTML::_('list.specificordering', $row, $id, $query, 1 );

	// build list of categories
	$lists['gallery_id']			= galleryUtils::galleriesSelectList( $row->gallery_id, 'gallery_id', true );
	// build the html select list
	$lists['published'] 		= JHTML::_("select.booleanlist", 'published', 'class="inputbox"', $row->published );

	$file 	= JPATH_ADMINISTRATOR . '/components/com_rsgallery2/options/images.item.xml';
	$params = new JParameter( $row->params, $file);

	html_rsg2_images::editImage( $row, $lists, $params, $option );
}

/**
* Saves the record on an edit form submit
* @param database A database connector object
*/
function saveImage( $option, $redirect = true ) {
	global  $rsgOption, $mainframe;
	
	$database =& JFactory::getDBO();
	$my =& JFactory::getUser();

	$row = new rsgImagesItem( $database );
	if (!$row->bind( JRequest::get('post') )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->descr = JRequest::getVar( 'descr', '', 'post', 'string', JREQUEST_ALLOWRAW );
	//Make the alias for SEF
	if(empty($row->alias)) {
            $row->alias = $row->title;
    }
    $row->alias = JFilterOutput::stringURLSafe($row->alias);
	
	//XHTML COMPLIANCE
	$row->descr = str_replace( '<br>', '<br />', $row->descr );
	
	// save params
	$params = rsgInstance::getVar( 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$row->params = implode( "\n", $txt );
	}

	$row->date = date( 'Y-m-d H:i:s' );
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->reorder( "gallery_id = " . (int) $row->gallery_id );
	
	if ($redirect)
		$mainframe->redirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeImages( $cid, $option ) {
	global  $rsgOption, $rsgConfig, $mainframe;
	$database =& JFactory::getDBO();
	
	$return="index.php?option=$option&rsgOption=images";
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}
	//Delete images from filesystem
	if (count( $cid )) {
		//Delete images from filesystem
		foreach ($cid as $id) {
			$name 		= galleryUtils::getFileNameFromId($id);
			$thumb 		= JPATH_ROOT.$rsgConfig->get('imgPath_thumb') . '/' . imgUtils::getImgNameThumb( $name );
        	$display 	= JPATH_ROOT.$rsgConfig->get('imgPath_display') . '/' . imgUtils::getImgNameDisplay( $name );
        	$original 	= JPATH_ROOT.$rsgConfig->get('imgPath_original') . '/' . $name;
        
        	if( file_exists( $thumb )){
            	if( !JFile::delete( $thumb )){
				JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING THUMB IMAGE') ." ". $thumb);
				$mainframe->redirect( $return );
				return;
				}
			}
			if( file_exists( $display )){
				if( !JFile::delete( $display )){
				JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING DISPLAY IMAGE') ." ". $display);
				$mainframe->redirect( $return );
				return;
				}
			}
			if( file_exists( $original )){
				if( !JFile::delete( $original )){
				JError::raiseNotice('ERROR_CODE', JText::_('ERROR DELETING ORIGINAL IMAGE') ." ". $original);
				$mainframe->redirect( $return );
				return;
				}
			}
		}
		
		//Delete from database
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__rsgallery2_files"
		. "\n WHERE id IN ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect( $return , JText::_('Image(s) deleted succesfully!') );
}


function moveImages( $cid, $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	
	$new_id = rsgInstance::getInt( 'move_id', '' );
	if ($new_id == 0) {
		echo "<script> alert('No gallery selected to move to'); window.history.go(-1);</script>\n";
		exit;
	}
	
	//Move images to another gallery
	foreach ($cid as $id) {
		$query = "UPDATE #__rsgallery2_files"
		. "\n SET gallery_id = " . intval( $new_id )
		. "\n WHERE id = ". intval ( $id )
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			
			exit();
		}
	}
	$mainframe->redirect( "index2.php?option=$option&rsgOption=images", '' );
	
}
/**
* Publishes or Unpublishes one or more records
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current url option
*/
function publishImages( $cid=null, $publish=1,  $option ) {
	global  $rsgOption, $mainframe;
	$database = JFactory::getDBO();
	$my =& JFactory::getUser();

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__rsgallery2_files"
	. "\n SET published = " . intval( $publish )
	. "\n WHERE id IN ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = $my->id ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new rsgImagesItem( $database );
		$row->checkin( $cid[0] );
	}
	$mainframe->redirect( "index2.php?option=com_rsgallery2&rsgOption=$rsgOption" );
}
/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderImages( $uid, $inc, $option ) {
	global  $rsgOption;
	$mainframe =& JFactory::getApplication();
	$database = JFactory::getDBO();
	
	$row = new rsgImagesItem( $database );
	$row->load( (int)$uid );
	$row->move( $inc, "gallery_id = $row->gallery_id" );

	$mainframe->redirect( "index.php?option=$option&rsgOption=$rsgOption" );
}

/**
* Cancels an edit operation
* @param string The current url option
*/
function cancelImage( $option ) {
	global $rsgOption, $mainframe;
	$database = JFactory::getDBO();
	$row = new rsgImagesItem( $database );
	$row->bind( $_POST );
	$row->checkin();
	$mainframe->redirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}

/**
 * Uploads single images
 */
function uploadImage( $option ) {
	$database =& JFactory::getDBO();
	//Check if there are galleries created
	$database->setQuery( "SELECT id FROM #__rsgallery2_galleries" );
    $database->query();
    if( $database->getNumRows()==0 ){
        HTML_RSGALLERY::requestCatCreation( );
        return;
    }
    
	//Create gallery selectlist
	$lists['gallery_id']			= galleryUtils::galleriesSelectList( NULL, 'gallery_id', false );
	html_rsg2_images::uploadImage( $lists, $option );
}

function saveUploadedImage( $option ) {
	global $id, $rsgOption, $mainframe;
	$title = rsgInstance::getVar('title'  , '');  
	$descr = rsgInstance::getVar('descr'  , '', 'post', 'string', JREQUEST_ALLOWRAW); 
	$gallery_id = rsgInstance::getInt('gallery_id'  , '');
	$files = rsgInstance::getVar('images','', 'FILES');

	//For each error that is found, store error message in array
	$errors = array();
	foreach ($files["error"] as $key => $error) {
		if( $error != UPLOAD_ERR_OK ) {
			if ($error == 4) {//If no file selected, ignore
				continue;
			} else {
				//Create meaningfull error messages and add to error array
				$error = fileHandler::returnUploadError( $error );
				$errors[] = new imageUploadError($files["name"][$key], $error);
				continue;
			}
		}

		//Special error check to make sure the file was not introduced another way.
		if( !is_uploaded_file( $files["tmp_name"][$key] )) {
			$errors[] = new imageUploadError( $files["tmp_name"][$key], "not an uploaded file, potential malice detected!" );
			continue;
		}
		//Actually importing the image
		$e = fileUtils::importImage($files["tmp_name"][$key], $files["name"][$key], $gallery_id, $title[$key], $descr);
		if ( $e !== true )
			$errors[] = $e;

	}
	//Error handling if necessary
	if ( count( $errors ) == 0){
		$mainframe->redirect( "index2.php?option=$option&rsgOption=$rsgOption", JText::_('Item uploaded succesfully!') );
	} else {
		//Show error message for each error encountered
		foreach( $errors as $e ) {
			JError::raiseWarning(0, $e->toString());
		}
		//If there were more files than errors, assure the user the rest went well
		if ( count( $errors ) < count( $files["error"] ) ) {
			echo "<br>".JText::_('the rest of your files were uploaded fine');
		}
	}		
}

/**
 * Resets hits to zero
 * @param array image id's
 * @todo Warn user with alert before actually deleting
 */
function resetHits ( &$cid ) {
	global $mainframe;
	$database =& JFactory::getDBO();

	$total		= count( $cid );
	/*
	echo "Reset hits for $total images";
	echo "<pre>";
	print_r( $cid );
	echo "</pre>";
	*/
	//Reset hits
	$cids = implode( ',', $cid );

	$database->setQuery("UPDATE #__rsgallery2_files SET ".
			"hits = 0 ".
			"WHERE id IN ( $cids )");

	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
	}

	$mainframe->redirect( "index2.php?option=com_rsgallery2&rsgOption=images", JText::_('Hits reset to zero succesfull') );
}

function saveOrder( &$cid ) {
	global $mainframe;
	$database =& JFactory::getDBO();

	$total		= count( $cid );
	$order 		= JRequest::getVar("order", array(), 'default', 'array' );

	$row 		= new rsgImagesItem( $database );
	
	$conditions = array();

	// update ordering values
	for ( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "gallery_id=" . (int) $row->gallery_id;
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true;
					break;
				} // if
			if (!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->reorder( $cond[1] );
	} // foreach

	// clean any existing cache files
	$cache =& JFactory::getCache();
	$cache->clean( 'com_rsgallery2' );

	$msg 	= 'New ordering saved';
	$mainframe->redirect( 'index2.php?option=com_rsgallery2&rsgOption=images', $msg );
} // saveOrder

function copyImage( $cid, $option ) {	
	global $mainframe;
	$database =& JFactory::getDBO();

	//For each error that is found, store error message in array
	$errors = array();
	
	$cat_id = rsgInstance::getInt('move_id', '' );//get gallery id to copy item to
	if (!$cat_id) {
		echo "<script> alert('No gallery selected to move to'); window.history.go(-1);</script>\n";
		exit;
	}
	
    //Create unique copy name
    $tmpdir	= uniqid( 'rsgcopy_' );
    
    //Get full path to copy directory
	$copyDir = JPath::clean( JPATH_ROOT . '/media/' . $tmpdir . '/' );
    if( !JFolder::create($copyDir ) ) {
    		$errors[] = 'Unable to create temp directory ' . $copyDir; 
    } else {
	    foreach( $cid as $id ) {
			$gallery = rsgGalleryManager::getGalleryByItemID($id);
	    	$item = $gallery->getItem( $id );
	    	$original = $item->original();
	    	$source = $original->filePath();
	    	
	    	$destination = $copyDir . $item->name;
	    	
	    	if( is_dir($copyDir) ) {
	    		if( file_exists( $source ) ) {
	    			
	    			if(!JFile::copy( $source, $destination)){
	    				$errors[] = 'The file could not be copied!';
	    			} else {
						//Actually importing the image
						$e = fileUtils::importImage($destination, $item->name, $cat_id, $item->title, $item->description);
						if ( $e !== true )	$errors[] = $e;
						if(!JFile::delete($destination)) $errors[] = 'Unable to delete the file' . $item->name;
					}
				}
			}
	    }
	    
	    if(!rmdir($copyDir)) $errors[] = 'Unable to delete the temp directory' . $copyDir;	
    }

	//Error handling if necessary
	if ( count( $errors ) == 0){
		$mainframe->redirect( "index2.php?option=$option&rsgOption=images", JText::_('Item(s) copied successfully!') );
	} else {
		//Show error message for each error encountered
		foreach( $errors as $e ) {
			echo $e->toString();
		}
		//If there were more files than errors, assure the user the rest went well
		if ( count( $errors ) < count( $files["error"] ) ) {
			echo "<br>".JText::_('Rest of the items copied successfully!');
		}
	}
}

function batchupload($option) {
	global $rsgConfig, $mainframe;
	$database = JFactory::getDBO();
	$FTP_path = $rsgConfig->get('ftp_path');
	
	//Retrieve data from submit form
	$batchmethod 	= rsgInstance::getVar('batchmethod', null);
	$uploaded 		= rsgInstance::getVar('uploaded', null);
	$selcat 		= rsgInstance::getInt('selcat', null);
	$zip_file 		= rsgInstance::getVar('zip_file', null, 'FILES'); 
	$ftppath 		= rsgInstance::getVar('ftppath', null);
	$xcat 			= rsgInstance::getInt('xcat', null);
	
	//Check if a gallery exists, if not link to gallery creation
	$database->setQuery( "SELECT id FROM #__rsgallery2_galleries" );
	$database->query();
	if( $database->getNumRows()==0 ){
		HTML_RSGALLERY::requestCatCreation( );
		return;
	}
	
	//New instance of fileHandler
	$uploadfile = new fileHandler();
	
	if (isset($uploaded)) {
		if ($batchmethod == "zip") {
			//Check if file is really a ZIP-file
			if (!eregi( '.zip$', $zip_file['name'] )) {
				$mainframe->redirect( "index2.php?option=com_rsgallery2&task=batchupload", $zip_file['name'].JText::_(' is not a valid archive format. Only ZIP-files are allowed!'));
			} else {
				//Valid ZIP-file, continue
				if ($uploadfile->checkSize($zip_file) == 1) {
					$ziplist = $uploadfile->handleZIP($zip_file);
				} else {
					//Error message
					$mainframe->redirect( "index2.php?option=com_rsgallery2&task=batchupload", JText::_('ZIP-file is too big!'));
				}
			}
		} else {
			$ziplist = $uploadfile->handleFTP($ftppath);
		}
		html_rsg2_images::batchupload_2($ziplist, $uploadfile->extractDir);
	} else {
		html_rsg2_images::batchupload($option);
	}
}//End function

function save_batchupload() {
    global  $rsgConfig, $mainframe;
	$database = JFactory::getDBO();
    //Try to bypass max_execution_time as set in php.ini
    set_time_limit(0);
    
    $FTP_path = $rsgConfig->get('ftp_path');

    $teller 	= rsgInstance::getInt('teller'  , null);
    $delete 	= rsgInstance::getVar('delete'  , null);
    $filename 	= rsgInstance::getVar('filename'  , null);
    $ptitle 	= rsgInstance::getVar('ptitle'  , null);
    $descr 		= rsgInstance::getVar('descr'  , array(0));
	$extractdir = rsgInstance::getVar('extractdir'  , null);
	
    //Check if all categories are chosen
	if (isset($_REQUEST['category']))
		$category = rsgInstance::getVar('category'  , array(0));
    else
        $category = array(0);

    if ( in_array('0', $category) || 
		 in_array('-1', $category)) {
        $mainframe->redirect("index2.php?option=com_rsgallery2&task=batchupload", JText::_('_RSGALLERY_ALERT_NOCATSELECTED'));
	}

     for($i=0;$i<$teller;$i++) {
        //If image is marked for deletion, delete and continue with next iteration
        if (isset($delete[$i]) AND ($delete[$i] == 'true')) {
            //Delete file from server
            unlink(JPATH_ROOT . "/media/" . $extractdir . '/' . $filename[$i]);
            continue;
        } else {
            //Setting variables for importImage()
            $imgTmpName = JPATH_ROOT . "/media/" . $extractdir . '/' . $filename[$i];
            $imgName 	= $filename[$i];
            $imgCat	 	= $category[$i];
            $imgTitle 	= $ptitle[$i];
            $imgDesc 	= $descr[$i];
            
            //Import image
            $e = imgUtils::importImage($imgTmpName, $imgName, $imgCat, $imgTitle, $imgDesc);
            
            //Check for errors
            if ( $e !== true ) {
                $errors[] = $e;
            }
        }
    }
    //Clean up mediadir
    fileHandler::cleanMediaDir( $extractdir );
    
    // Error handling
    if (isset($errors )) {
        if ( count( $errors ) == 0) {
            echo JText::_('Item uploaded succesfully!');
        } else {
            foreach( $errors as $err ) {
                echo $err->toString();
            }
        }
    } else {
        //Everything went smoothly, back to Control Panel
		global $mainframe;
		$mainframe->redirect("index2.php?option=com_rsgallery2", JText::_('Item uploaded succesfully!'));
    }
}
