<?php
/**
* RSGallery Toolbar Menu HTML
* @version $Id: toolbar.rsgallery2.html.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
**/

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class menu_rsg2_maintenance{
	
	function regenerateThumbs() {

		JToolBarHelper::custom('executeRegenerateImages','forward.png','forward.png',JText::_('MAINT_REGEN_BUTTON'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true);
		
	}
	
}


class menu_rsg2_images{
    function upload() {
		JToolBarHelper::title( JText::_('Upload'), 'generic.png' );
        JToolBarHelper::spacer();
        JToolBarHelper::custom('save_upload','upload.png','upload.png',JText::_('Upload'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        
    }
    function show(){
        JToolBarHelper::title( JText::_('Manage Items'), 'generic.png' );
        JToolBarHelper::custom('move_images','forward.png','forward.png',JText::_('Move To'), true);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('copy_images','copy.png','copy.png',JText::_('Copy'), true);
        JToolBarHelper::spacer();
        JToolBarHelper::publishList();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublishList();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('upload','upload.png','upload.png',JText::_('Upload'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::editListX();
        JToolBarHelper::spacer();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('reset_hits','default.png','default.png',JText::_('Reset hits'), true);
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        //menuRSGallery::adminTasksMenu();
    }
    function edit() {
        global $id;

        
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        if ( $id ) {
            // for existing content items the button is renamed `close`
            JToolBarHelper::cancel( 'cancel', JText::_('Close') );
        } else {
            JToolBarHelper::cancel();
        }
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        
    }
    function remove() {
        global $id;

        
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('removeReal','delete_f2.png','',JText::_('Confirm removal'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        
    }
}

class menu_rsg2_galleries{
    function show(){
		JToolBarHelper::title( JText::_('Manage Galleries'), 'generic.png' );
        JToolBarHelper::spacer();
        JToolBarHelper::publishList();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublishList();
        JToolBarHelper::spacer();
        JToolBarHelper::editListX();
        JToolBarHelper::spacer();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        JToolBarHelper::addNewX();
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2' ,true);
        //menuRSGallery::adminTasksMenu();
    }
    function edit() {
        global $id;

        
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        if ( $id ) {
            // for existing content items the button is renamed `close`
            JToolBarHelper::cancel( 'cancel', JText::_('Close') );
        } else {
            JToolBarHelper::cancel();
        }
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        
    }
    function remove() {
        global $id;

        
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::trash('removeReal', JText::_('Confirm removal'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::help( 'screen.rsgallery2',true );
        
    }
}

class menuRSGallery {

    function adminTasksMenuX(){
        

        // do we want an admin tasks menu for navigation?
        /*
        JToolBarHelper::spacer();
        JToolBarHelper::spacer();
        JToolBarHelper::divider();
        JToolBarHelper::spacer();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('controlPanel', '../components/com_rsgallery2/images/rsg2-cpanel.png', '../components/com_rsgallery2/images/rsg2-cpanel.png', JText::_('CPanel'), false);
        JToolBarHelper::custom('view_categories', '../components/com_rsgallery2/images/rsg2-categories.png', '../components/com_rsgallery2/images/rsg2-categories.png', JText::_('Galleries'), false);
        JToolBarHelper::custom('view_images', '../components/com_rsgallery2/images/rsg2-mediamanager.png', '../components/com_rsgallery2/images/rsg2-mediamanager.png', JText::_('Images'), false);
        JToolBarHelper::custom('upload', 'upload_f2.png', 'upload_f2.png', JText::_('Upload'), false);
        
        */
    }
    
    function image_new()
        {
        JToolBarHelper::save();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        
        }

    function image_edit()
        {
        
        JToolBarHelper::save('save_image');
        JToolBarHelper::cancel('view_images');
        JToolBarHelper::spacer();
        
        }
    
    function image_batchUpload()
        {
		JToolBarHelper::title( JText::_('Batch Upload'), 'generic.png' );
        if( rsgInstance::getVar('uploaded'  , null) )
        	JToolBarHelper::custom('save_batchupload','upload.png','upload.png',JText::_('Upload'), false);
		else
        	JToolBarHelper::custom('batchupload','forward.png','forward.png',JText::_('Next'), false);
        //JToolBarHelper::save('save_image');
        //JToolBarHelper::cancel();
        //JToolBarHelper::back();
        JToolBarHelper::spacer();
        JToolBarHelper::help('screen.rsgallery2',true);
        
        }
    
    function image_upload()
        {
		JToolBarHelper::title( JText::_('Upload'), 'generic.png' );
        JToolBarHelper::custom('upload','upload_f2.png','upload_f2.png',JText::_('Upload'), false);
        //JToolBarHelper::save('upload');
		JToolBarHelper::custom('upload','forward.png','forward.png',JText::_('Next'), false);
        
        }
    
    function images_show()
        {
        
        JToolBarHelper::addNew('forward');
        JToolBarHelper::editList('edit_image');
        JToolBarHelper::deleteList( '', 'delete_image', JText::_('Delete') );
        //menuRSGallery::adminTasksMenu();
        }
        
    function config_rawEdit(){
        JToolBarHelper::title( JText::_('Configuration Raw Edit'), 'generic.png' );
        JToolBarHelper::apply('config_rawEdit_apply');
        JToolBarHelper::save('config_rawEdit_save');
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        
    }
    
    function config_dumpVars(){
        JToolBarHelper::title( JText::_('Configuration Variables'), 'generic.png' );
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        
    }
    
    function config_show()
        {
        JToolBarHelper::title( JText::_('Configuration'), 'generic.png' );
        JToolBarHelper::apply('applyConfig');
        JToolBarHelper::save('saveConfig');
        JToolBarHelper::cancel();
        JToolBarHelper::help('screen.rsgallery2',true);
        //menuRSGallery::adminTasksMenu();
        }
	function edit_main(){
		
		JToolBarHelper::save( 'save_main' );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		
	}
	function edit_thumbs(){
		
		JToolBarHelper::save( 'save_thumbs' );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		
	}
	function edit_display(){
		
		JToolBarHelper::save( 'save_display' );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('templates');
		
	}
    function simple(){
        JToolBarHelper::title( JText::_('Control Panel'), 'generic.png' );
        JToolBarHelper::help('screen.rsgallery2', true);
        //menuRSGallery::adminTasksMenu();
    }
} 
class menu_rsg2_jumploader {
	function show() {
		JToolBarHelper::title( JText::_('Java Uploader'), 'generic.png' );
    JToolBarHelper::apply('');
    JToolBarHelper::save('');
    JToolBarHelper::cancel();
    JToolBarHelper::help('screen.rsgallery2',true);
	}
	
	function simple() {
		JToolBarHelper::title( JText::_('Java Uploader'), 'generic.png' );
		JToolBarHelper::help('screen.rsgallery2',true);
	}
}
?>