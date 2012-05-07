<?php
/**
* This file handles gallery manipulation functions for RSGallery2
* @version $Id: gallery.manager.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/
defined( '_JEXEC' ) or die( 'Access Denied.' );

/**
* Gallery utilities class
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgGalleryManager{

	/**
	 * returns the rsgGallery object which contains item id
	 *
	 * @param id of item
	 */
	function getGalleryByItemID( $id = null ) {
		$database =& JFactory::getDBO();
		
		if( $id === null ){
			$id = rsgInstance::getInt( 'id', 0 );
		}
		
		if( !is_numeric( $id )) return false;
		$query = "SELECT f.gallery_id FROM #__rsgallery2_files AS f WHERE f.id = $id";
		$database->setQuery ($query);
		$gid = $database->loadResult();
		
		if ($gid) {
			return rsgGalleryManager::get( $gid );	
		} else {
			JError::raiseError( 'RSG2 ERROR ID', JText::sprintf('COM_RSGALLERY2_ITEM_ID_DOES_NOT_EXIST', $id));
		}
	}
	
	/**
		@deprecated Use rsgGallery->getItem() instead!
	**/
	function getItem( $id = null ){
		$gallery = rsgGalleryManager::get();
		return $gallery->getItem($id);
	}

    /**
     * returns a gallery
     * @param id of the gallery
     * @todo move published check to rsgAccess
     */
	function get( $id = null ){
		global $rsgAccess, $rsgConfig;
		$my =& JFactory::getUser();
		
		if( $id === null ){
			$id = rsgInstance::getInt( 'catid', 0 );
			$id = rsgInstance::getInt( 'gid', $id );
			
			if( !$id ){
				// check if an item id is set and if so return the gallery for that item id
				if( rsgInstance::getInt( 'id', 0 ))
					return rsgGalleryManager::getGalleryByItemID();
			}
		}

		// since the user will never be offered the chance to view a gallery they can't, unauthorized attempts at viewing are a hacking attempt, so it is ok to print an unfriendly error.
		$rsgAccess->checkGallery( 'view', $id ) or die("RSGallery2: Access denied to gallery $id");

		$gallery = rsgGalleryManager::_get( $id );

		// if gallery is unpublished don't show it unless ACL is enabled and users has permissions to modify (owners can view their unpublished galleries).
		if( $gallery->get('published') < 1 ) {
			
			// if user is admin or superadmin then always return the gallery
			if ( $my->gid > 23 )
				return $gallery;

			if( $rsgConfig->get( 'acl_enabled' )){
				if( !$rsgAccess->checkGallery( 'create_mod_gal', $id )) die("RSGallery2: Access denied to gallery $id");
			}
			else
				die("RSGallery2: Access denied to gallery $id");
		}

		return $gallery;
	}

    /**
     * returns an array of all images in $parent and sub galleries
     * @param int id of parent gallery
     * @todo this is a stub, no functionality yet
     */
    function getFlatArrayofImages( $parent ){
        return true;
    }
    /**
     * returns an array of all sub galleris in $parent including $parent
     * @param int id of parent gallery
     * @todo this is a stub, no functionality yet
     */
    function getFlatArrayofGalleries( $parent ){
        return true;
    }

    /**
     * returns an array of galleries from an array of IDs
     * @param id of the gallery
     */
    function getArray( $cid ){
        $galleries = array();
        
        foreach( $cid as $gid ){
            $galleries[] = rsgGalleryManager::_get( $gid );
        }
        return $galleries;
    }
    
    /**
     * returns an array of galleries
     * @param id of parent gallery
     */
    function getList( $parent ){
        global $rsgAccess, $rsgConfig;
		$database = JFactory::getDBO();
        if( !is_numeric( $parent )) return false;
        
        // since the user will never be offered the chance to view a gallery they can't, unauthorized attempts at viewing are a hacking attempt, so it is ok to print an unfriendly error.
        $rsgAccess->checkGallery( 'view', $parent ) or die("RSGallery2: Access denied to gallery $parent");

        $database->setQuery("SELECT * FROM #__rsgallery2_galleries".
                            " WHERE parent = '$parent'".
                            " ORDER BY ordering ASC");
        $rows = $database->loadAssocList();
        $galleries = array();

        foreach( $rows as $row ){
            // check if user has view access
            if( !$rsgAccess->checkGallery( 'view', $row['id'] )) continue;

            // if gallery is unpublished don't show it unless ACL is enabled and users has permissions to modify (owners can view their unpublished galleries).
            if( $row['published']<1 ){
                if( $rsgConfig->get( 'acl_enabled' )){
                    if( !$rsgAccess->checkGallery( 'create_mod_gal', $row['id'] )) continue;
                }
                else{
                    continue;
                }
            }
            
            $galleries[] = new rsgGallery( $row );
        }

        return $galleries;
    }

    /**
     * recursively deletes all galleries and subgalleries in array
     * @param array of gallery ids
     */
    function deleteArray( $cid ){
        global $rsgAccess;

        // check if user has access
        // note we don't check sub galleries of these galleries.  if a user has the right to delete a gallery, they automatically have the right to delete any sub galleries therein.
        foreach( $cid as $gid ){
            // an unfriendly error since the user will never be offered the chance to delete a gallery they cannot.
            $rsgAccess->checkGallery( 'delete', $gid ) or die("RSGallery2: Access denied to delete gallery $gallery");
        }

        // delete all galleries and sub galleries
        $galleries = rsgGalleryManager::_getArray( $cid );

        return rsgGalleryManager::_deleteTree( $galleries );
    }

    /*
        private functions
        no access checks are made, do not use outside this class!
    */

	/**
	 * returns a gallery
	 * @param rsgGallery object
	*/
	function _get( $gallery ){
		static $galleries = array();

		if( !isset( $galleries[$gallery] )){
			$database =& JFactory::getDBO();
		
			if( !is_numeric( $gallery )) die("gallery id is not a number: $gallery");
			
			$database->setQuery("SELECT * FROM #__rsgallery2_galleries ".
								"WHERE id = '$gallery' ".
								"ORDER BY ordering ASC ");
		
			$row = $database->loadAssocList();
			if( count($row)==0 && $gallery!=0 ){
				JError::raiseError( 1, "gallery id does not exist: $gallery" );
			}
			else if( count($row)==0 && $gallery==0 ){
				// gallery is root, and it aint in the db, so we have to create it.
				return rsgGalleryManager::_getRootGallery();
			}
			$row = $row[0];
		
			$galleries[$gallery] = new rsgGallery( $row );
		}
		return $galleries[$gallery];
	}

    /**
     * return the top level gallery
     * this is a little interesting, because the top level gallery is a pseudo gallery, but we need to create some 
     * usefull values so that it can be used as a real gallery.
     * @todo possibly have the top level gallery be a real gallery in the db.  this obviously needs to be discussed more.
     * @todo are these good defaults?  not sure....
     * @param rsgGallery object
     */
    function _getRootGallery(){
        global $rsgConfig;

        return new rsgGallery( array(
            'id'=>0,
            'parent'=>null,
            'name'=>'',
            'description'=>$rsgConfig->get("intro_text"),
            'published'=>1,
            'checked_out'=>false,
            'checked_out_time'=>null,
            'ordering'=>0,
            'date'=>'0000-00-00 00:00:00',
            'hits'=>0,
            'params'=>'',
            'user'=>'',
            'uid'=>'',
            'allowed'=>'',
            'thumb_id'=>''
        ));
    }
    
    /**
     * returns an array of galleries from an array of IDs
     * @param id of the gallery
     */
    function _getArray( $cid ){
        $galleries = array();
        
        foreach( $cid as $gid ){
            $galleries[] = rsgGalleryManager::_get( $gid );
        }
        return $galleries;
    }

    /**
     * recursively deletes a tree of galleries
     * @param id of the gallery
     * @todo this is a quick hack.  galleryUtils and imgUtils need to be reorganized; and a rsgImage class created to do this proper
     */
    function _deleteTree( $galleries ){
        global $rsgAccess;
		$database =& JFactory::getDBO();
        foreach( $galleries as $gallery ){
            rsgGalleryManager::_deleteTree( $gallery->kids() );

            // delete images in gallery
            foreach( $gallery->items() as $item ){
                imgUtils::deleteImage( galleryUtils::getFileNameFromId( $item->id ));
            }

            // delete gallery
            $id = $gallery->get('id');
            if( !is_numeric( $id )) return false;

            $query = "DELETE FROM #__rsgallery2_galleries WHERE id = $id";
            echo "<br>deleting gallery $id";

            $database->setQuery( $query );
            if (!$database->query())
                echo $database->error();

            // Delete permissions here
            $rsgAccess->deletePermissions( $id );
        }
    }
}