<?php
/**
* This file contains the install class for RSGallery2
* @version $Id: install.class.php 1042 2011-09-19 10:48:06Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

global $rsgConfig;
if( !isset( $rsgConfig )){
    
    require_once( JPATH_ADMINISTRATOR . "/components/com_rsgallery2/includes/config.class.php" );
    require_once( JPATH_ADMINISTRATOR . "/components/com_rsgallery2/includes/version.rsgallery2.php" );

    $rsgVersion = new rsgalleryVersion();
    $rsgConfig = new rsgConfig( false );

    // report all errors if in debug mode
    if($rsgConfig->get('debug'))
        error_reporting(E_ALL);
}

/**
* Install class
* @package RSGallery2
* @author Ronald Smit <webmaster@rsdev.nl>
*/
class rsgInstall {
    /** @var string RSGallery base directory */
    var $galleryDir;
    /** @var string Directory to hold original image */
    var $dirOriginal;
    /** @var string Directory to hold thumbnail */
    var $dirThumbs;
    /** @var string Directory to hold display image */
    var $dirDisplay;
	/** @var string Directory to hold watermarked image */
	var $dirWatermarked;
	/** @var array Table list of RSGallery2 */
    var $tablelistNew;
    /** @var array Table list of old RSGallery versions */
    var $tablelistOld;
    /** @var array List migrator class instances */
    var $galleryList;
    /** @var array List of allowed image formats */
    var $allowedExt;
    
    /** Constructor */
    function rsgInstall(){
		global $rsgConfig, $mainframe;
        $app =JFactory::getApplication();
		
		if (!defined("JURI_SITE")){
			define('JURI_SITE', $app->isSite() ? JURI::base() : JURI::root());
		}
		
        $this->galleryDir   = '/images/rsgallery';
        $this->dirOriginal  = '/images/rsgallery/original';
        $this->dirThumbs    = '/images/rsgallery/thumb';
        $this->dirDisplay   = '/images/rsgallery/display';
		$this->dirWatermarked  = '/images/rsgallery/watermarked';
		
        $this->tablelistNew = array('#__rsgallery2_galleries','#__rsgallery2_files','#__rsgallery2_comments','#__rsgallery2_config', '#__rsgallery2_acl');
        $this->tablelistOld = array('#__rsgallery','#__rsgalleryfiles','#__rsgallery_comments','');

        //TODO: this should use the master list in imgUtils
        $this->allowedExt   = array("jpg","gif","png");

        // initialize migrators here
        $this->galleryList  = array(
            new migrate_com_akogallery,
            new migrate_com_zoom_251_RC4,
            new migrate_com_ponygallery_ml_241,
            new migrate_com_easygallery_10B5
        );

        if( $rsgConfig->get( 'debug' )){
            $this->galleryList[] = new testMigrator;
            $this->galleryList[] = new testMigratorFail;
        }
        
    }
    /** For debug purposes only */
    function echo_values(){
    echo JText::_('Thumbdirectory is:').$this->dirThumbs;
    }
    /**
     * Changes Menu icon in backend to RSGallery2 logo
     */
    function changeMenuIcon() {
    $database =& JFactory::getDBO();
	$database->setQuery("UPDATE #__components SET admin_menu_img='../administrator/components/com_rsgallery2/images/rsg2_menu.png' WHERE admin_menu_link='option=com_rsgallery2'");
	if ($database->query())
        {
        $this->writeInstallMsg(JText::_('Menu Image RSGallery2 succesfully changed'), 'ok');
        }
    else
        {
        $this->writeInstallMsg(JText::_('Menu image could not be changed'), 'error');
        }
    }
    
    /** 
     * Creates the default gallery directory structure
     */
    function createDirStructure() {
        
        $dirs = array($this->galleryDir, $this->dirOriginal, $this->dirThumbs, $this->dirDisplay, $this->dirWatermarked);
        $count = 0;
        
        foreach ($dirs as $dir) {
        if (file_exists(JPATH_SITE.$dir) && is_dir(JPATH_SITE.$dir))
            {
            // Dir already exists, next
            $this->writeInstallMsg("<strong>$dir</strong> ".JText::_('already exists'),"ok");
            }
        else
            {
            if(@mkdir(JPATH_SITE.$dir, 0777))
                {
                $this->writeInstallMsg("<strong>$dir</strong> ".JText::_('is created'),"ok");
                $count++;
                }
            else
                {
                $this->writeInstallMsg("<strong>$dir</strong>".JText::_('could not be created'),"error");
                }
            }
        }
    }

    /**
        DEPRECIATED: use GenericMigrator:: instead
    **/
    function createTableStructure(){
        $result = $this->populate_db();

        if( count( $result ) == 0 ){
            $this->writeInstallMsg(JText::_('Database tables created succesfully'),"ok");
            return true;
        }
        else{
            foreach( $result as $e )
                $this->writeInstallMsg( $e, "error" );
            return true;
        }
    }

    /**
        ripped from joomla core: /installation/install2.php
        DEPRECIATED: use GenericMigrator:: instead
    * @param object database object
    * @param string File name
    * @return array containing errors
    */
    function populate_db( $sqlfile='rsgallery2.sql') {
        $database =& JFactory::getDBO();
		
        $sqlDir = JPATH_ADMINISTRATOR . "/components/com_rsgallery2/sql/";
        $errors = array();
    
        $query = fread( fopen( $sqlDir . $sqlfile, 'r' ), filesize( $sqlDir . $sqlfile ) );
        $pieces  = GenericMigrator::split_sql($query);
    
        for ($i=0; $i<count($pieces); $i++) {
            $pieces[$i] = trim($pieces[$i]);
            if(!empty($pieces[$i]) && $pieces[$i] != "#") {
                $database->setQuery( $pieces[$i] );
                if (!$database->query()) {
                    $errors[] = array ( $database->getErrorMsg(), $pieces[$i] );
                }
            }
        }
        return $errors;
    }
    
    /**
        ripped from joomla core: /installation/install2.php
        DEPRECIATED: use GenericMigrator:: instead
    * @param string
    */
    function split_sqlX($sql) {
        $sql = trim($sql);
        $sql = ereg_replace("\n#[^\n]*\n", "\n", $sql);
    
        $buffer = array();
        $ret = array();
        $in_string = false;
    
        for($i=0; $i<strlen($sql)-1; $i++) {
            if($sql[$i] == ";" && !$in_string) {
                $ret[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }
    
            if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            }
            elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $sql[$i];
            }
            if(isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $sql[$i];
        }
    
        if(!empty($sql)) {
            $ret[] = $sql;
        }
        return($ret);
    }
    
    /**
     * Reads the content of the source directory and creates images in the specified directory
     * !!!!!!!!!!!!!!!! OBSOLETE, WILL BE REMOVED SHORTLY !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * @param string Source directory
     * @param string Type of image (display, thumbs)
     * @todo Do a check on allowed filetypes, so only gif, jpeg and png are fed to the image convertor
     */
    function createImages($dir, $type = "display") {
    global $rsgConfig;
    /** 
     * Set timelimit to avoid time out errors due to restrictions 
     * in php.ini's 'max_execution_time' which defaults to 30 in
     * most installations.
     */
    switch ($type) {
    case "thumbs":
        $tdir    = $this->dirThumbs;
        $width  = $rsgConfig->get("thumb_width");
        break;
    default:
    case "display":
        $tdir    = $this->dirDisplay;
        $width  = $rsgConfig->get("image_width");
        break;
    }

    set_time_limit(120);
    $count = 0;
    if (is_dir($dir))
        {
        if ($handle = opendir($dir))
            {
            while (($filename = readdir($handle)) !== false)
                {
                if (!is_dir($dir.$filename) && $filename !== "." && $filename !== ".." && $filename !== "Thumbs.db")
                    {
                    if(imgUtils::resizeImage($dir."/".$filename, JPATH_SITE.$this->dirDisplay."/".$filename, $rsgConfig->get('image_width')))
                        {
                        continue;
                        }
                    else
                        {
                        $count++;
                        }
                    }
                }
            closedir($handle);
            }
        }
    if ($count > 0)
        return false;
    else
        return true;
    }
    
    /**
     * Copies everything from directory $source to directory $target and sets up permissions
     * 
     * @param string Source directory
     * @param string Destination directory
     * @param int chmod wanted (e.g. 0777)
     * @param boolean Subdirectory copying yes or no
     * @return boolean true on success, false on failure
     */
    function copyFiles($source, $target, $chmod=0777, $subdir=false){
    $errorcount = 0;
    $exceptions = array('.','..');
    /** 
     * Set timelimit to avoid time out errors due to restrictions 
     * in php.ini's 'max_execution_time' which defaults to 30 in
     * most installations.
     */
    set_time_limit(0);
    //* Processing
    $handle = opendir($source);
    while (false!==($item=readdir($handle)))
        if (!in_array($item,$exceptions))
            {
            /** cleanup for trailing slashes in directories destinations */
            $from    = str_replace('//','/',$source.'/'.$item);
            $to      = str_replace('//','/',$target.'/'.$item);
            if (is_file($from))
                {
                if (@copy($from,$to))
                    {
                    chmod($to,$chmod);
                    touch($to,filemtime($from)); // to track last modified time
						$messages[]=JText::_('INSTALL_FILE_COPIED_FROM').$from.JText::_('INSTALL_FILE_COPIED_TO').$to;
                    }
                else
                    {
                    $errors[]=JText::_('cannot copy file from ').$from.JText::_('INSTALL_FILE_COPIED_TO').$to;
                    $errorcount++;
                    }
                }
            if (is_dir($from))
                {
                if($subdir)
                    {
                    if (@mkdir($to))
                        {
                        chmod($to,$chmod);
                        $messages[]=JText::_('Directory created: ').$to;
                        }
                    else
                        {
                        $errors[]=JText::_('cannot create directory ').$to;
                        $errorcount++;
                        }
                    $this->copyFiles($from, $to, $chmod, $subdir);
                    }
                }
            }
    closedir($handle);
    if ($errorcount > 0)
        return false;
    else
        return true;
    }
     
    /**
     * Function will recursively delete all directories and files in them, including subdirectories
     *
     * @param string $target Directory to delete
     * @param array $exceptions Array of files to exclude from the delete
     * @param boolean $output Status message for every file True or False
     * @return boolean True or False
     */
    function deleteGalleryDir($target, $exceptions, $output=false) {
    
    if (file_exists($target) && is_dir($target))
        {
        $sourcedir = opendir($target);
        while(false !== ($filename = readdir($sourcedir)))
            {
            if(!in_array($filename, $exceptions))
                {
                if($output)
                    {
                    echo JText::_('Processing: ').$target."/".$filename."<br>";
                    }
                if(is_dir($target."/".$filename))
                    {
                    // recurse subdirectory; call of function recursive
                    $this->deleteGalleryDir($target."/".$filename, $exceptions);
                    }
                else if(is_file($target."/".$filename))
                    {
                    // unlink file
                    unlink($target."/".$filename);
                    }
                }
            }
        closedir($sourcedir);
        if(rmdir($target))
            {
            //return 0;
            $this->writeInstallMsg(JText::_('Directory structure deleted'),"ok");
            }
        else
            {
            //return 1;
            $this->writeInstallMsg(JText::_('Deleting old directory structure failed'),"error");
            }
        }
    else
        {
        //return 2;
        $this->writeInstallMsg(JText::_("No old directory structure found continue"),"ok");
        }
    }
    
    /**
     * NEEDS A REWRITE, DOES NOT FUNCTION PROPERLY
     * Function tries to set the correct permissions on a directory
     * @param string Directory to change permissions on
     * @param int Warning number
     * @todo Rewrite this to function properly. Error trapping is different in class now
     */
    function setDirPermsOnGallery($dir, &$warning_num)
        {
        global $ftpIsAvailable, $ftpUse;
        if(file_exists($dir))
            {
            if(is_dir($dir))
                {
                // check for correct permissions on the dir
                @chmod($dir, 0777);
                if((fileperms($dir) & 0777) != 0777)
                    {
                    // can't change file perms, so see if
                    // safemode patch installed and try it thru
                    // ftp assist
                    if(isset($ftpIsAvailable) && $ftpIsAvailable && $ftpUse && function_exists('chmodDir'))
                        chmodDir($dir, '777');
                    }
                if(fileperms($dir) & 0777 != 0777)
                    {
                    // issue warning about not being able to change gallery dir perms to 777
                    // this may or may NOT be a problem.  Let user decide.
                    $warning_num = 2;
                    return false;
                    }
                else
                    return true;
                }
            else
                {
                // existing gallery is a file rather than a directory
                // needs to be corrected by user first
                $warning_num = 1;
                return false;
                }
            }
            return true;
        }

    /**
     * Functions checks permissions on directories and returns status messages
     * @param string Directoy path to checked dir
     * @return boolean
     * @todo Rewrite this. Does not make sense now
     */
    function checkDirPerms($dir)
        {
        global $warning;
        if(!is_dir($dir))
            {
            //
            $this->writeInstallMSg("<strong>$dir</strong>".JText::_('PERMS_NOT_EXIST'),"error");
            }
        elseif(is_dir($dir) && (fileperms($dir) & 0777) != 0777)
            {
            $this->writeInstallMsg("<strong>$dir</strong>".JText::_('PERMS_NOT_SET').decoct(fileperms($dir)).JText::_('.<br />Please try to correct these permissions though FTP.'),"error");
            }
        else
            {
            $this->writeInstallMsg("<strong>$dir</strong>".JText::_(' was found, permissions are OK.'),"ok");
            }
        }
    
    /**
     * Checks if component is installed
     * @param Component name (eg 'com_rsgallery2')
     * @return True or False
     */
    function componentInstalled($component){
    $database =& JFactory::getDBO();
    $sql = "SELECT COUNT(1) FROM #__components as a WHERE a.option = '$component'";
    $database->setQuery($sql);
    $result = $database->loadResult($sql);
    
    if ($result > 0)
        {
        return true;
        }
    else
        {
        return false;
        }
    }

    /**
     * Writes an installation status message 
     * @param string Message to write
     * @param string Type of message (ok,error)
     */
     function writeInstallMsg($msg, $type = NULL)
        {
        global $mainframe;
        if ($type == "ok")
            {
            $icon = "tick.png";
            }
        elseif ($type == "error")
            {
            $icon = "publish_x.png";
            }
        else
            {
            $icon = "downarrow.png";
            }
        ?>
        <div align="center">
        <table width="500"><tr><td>
        <table class="adminlist" border="1">
        <tr>
            <td width="40"><img src="<?php echo JURI_SITE;?>/administrator/images/<?php echo $icon;?>" alt="" border="0"></td>
            <?php if( $type=='error' ): ?>
                <td><pre><?php print_r( $msg );?></pre></td>
            <?php else: ?>
                <td><?php echo $msg;?></td>
            <?php endif; ?>
        </tr>
        </table>
        </td></tr></table>
        </div>
        <?php
        }
        
     /**
      * Shows the "Installation complete" box with a link to the controlpanel
      */
     function installComplete($msg = null){
     global $mainframe;
	
		if($msg == null) $msg = JText::_('Installation of RSGallery is completed');
     ?>
     <div align="center">
        <table width="500"><tr><td>
        <table class="adminlist" border="1">
        <tr>
            <td colspan="2">
                <div align="center">
                <h2><?php echo $msg; ?></h2> 
                <?php echo JText::_('INSTALL_STATUS_MSGS')?>
                <br>
                
                <a href="index2.php?option=com_rsgallery2">
                    <img align="absmiddle" src="<?php echo JURI_SITE;?>/administrator/images/cpanel.png" alt="" width="48" height="48" border="0">&nbsp;
                    <h2><?php echo JText::_('Control Panel') ?></h2>
                </a>
                </div>
            </td>
        </tr>
        </table>
        </td></tr></table>
        </div>
        <?php
     }
    /**
     * Deletes table from database if it exists
     * 
     * @param string Tablename to delete
     */
    function deleteTable($table)
        {
        $database =& JFactory::getDBO();
        $sql = "DROP TABLE IF EXISTS `$table`";
        $database->setQuery($sql);
        if ($database->query())
            {
            $this->writeInstallMsg("<strong>$table</strong> ".JText::_('is deleted'),"ok");
            }
        else
            {
            $this->writeInstallMsg("<strong>$table</strong> ".JText::_('could not be deleted Delete manually'),"error");
            }
        }
        
    /**
     * Performs exactly the same as the PHP5 function array_combine()
     */
    function array_combine_emulated($keys, $vals) {
        $keys = array_values( (array) $keys );
        $vals = array_values( (array) $vals );
        $n = max( count( $keys ), count( $vals ) );
        $r = array();
        for( $i=0; $i<$n; $i++ ) {
            $r[ $keys[ $i ] ] = $vals[ $i ];
            }
        return $r;
    }
    
    /**
     * Returns the highest value for autoincrement id in table
     * @param string Tablename
     * @param integer Autoincrement ID for the table
     * @return integer Highest value for ID in table
     */
    function maxId($tablename = "#__rsgallery2_cats", $id = "id") {
        $database =& JFactory::getDBO();
        $sql = "SELECT MAX($id) FROM $tablename";
        $database->setQuery($sql);
        $max_id = $database->loadResult();
        return $max_id;
    }
    /**
     * Migrates file information of other gallery systems to RSGallery2
     * 
     * @param string Old category tablename
     * @param string Old image name
     * @param string Old image filename
     * @param timestamp Old image date
     * @param string Old description
     * @param integer Old User ID
     * @param integer Old category ID
     * @param integer Highest value in new table
     */
    function migrateOldFilesX($old_table, $old_image_name, $old_image_filename, $old_image_date, $old_description, $old_uid, $old_catid, $max_id) {
		$database = JFactory::getDBO();
	    $error = 0;
	    $file = 0;
	    $sql = "SELECT * FROM $old_table";
	    $database->setQuery($sql);
	    $old = $database->loadObjectList();
	    foreach ($old as $row)
	        {
	        $filename   = $row->$old_image_filename;
	        $imagename  = $row->$old_image_name;
	        $date       = $row->$old_image_date;
	        $descr      = $row->$old_description;
	        $uid        = $row->$old_uid;
	        $catid      = $row->$old_catid + $max_id;
	        $sql2 = "INSERT INTO #__rsgallery2_files ".
	                "(name, descr, title, date, userid, gallery_id) VALUES ".
	                "('$filename', '$descr', '$imagename', '$date', '$uid', '$catid')";
	        $database->setQuery($sql2);
	        if (!$database->query()) {
	            $error++;
	        } else {
	            $file++;
	        }
		}
	    $total = $error + $file;
	    if ($error > 0) {
	        $this->writeInstallMsg(JText::_('MIGRATE_NOT_ALL')."<strong>$file</strong>".JText::_(' out of ')."<strong>$total</strong>".JText::_(' entries processed)'),"error");
		} else{
	        $this->writeInstallMsg(JText::_('All file information migrated to RSGallery2 database(')."<strong>$file</strong>".JText::_(' entries processed)'),"ok");
		}
	}
    /**
     * Function migrates category information of other gallery systems to RSGallery2
     *
     * @param string Old category tablename
     * @param string Old ID field name
     * @param string Old Category field name
     * @param string Old Parent ID field name
     * @param string Old Description field name
     */
    function migrateOldCatsX($old_table, $old_catid = "id", $old_catname = "catname", $old_parent_id = "parent_id", $old_descr_name = "description", $max_id) {

    }
    
    function migrateOldCommentsX($old_table = "#__zoom_comments", $old_comment = "cmtcontent", $old_img_id = "imgid") {
		$database = JFactory::getDBO();
    }
    
    function migrateFromZoomX() {
    global $mosConfig_absolute_path;
    
    if ($this->componentInstalled("com_zoom"))
        {
        include_once(JPATH_SITE."/components/com_zoom/etc/zoom_config.php");
        //First check if the right version is installed
        if ($zoomConfig['version'] == "2.5.1 RC1" OR $zoomConfig['version'] == "2.5.1 RC2")
            {
            $basedir = JPATH_SITE."/".$zoomConfig['imagepath'];
            $this->writeInstallMsg("OK, right version (".$zoomConfig['version'].") is installed. Let's migrate!","ok");
            $max_id = $this->maxId();
            $this->createTableStructure();
            $this->migrateOldCats("#__zoom", "catid", "catname", "subcat_id", "catdescr", $max_id);
            $this->migrateOldFiles("#__zoomfiles", "imgname", "imgfilename", "imgdate", "imgdescr", "uid", "catid", $max_id);
            //$this->migrateOldComments();//Obsolete for now
            $this->createDirStructure();
            if ($this->copyZoomImages($basedir))
                {
                $this->writeInstallMsg(JText::_('Files succesfully copied to new structure'),"ok");
                }
            else
                {
                $this->writeInstallMsg(JText::_('There were errors copying files to the new structure'),"error");
                }
            $this->installComplete(JText::_('migration of Zoom Gallery completed. Goto the Control Panel.'));
            }
        else
            {
            //Wrong version, we only migrate from Zoom 2.5.1 RC1
            }
        }
    }

    function upgradeInstallX() {
    global $rsgConfig, $database, $mosConfig_absolute_path;
        $imagepath_old = $mosConfig_absolute_path."/images/gallery";
        /**
         * 0. We assume:
         *    ----------
         * -  RSGallery 2.0 beta 5 or RSGallery2 beta1 was previously installed, no modifications to the tables are made
         * -  Old component is still installed
         *    dir structure, images and database tables are still intact.
         */
        echo "<h3>".JText::_('Upgrade from RSGallery')."</h3>";
        /**
         * 1. Check if old component is installed
         */
        if ($this->ComponentInstalled("com_rsgallery"))
            {
            //Yes, component is installed
            $config_file = JPATH_ADMINISTRATOR . "/components/com_rsgallery2/language/english.php";
            if (file_exists($config_file))
                {
                //Supress notices on duplicate definitions with @, as we loaded the new english.php already
                @include_once( JPATH_ADMINISTRATOR . "/components/com_rsgallery2/language/english.php");
                $version = _RSGALLERY_VERSION;
                }
            else
                {
                //Well, component is installed, but no version information can be established
                $mainframe->redirect("index2.php?option=com_rsgallery2&task=install",JText::_('UPGRADE_REC_FULL'));
                }
            /**
             * 2. Then we need to create the new directory structure.
             */
            $this->createDirStructure();
                
            /**
             * 3a. Full pics need to move to /images/rsgallery/original
             */
            if ($this->copyFiles($imagepath_old,JPATH_SITE.$this->dirOriginal,0777,false))
                {
                $this->writeInstallMsg(JText::_('Original files succesfully transfered'),"ok");
                }
            else
                {
                $this->writeInstallMsg(JText::_('UPGRADE_FILES_TRANF_ERROR'),"error");
                }
                
            /**
             * 3b. Thumbs need to move to /images/rsgallery/thumb
             */
            if ($this->copyFiles($imagepath_old."/thumbs",JPATH_SITE.$this->dirThumbs,0777,false))
                {
                $this->writeInstallMsg(JText::_('Thumb files succesfully transfered'),"ok");
                }
            else
                {
                $this->writeInstallMsg(JText::_('UPGRADE_THUMB_TRANF_ERROR'),"error");
                }
            
            /**
             * 4. Display images need to be generated
             */
            if($this->createImages(JPATH_SITE.$this->dirOriginal, "display"))
                {
                $this->writeInstallMsg(JText::_('Display images created succesfully.'),"ok");
                }
            else
                {
                $this->writeInstallMsg(JText::_('UPGRADE_DISP_CREATE_ERROR'),"error");
                }

            if ($version == "RSGallery 2.0 beta 5")
                {
                /**
                 * 5. Then we need to alter the database tables to reflect RSGallery2 structure
                 */
                //Alter database
                $sql0 = "ALTER TABLE #__rsgallery ".
                    "CHANGE `id` `id` int(9) unsigned NOT NULL auto_increment, ".
                    "CHANGE `catname` `catname` varchar(50) DEFAULT '0', ".
                    "CHANGE `description` `description` TEXT DEFAULT NULL, ".
                    "CHANGE `published` `published` tinyint(1) unsigned NOT NULL DEFAULT '1', ".
                    "ADD `uid` int(11) unsigned NOT NULL DEFAULT '0', ".
                    "ADD `allowed` varchar(100) NOT NULL DEFAULT '0', ".
                    "ADD `show_full_description` int(1) NOT NULL DEFAULT '0', ".
                    "ADD `parent` int(9) NOT NULL DEFAULT '0', ".
                    "ADD `user` tinyint(4) NOT NULL DEFAULT '0'";
                    
                $sql1 = "ALTER TABLE #__rsgalleryfiles ".
                    "CHANGE `id` `id` int(9) unsigned NOT NULL auto_increment, ".
                    "CHANGE `name` `name` varchar(100) NOT NULL DEFAULT '', ".
                    "CHANGE `descr` `descr` TEXT DEFAULT NULL, ".
                    "CHANGE `gallery_id` `gallery_id` int(9) unsigned NOT NULL DEFAULT '0', ".
                    "CHANGE `ordering` `ordering` int(9) unsigned NOT NULL DEFAULT '0', ". 
                    "ADD `approved` tinyint(1) unsigned NOT NULL DEFAULT '0', ".
                    "ADD `userid` int(10) NOT NULL, ".
                    "ADD UNIQUE KEY `UK_name` (`name`)";
                    
                $sql2 = "ALTER TABLE #__rsgallery_comments ".
                    "CHANGE `id` `id` int(9) unsigned NOT NULL auto_increment, ".
                    "CHANGE `comment` `comment` TEXT NOT NULL";
                    
                $sql3 = "CREATE TABLE IF NOT EXISTS `#__rsgallery2_config` (".
                    " `id` int(9) unsigned NOT NULL auto_increment,".
                    " `name` text NOT NULL,".
                    " `value` text NOT NULL,".
                    " PRIMARY KEY  (`id`),".
                    " KEY `id` (`id`)".
                    ") ENGINE=MyISAM;";
                    
                for ($i = 0;$i <= 3;$i++)
                    {
                    $sql = "sql".$i;
                    $database->setQuery($$sql);
                    if ($database->query())
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelist[$i]."</strong>".JText::_(' is succesfully altered'),"OK");
                        }
                    else
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelist[$i]."</strong>".JText::_(' is NOT succesfully altered'),"error");
                        }
                    }
                    
                $sql4 = "RENAME TABLE #__rsgallery TO #__rsgallery2_cats";
                $sql5 = "RENAME TABLE #__rsgalleryfiles TO #__rsgallery2_files";
                $sql6 = "RENAME TABLE #__rsgallery_comments TO #__rsgallery2_comments";
                for ($i = 4;$i <= 6;$i++)
                    {
                    $sql = "sql".$i;
                    $database->setQuery($$sql);
                    if ($database->query())
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelist[$i]."</strong>".JText::_(' is succesfully renamed'),"OK");
                        }
                    else
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelist[$i]."</strong>".JText::_(' is NOT renamed'),"error");
                        }
                    }
                /**
                 * 6. Now create dummy tables for a succesfull uninstall of RSGallery 2.0 beta 5
                 */
                 $sql7 = "CREATE TABLE IF NOT EXISTS `#__rsgallery` (i INT NOT NULL)";
                 $sql8 = "CREATE TABLE IF NOT EXISTS `#__rsgalleryfiles` (i INT NOT NULL)";
                 $sql9 = "CREATE TABLE IF NOT EXISTS `#__rsgallery_comments` (i INT NOT NULL)";
                 $x = 0;
                 for ($i = 7;$i <= 9;$i++)
                    {
                    $sql = "sql".$i;
                    $database->setQuery($$sql);
                    if (!$database->query())
                        {
                        $x++;
                        }
                    }
                 if ($x > 0)
                    {
                    $this->writeInstallMsg(JText::_('UPGRADE_DUMMY_ERROR'),"error");
                    }
                }
            elseif ($version == "RSGallery2 beta1")
                {
                /**
                 * 4. Then we need to alter the database tables to reflect RSGallery2 structure
                 */
                 $sql0 = "ALTER TABLE `#__rsgallery` ".
                    "CHANGE `description` `description` TEXT DEFAULT NULL";
                    
                 $sql1 = "ALTER TABLE `#__rsgalleryfiles` ".
                    "CHANGE `descr` `descr` TEXT DEFAULT NULL, ".
                    "ADD `userid` int(10) NOT NULL";
                    
                 $sql2 = "ALTER TABLE `#__rsgallery_comments` ".
                    "CHANGE `comment` `comment` TEXT NOT NULL";
                    
                 $sql3 = "CREATE TABLE IF NOT EXISTS `#__rsgallery2_config` (".
                    " `id` int(9) unsigned NOT NULL auto_increment,".
                    " `name` text NOT NULL,".
                    " `value` text NOT NULL,".
                    " PRIMARY KEY  (`id`),".
                    " KEY `id` (`id`)".
                    ") ENGINE=MyISAM;";
                    
                for ($i = 0;$i <= 3;$i++)
                    {
                    $sql = "sql".$i;
                    $database->setQuery($$sql);
                    if ($database->query())
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelistOld[$i]."</strong>".JText::_(' is succesfully altered'),"OK");
                        }
                    else
                        {
                        $this->writeInstallMsg(JText::_('Table ')."<strong>".$this->tablelistOld[$i]."</strong>".JText::_(' is NOT succesfully altered'),"error");
                        }
                    }
                    
                /**
                 * 6. Now create dummy tables for a succesfull uninstall of RSGallery 2.0 beta 5
                 */
                 $sql7 = "CREATE TABLE IF NOT EXISTS `#__rsgallery` (i INT NOT NULL)";
                 $sql8 = "CREATE TABLE IF NOT EXISTS `#__rsgalleryfiles` (i INT NOT NULL)";
                 $sql9 = "CREATE TABLE IF NOT EXISTS `#__rsgallery_comments` (i INT NOT NULL)";
                 $x = 0;
                 for ($i = 7;$i <= 9;$i++)
                    {
                    $sql = "sql".$i;
                    $database->setQuery($$sql);
                    if (!$database->query())
                        {
                        $x++;
                        }
                    }
                 if ($x > 0)
                    {
                    $this->writeInstallMsg(JText::_('UPGRADE_DUMMY_ERROR'),"error");
                    }
                }
            else
                {
                /** Revert changes, remove new structure and content */
                $exceptions = array(".","..");
                $this->deleteGalleryDir(JPATH_SITE.$this->galleryDir, $exceptions, $output=false);
                //Abort upgrade. Gallery structure present but no version information could be retrieved
                $mainframe->redirect("index2.php?option=com_rsgallery2&task=install",JText::_('UPGRADE_NOT_POSSIBLE'));
                }
            }
        else
            {
            //No, component is not installed
            $mainframe->redirect("index2.php?option=com_rsgallery2&task=install",JText::_('UPGRADE_NOT_POSSIBLE'));
            }
        /**
         * 8. Finally a check if everything went OK (rights, etc)
         * Check needs to be implemented.
         */
    $this->installComplete(JText::_('UPGRADE_SUCCESS'));
    }

    function showInstallOptions(){
        global $mainframe;
        ?>
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td width="500">
                <table class="adminform" width="500">
                <tr>
                    <th><div style=font-size:14px;>Choose your option</div></th>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    <div style=font-size:12px;font-weight:bold;>
                    <img style="float:left;margin:7px;" src="<?php echo JURI_SITE;?>/administrator/images/install.png" alt="" border="0">&nbsp;
                    <a href="index2.php?option=com_rsgallery2&task=install&opt=fresh">Fresh install</a>
                    </div>
                    Installs a complete new RSGallery2. All original images, directories and database entries will be lost. Typical choice for a first install or if you want a completely fresh installation.
                    </td>
                </tr>
                <tr>
                    <td>
                    <div style=font-size:12px;font-weight:bold;>
                    <img style="float:left;margin:7px;" src="<?php echo JURI_SITE;?>/administrator/images/categories.png" alt="" border="0">&nbsp;
                    <a href="index2.php?option=com_rsgallery2&task=install&opt=upgrade">Upgrade</a>
                    </div>
                    Upgrade from RSGallery 2.0 beta 5 only. This upgrade only works if the old database tables are still on the server and the '<strong>gallery</strong>' and '<strong>gallery/thumbs</strong>' directory still exist. If not, choose <a href="index2.php?option=com_rsgallery2&task=install&opt=fresh">Fresh install</a>.
                    </td>
                </tr>
                <tr>
                    <td><div style=font-size:12px;font-weight:bold;>
                    <img style="float:left;margin:7px;" src="<?php echo JURI_SITE;?>/administrator/images/menu.png" alt="" border="0">&nbsp;
                    <a href="index2.php?option=com_rsgallery2&task=install&opt=migration">Migration</a>
                    </div>
                    Migrate your other galleries to RSGallery2. This option will detect any other gallery component in your installation and will offer you the possibility to import the images into the new RSGallery2.<br>(Currently supported are: <strong>Zoom Media Gallery</strong>, <strong>Akogallery</strong> and .......)
                    </td>
                </tr>
                <tr>
                    <td>
                    <div style=font-size:12px;font-weight:bold;>
                    <img style="float:left;margin:15px;" src="<?php echo JURI_SITE;?>/administrator/images/next_f2.png" alt="" border="0">&nbsp;
                    <a href="index2.php?option=com_rsgallery2">Do Nothing</a>
                    </div>
                    Choose this if you are upgrading from a recent RSGallery2 installation.  This option will preserve your existing RSGallery2 galleries and take you to the control panel.  Clicking "Continue" below does same but takes you back to Component Installers.
                    </td>
                </tr>      
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                <tr class="row1">
                    <td>&nbsp;</td>
                </tr>
                </table>
            </td>
            <td width="300">&nbsp;</td>
        </tr>
        </table>
        <?php
    }

    function freshInstall() {
        global $mainframe, $rsgConfig;
		$database =& JFactory::getDBO();
        echo "<h2>Fresh install</h2>";
        //Delete images and directories if exist
        $exceptions = array(".", "..");
        $this->deleteGalleryDir(JPATH_SITE.$this->galleryDir, $exceptions, false);

        //Delete database tables
        foreach ($this->tablelistNew as $table)
            {
            $this->deleteTable($table);
            }
        
        //Create new directories
        $this->createDirStructure();
        
        $this->createTableStructure();

        // save config to populate database with default config values
        $rsgConfig->saveConfig();
        
        //Now wish the user good luck and link to the control panel
        $this->installComplete();
    }
    
    /**
     * Migration scripts are called from here
     *
     */
    function showMigrationOptions() {
        
        $i = 0;

        foreach( $this->galleryList as $component ){
            if( $component->detect() ){
                ?>
                <div align="center">
                <table width="500"><tr><td>
                <table class="adminlist" border="1">
                <tr>
                    <td width="75%"><strong><?php echo $component->getName(); ?></strong> is installed</td>
                    <td><a href="index2.php?option=com_rsgallery2&rsgOption=maintenance&task=doMigration&type=<? echo $component->getTechName(); ?>"><img src="<?php echo JURI_SITE;?>/administrator/images/install.png" alt="" width="24" height="24" border="0" align="middle">&nbsp;Migrate</a></td>
                </tr>
                </table>
                </td></tr></table>
                </div>
                <?php
                $i++;
            }
        }
        if ( $i == 0 ){
            //No migration possibilities
            $this->writeInstallMsg(JText::_('No other gallerysystems installed'),"error");
        }
    }

    /**
     * actually does a migration
     * @param string type of migration
     */
    function doMigration( $type ){
        foreach( $this->galleryList as $gallery ){
            if( $type == $gallery->getTechName() ){
                return $gallery->migrate();
            }
        }
        return "$type".JText::_('is not a valid migration type.');
    }
    
    /**
     * Checks if specified table exists in the system
     * 
     * @param string Tablename
     * @return True or False
     */
    function tableExists($table) {
    global $mosConfig_dbprefix;
	$database =& JFactory::getDBO();
		
    $table = substr($table, 3);
    $sql = "SHOW TABLES LIKE '$mosConfig_dbprefix$table'";
    $database->setQuery($sql);
    if ($database->query())
        $result = $database->getNumRows();
    if ($result > 0)
        {
        return true;
        }
    else
        {
        return false;
        }
    
    }
    /**
     * Returns the extension of a file
     *
     * @param integer Filename
     * @return Extension of filename
     */
    function getExtension($filename){
        $parts = array_reverse(explode(".", $filename));
        $ext = $parts[0];
        return strtolower($ext);
    }
}//End class

/**
* abstract parent class for migrators
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class GenericMigrator{
    /* public functions - should be overridden */
    
    /**
     * Function will return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.
     * It would be advisable to use the class name.  we would just use get_class(), but it's implementation is differs in PHP 4 and 5.
     * @return string Technical name
     */
    function getTechName(){
        return 'GenericMigrator';
    }

    /**
     * @return string containing user friendly name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'GenericMigrator';
    }

    /**
     * detect if the gallery version this class handles is installed
     * @return true or false
     */
    function detect(){
        return false;
    }

    /**
     * do the migration thing
     * @return true on success, anything else a failure
     */
    function migrate(){
        return false;
    }

    /* utility functions */

    /**
    * @param string File name
    * @return bool true if success
    **/
    function handleSqlFile( $sqlfile ){
        $result = $this->runSqlFile( $sqlfile );

        if( count( $result ) == 0 ){
            rsgInstall::writeInstallMsg(JText::_('Database tables created succesfully.'),"ok");
            return true;
        }
        else{
            foreach( $result as $e )
                rsgInstall::writeInstallMsg( $e, "error" );
            return false;
        }
    }

    /**
     * ripped from joomla core: /installation/install2.php:populate_db()
     * @param string File name
     * @return array containing errors
     */
    function runSqlFile( $sqlfile ) {
		$database =& JFactory::getDBO();
        $sqlDir =  JPATH_ADMINISTRATOR . "/components/com_rsgallery2/sql/";

        $errors = array();
    
        $query = fread( fopen( $sqlDir . $sqlfile, 'r' ), filesize( $sqlDir . $sqlfile ) );
        $pieces  = $this->split_sql($query);
    
        for ($i=0; $i<count($pieces); $i++) {
            $pieces[$i] = trim($pieces[$i]);
            if(!empty($pieces[$i]) && $pieces[$i] != "#") {
                $database->setQuery( $pieces[$i] );
                if (!$database->query()) {
                    $errors[] = array ( $database->getErrorMsg(), $pieces[$i] );
                }
            }
        }
        return $errors;
    }
    
    /**
     * ripped from joomla core: /installation/install2.php
     * @param string
     */
    function split_sql($sql) {
        $sql = trim($sql);
        $sql = ereg_replace("\n#[^\n]*\n", "\n", $sql);
    
        $buffer = array();
        $ret = array();
        $in_string = false;
    
        for($i=0; $i<strlen($sql)-1; $i++) {
            if($sql[$i] == ";" && !$in_string) {
                $ret[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }
    
            if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            }
            elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $sql[$i];
            }
            if(isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $sql[$i];
        }
    
        if(!empty($sql)) {
            $ret[] = $sql;
        }
        return($ret);
    }
    
	/**
     * Function migrates gallery information of other gallery systems to RSGallery2
     *
     * @param string Old gallery tablename
     * @param string Old ID field name
     * @param string Old Category field name
     * @param string Old Parent ID field name
     * @param string Old Description field name
     */
	function migrateGalleries($old_table, $old_catid = "id", $old_catname = "catname", $old_parent_id = "parent_id", $old_descr_name = "description", $max_id) {
		$database = JFactory::getDBO();
	    //Set variables
	    $error = 0;
	    $file = 0;
	    
	    //Select all category details from other gallery system
	    $sql = "SELECT $old_catid, $old_catname, $old_parent_id, $old_descr_name FROM $old_table ORDER BY $old_catname ASC";
	    $database->setQuery($sql);
	    $old = $database->loadObjectList();
	    
	    foreach ($old as $row) {
			//Create new category ID
	        $id             = $row->$old_catid + $max_id;
	        $catname        = $row->$old_catname;
	        $description    = $row->$old_descr_name;
			$alias			= $database->getEscaped(JFilterOutput::stringURLSafe($catname));	        
	        if ($row->$old_parent_id == 0) {
	            $parent_id  = 0;
	        } else {
	            $parent_id  = $row->$old_parent_id + $max_id;
	        }
	        
	        //Insert values into RSGallery2 gallery table
	        $sql2 = "INSERT INTO #__rsgallery2_galleries ".
	                "(id, name, parent, description, published, alias) VALUES ".
	                "('$id','$catname','$parent_id','$description', '1', '$alias')";
	        $database->setQuery($sql2);
			//Count errors and migrated files
	        if (!$database->query()) {
	            $error++;
	        } else {
	            $file++;
	        }
		}
		
	    $total = $error + $file;
	    if ($error > 0) {
	        rsgInstall::writeInstallMsg(JText::_('_MIGRATE_NOT_ALL_GAL')."<strong>$file</strong>".JText::_(' out of ')."<strong>$processed</strong>".JText::_(' entries processed)'),"error");
		} else {
	        rsgInstall::writeInstallMsg(JText::_('All gallery information migrated to RSGallery2 database(')."<strong>$file</strong>".JText::_(' entries processed)'),"ok");
	    }
    }
    
	/**
     * Migrates item information of other gallery systems to RSGallery2
     * 
     * @param string Old files tablename
     * @param string Old image name
     * @param string Old image filename
     * @param timestamp Old image date
     * @param string Old description
     * @param integer Old User ID
     * @param integer Old category ID
     * @param integer Highest value in new table
     */
    function migrateItems($old_table, $old_image_name, $old_image_filename, $old_image_date, $old_description, $old_uid, $old_catid, $max_id, $prefix) {
		$database = JFactory::getDBO();
	    //Set variables
	    $error = 0;
	    $file = 0;
	    
	    //GEt all information from images table
	    $sql = "SELECT * FROM $old_table";
	    $database->setQuery($sql);
	    $old = $database->loadObjectList();
	    
	    foreach ($old as $row) {
	        $filename   = $prefix.$row->$old_image_filename;
	        $imagename  = $row->$old_image_name;
	        $date       = $row->$old_image_date;
	        $descr      = $row->$old_description;
	        $uid        = $row->$old_uid;
	        $catid      = $row->$old_catid + $max_id;
			$alias		= $database->getEscaped(JFilterOutput::stringURLSafe($imagename));
	        
	        //Insert data into RSGallery2 files table
	        $sql2 = "INSERT INTO #__rsgallery2_files ".
	                "(name, descr, title, date, userid, gallery_id, alias) VALUES ".
	                "('$filename', '$descr', '$imagename', '$date', '$uid', '$catid', '$alias')";
	        $database->setQuery($sql2);
	        
	        //Error and file counting
	        if (!$database->query()) {
	            $error++;
	        } else {
	            $file++;
	        }
		}
	    $total = $error + $file;
	    if ($error > 0) {
	        rsgInstall::writeInstallMsg(JText::_('MIGRATE_NOT_ALL')."<strong>$file</strong>".JText::_(' out of ')."<strong>$total</strong>".JText::_(' entries processed)'),"error");
		} else{
	        rsgInstall::writeInstallMsg(JText::_('All file information migrated to RSGallery2 database(')."<strong>$file</strong>".JText::_(' entries processed)'),"ok");
		}
	}

	/**
     * Migrates comment information of other gallery systems to RSGallery2
     * 
     * @param string Old commments tablename
     * @param string Old comment text
     * @todo Make this work. As images get new Image ID's this means the comments don't match when migrated.
     */	
	function migrateComments($old_table = "#__zoom_comments", $old_comment = "cmtcontent", $old_img_id = "imgid") {
		$database = JFactory::getDBO();
    	return true;
    }
}//end class

/**
 * test migrator - always succesfull
 * @package RSGallery2
 */
class testMigrator extends GenericMigrator{
    function getTechName(){
        return 'testMigrator';
    }
    function getName(){
        return 'test migrator for debug mode';
    }
    function detect(){
        return true;
    }
    function migrate(){
        return true;
    }
}
/**
 * test migrator - always fails
 * @package RSGallery2
 */
class testMigratorFail extends GenericMigrator{
    function getTechName(){
        return 'testMigratorFail';
    }
    function getName(){
        return 'test migrator for debug mode - always fails';
    }
    function detect(){
        return true;
    }
    function migrate(){
        return "this test migrator always fails.  :-p";
    }
}


/**
* akogallery migrator
* @package RSGallery2
*/
class migrate_com_akogallery extends GenericMigrator{

    var $imgTable =         '#__akogallery';
    var $commentTable =     '#__akogallery_comments';
    var $categoryTable =    '#__categories';
    
    /**
     * @return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.  advisable to use the class name.  we would just use get_class(), but it's implementation differs in PHP 4 and 5.
     */
    function getTechName(){
        return 'com_akogallery';
    }


    /**
     * @return string containing a user friendly name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'AKO Gallery - any version';
    }

    /**
     * detect if the gallery version this class handles is installed
     * @return true or false
     */
    function detect(){
        // if AKO has changed it's storage format over time, we should also check for version
        return rsgInstall::componentInstalled( 'com_akogallery' );
    }

    /**
     * do the migration thing
     * @return true on success, anything else a failure
     */
    function migrate() {
        
        $comconfig =  JPATH_ADMINISTRATOR . "/components/com_akogallery/config.akogallery.php";

        if (! file_exists($comconfig))
            return ( "Config file for AKOGallery does not exist" );
        else
            include_once( $comconfig );

        $imgDir = JPATH_SITE . $ag_pathimages;

        if( !is_dir( $imgDir )) {
            return 'Image Directory does not exist.';
        }

        set_magic_quotes_runtime(1);
        
        $oldnewcats = $this->migrateCategories();
        if( $oldnewcats === false ){
            set_magic_quotes_runtime(0);
            return 'Error migrating Categories';
        }

        if( !$this->migrateImages( $imgDir, $oldnewcats )){
            set_magic_quotes_runtime(0);
            return 'Error migrating images';
        }

        if( !$this->migrateComments() ){
            set_magic_quotes_runtime(0);
            return 'Error migrating Comments';
        }

        set_magic_quotes_runtime(0);
            
        return 'Successful migration';
    }
	/**
	 * add a new category for every category in #__categories that has section set to com_akogallery
     */
    function migrateCategories(){
        
        $database =& JFactory::getDBO();
        $objects = 0;
        $error = 0;
        
        $stringId = "id";
        $stringCatName = "name";
        $stringDesc = "description";
        $stringParentId = "parent_id";
        
        $id = 0; 
        $catname = ''; 
        $desc = ''; 
        $parent_id = 0; 
        $insertSQL= '';
        
        $selectSQL = "SELECT $stringId, $stringCatName, $stringParentId, $stringDesc FROM " . $this->categoryTable . " WHERE section = '" . $this->getTechName() .
            "' ORDER BY $stringCatName ASC";
        $database->setQuery( $selectSQL );
        $AKOCat = $database->loadObjectList();

        // We want to make sure everything works or nothing works... makes it easier to fix and retry
        $database->setQuery( "BEGIN" );

        // contains ids: oldcat => newcat
        $oldnewcats = array();
        
        foreach ( $AKOCat as $oldCat ) {
            $oldnewcats[ $oldCat->$stringId ] = rsgInstall::maxId() + 1;
            $id         = $oldnewcats[ $oldCat->$stringId ];
            $catname    = $oldCat->$stringCatName;
            $desc       = $oldCat->$stringDesc;
    
            if( $oldCat->$stringParentId == 0 )
                $parent_id = 0;
            else
                $parent_id = $oldnewcats[ $oldCat->$stringParentId ];

            $insertSQL = 'INSERT INTO #__rsgallery2_cats ' .
            '( id, catname, parent, description ) VALUES ' .
            "( $id, '$catname', $parent_id, '$desc' )";
            $database->setQuery( $insertSQL );
            
            if( ! $database->query() )
            {
                $error++;
                rsgInstall::writeInstallMsg( "Error importing AKOGallery categories into RSG2 category table. Category Migration rolled back. Please post a bug about this so we can help you with it.
                <br>id = $id
                <br>catname = $catname
                <br>parent = $parent_id
                <br>description = $desc
                <br><br>insertSQL Statement = $insertSQL
                <br><Br>selectSQL statement = $selectSQL
                <br><br>error:" . $database->getErrorMsg(), "error" );
            }
            else
            $objects++;
        }
        if( $error <> 0 )
        {
            $database->setQuery( "ROLLBACK" );
            rsgInstall::writeInstallMsg( "Error importing AKOGallery categories into RSG2 category table. Category Migration rolled back. Please post a bug about this so we can help you with it.", "error" );
        }
        else {
            $database->setQuery( "COMMIT" );
            rsgInstall::writeInstallMsg( "All Category entries successfully imported into RSG2 table. " . $objects . " objects imported", "ok" );
            return $oldnewcats;
        }
    }

    function migrateImages( $imgDir, $oldnewcats ){
        /*
        for every entry in $this->imgTable call imgUtils::importImage() with the info from $this->imgTable, $this->$commentTable and full path to image using $imgDir
        */
        $database =& JFactory::getDBO();
        
        $selectSQL = "SELECT imgfilename, imgtitle, catid FROM $this->imgTable";
        $database->setQuery( $selectSQL );
        $AKOFile = $database->loadObjectList();

        $finalResult = true;
        
        foreach ( $AKOFile as $file ) {
            set_magic_quotes_runtime(0);
            $filePath   = $imgDir . "/" . $file->imgfilename;
            $imgTitle   = $file->imgtitle;
            $catId      = $oldnewcats[ $file->catid ];
            $fileName   = $file->imgfilename;

            $result = imgUtils::importImage( $filePath, $fileName, $catId, $imgTitle );

            if( $result !== true ){
                rsgInstall::writeInstallMsg( $result->toString(), 'error' );
                $finalResult = false;
                return $finalResult;
            }
        }
        return $finalResult;
    }


    function migrateComments() {
        $database =& JFactory::getDBO();
        $error = 0;
        $objects = 0;

        $selectSQL = "SELECT cmtpic, cmtname, cmttext FROM $this->commentTable";
        $database->setQuery( $selectSQL );
        $AKOComment = $database->loadObjectList();
        // Again - We want everything or nothing to work.
        $database->setQuery( "BEGIN" );

        foreach ( $AKOComment as $comment ) {
            $picId      = $comment->cmtpic;
            $name       = $comment->cmtname;
            $commentText= $comment->cmttext;

            $insertSQL = "INSERT INTO #__rsgallery2_comments " .
            "( picid, name, comment ) VALUES " .
            "( $picId, '$name', '$commentText' )";
            $database->setQuery( $insertSQL );

            if( !$database->query() )
            $error++;
            else
            $objects++;

            if( $error <> 0 ) {
                $database->setQuery( "ROLLBACK" );
                rsgInstall::writeInstallMsg( "Error inserting comments. Transaction Cancelled. Please post an error so we can help you with it.", "error" );
            }
            else {
                $database->setQuery( "COMMIT" );
                rsgInstall::writeInstallMsg( "Comments Migrated Successfully. " . $objects . " imported into RSGallery2 comments table", "ok" );
                return true;
            }
        }
    }
}
/**
 * Pony Gallery ML version 2.4.1 migrator
 * @package RSGallery2
 * @author Ronald Smit <ronald.smit@rsdev.nl>
 */
class migrate_com_ponygallery_ml_241 extends genericMigrator {
	/**
     * @return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.  advisable to use the class name.  we would just use get_class(), but it's implementation is differs in PHP 4 and 5.
     */
    function getTechName(){
        return 'com_ponygallery_ml_241';
    }

    
    /**
     * @return string containing a user friendly name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'Pony Gallery ML 2.4.1';
    }

    /**
     * detect if the gallery version this class handles is installed
     * @return true or false
    **/
    function detect(){
        
        if( rsgInstall::componentInstalled( "com_ponygallery" )){
            include_once(JPATH_SITE . "/components/com_ponygallery/language/english.php");
			$version = explode(",", _PONYGALLERY_VERSION);
            if ( $version[0] == "Version 2.4.1" )
            	return true;
        }

        // component not installed or wrong version.
        return false;
    }
/**
 * Copies original images from Pony Gallery to the RSGallery2 file structure
 * and then creates display and thumb images.
 * @param string full path to the original Pony Images
 * @return True id succesfull, false if not
 */
function copyImages($basedir, $prefix = "pony_"){
        global $database, $rsgConfig;
        
        $sql = "SELECT * FROM #__ponygallery";
        $database->setQuery( $sql );
        $result = $database->loadObjectList();
        $i = 0;
        foreach ($result as $image) {
        	$source 		= $basedir . $image->imgfilename;
        	$destination	= JPATH_ORIGINAL . '/' . $prefix.$image->imgfilename;

			//First move image to original folder
        	$newpath = fileUtils::move_uploadedFile_to_orignalDir($source, $destination);
        	if ($newpath) {
        		imgUtils::makeDisplayImage($newpath, '', $rsgConfig->get('image_width'));
        		imgUtils::makeThumbImage($newpath);
        	} else {
        		$i++;
        	}
        }
		if ($i > 0) {
			return false;
		} else {
			return true;
		}
    }
    
    function migrate() {

    	//Set basedir to original images
	    include_once(JPATH_ADMINISTRATOR . "/components/com_ponygallery/config.ponygallery.php");
	    $basedir = JPATH_SITE . $ag_pathoriginalimages . '/';
	    
	    //Set prefix
	    $prefix = "pony_";
	    
	    //Show start message
	    rsgInstall::writeInstallMsg("Start migrating ".$this->getName(),"ok");

	    //Define Max ID in #__rsgallery2_galleries
	    $max_id = rsgInstall::maxId();

	    //Migrate categories to RSGallery2 DB
	    $this->migrateGalleries("#__ponygallery_catg", "cid", "name", "parent", "description", $max_id);
	    
	    //Migrate files into RSGallery2 DB
	    $this->migrateItems("#__ponygallery", "imgtitle", "imgfilename", "imgdate", "imgtext", "imgauthor", "catid", $max_id, $prefix);

	    //Migrate comments into RSGallery2 DB
	    //$this->migrateComments("#__ponygallery_comments", "cmttext", "cmtid");
		
	    if ($this->copyImages($basedir)) {
	        rsgInstall::writeInstallMsg(JText::_('Files succesfully copied to new structure'),"ok");
	    } else {
	        rsgInstall::writeInstallMsg(JText::_('There were errors copying files to the new structure'),"error");
	    }
	    rsgInstall::installComplete("Migration of ".$this->getName()." completed");
	    
    }
}

/**
* Zoom Gallery 2.5.1 RC4 migrator
* @package RSGallery2
* @author Ronald Smit <ronald.smit@rsdev.nl>
*/
class migrate_com_zoom_251_RC4 extends GenericMigrator{

    /**
     * @return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.  advisable to use the class name.  we would just use get_class(), but it's implementation is differs in PHP 4 and 5.
     */
    function getTechName(){
        return 'com_zoom_251_RC4';
    }

    
    /**
     * @return string containing a user friendly name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'ZOOM Gallery 2.5.1 RC4';
    }

    /**
     * detect if the gallery version this class handles is installed
     * @return true or false
    **/
    function detect(){
        
        $comdir =  JPATH_ADMINISTRATOR . "/components/com_zoom";
        
        if( rsgInstall::componentInstalled( "com_zoom" )){
            include_once(JPATH_ADMINISTRATOR . "/components/com_zoom/etc/zoom_config.php");

            if ( $zoomConfig['version'] == "2.5.1 RC4" ) {
            	return true;
            }
        }

        // component not installed or wrong version.
        return false;
    }

    /**
     * do the migration thing
     * @return true on success, anything else a failure
     */
    function migrate(){
	    global $mosConfig_absolute_path;
	    
	    //Set basedir from config file
	    include_once(JPATH_SITE . "/components/com_zoom/zoom_config.php");
	    $basedir = JPATH_SITE . "/" .$zoomConfig['imagepath'];
	    
	    //Set prefix
	    $prefix = "zoom_";
	    
	    //Write version is OK
	    rsgInstall::writeInstallMsg("OK, right version (".$zoomConfig['version'].") is installed. Let's migrate!","ok");
			    
	    //Determine max ID for proper ID transfer to database
	    $max_id = rsgInstall::maxId();
	    
	    //Create RSGallery2 table structure, WHY do this!!!!
	    //$this->createTableStructure();
	    
	    //Migrate categories to RSGallery2 DB
	    $this->migrateGalleries("#__zoom", "catid", "catname", "subcat_id", "catdescr", $max_id);
	    
	    //Migrate files into RSGallery2 DB
	    $this->migrateItems("#__zoomfiles", "imgname", "imgfilename", "imgdate", "imgdescr", "uid", "catid", $max_id, $prefix);
	    
	    //Migrate comments into RSGallery2 DB
	    //$this->migrateComments();//Obsolete for now
	    
		
	    if ($this->copyImages($basedir, $prefix)) {
	        rsgInstall::writeInstallMsg(JText::_('Files succesfully copied to new structure'),"ok");
	    } else {
	        rsgInstall::writeInstallMsg(JText::_('There were errors copying files to the new structure'),"error");
	    }
	    rsgInstall::installComplete("Migration of ".$this->getName()." completed");
	}
	
	function copyImages($basedir, $prefix = "zoom_") {
		global $rsgConfig;
		$database = JFactory::getDBO();
		
		//Set error count
		$i = 0;
		
		//Retrieve image names and folder from database
		$sql = "SELECT * FROM #__zoomfiles as a, #__zoom as b " .
				"WHERE a.catid = b.catid " .
				"ORDER BY a.catid ASC";
		$database->setQuery( $sql );
		$result = $database->loadObjectList();

		//Copy images and create display and thumb
		foreach ($result as $image) {
			$source 		= $basedir . $image->catdir . "/" . $image->imgfilename;
			$destination 	= JPATH_ORIGINAL . "/" . $prefix.$image->imgfilename;

			//First move image to original folder
        	$newpath = fileUtils::move_uploadedFile_to_orignalDir($source, $destination);
        	if ($newpath) {
        		imgUtils::makeDisplayImage($newpath, '', $rsgConfig->get('image_width'));
        		imgUtils::makeThumbImage($newpath);
        	} else {
        		$i++;
        	}
		}
		//Handle errors
		if ($i > 0) {
			return false;
		} else {
			return true;
		}
	}
}

/**
* Easy Gallery migrator
* @package RSGallery2
* @author Ronald Smit <ronald.smit@rsdev.nl>
*/
class migrate_com_easygallery_10B5 extends GenericMigrator{

    /**
     * @return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.  advisable to use the class name.  we would just use get_class(), but it's implementation is differs in PHP 4 and 5.
     */
    function getTechName(){
        return 'com_easygallery_10B5';
    }

    
    /**
     * @return string containing a user friendly name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'Easy Gallery 1.0 beta 5';
    }

    /**
     * detect if the gallery version this class handles is installed
     * @return true or false
    **/
    function detect(){
        global $mosConfig_absolute_path;
        
        if( rsgInstall::componentInstalled( "com_easygallery" )){
			return true;
        } else {
        	// component not installed or wrong version.
        	return false;
        }
    }

	function migrate() {
		$database = JFactory::getDBO();
		//Set basedir from config file
	    include_once(JPATH_ADMINISTRATOR . "/components/com_easygallery/configuration.php");
	    $basedir = JPATH_SITE .$eg_original_path;

	    //Set prefix
	    $prefix = "easy_";

	    //Write version is OK
	    rsgInstall::writeInstallMsg("OK, right version is installed. Let's migrate!","ok");
	    
	    //Determine max ID for proper ID transfer to database
	    $max_id = rsgInstall::maxId();
	    
	    //Create RSGallery2 table structure, WHY do this!!!!
	    //$this->createTableStructure();
	    
	    //Migrate categories to RSGallery2 DB
	    $this->migrateGalleries("#__categories", "id", "title", "parent_id", "description", $max_id);
	    
	    //Migrate files into RSGallery2 DB
	    $this->migrateItems("#__easygallery", "name", "path", "0000-00-00 00:00:00", "description", 0, "cid", $max_id, $prefix);
	    
	    //Migrate comments into RSGallery2 DB
	    //$this->migrateComments();//Obsolete for now
	    
	    if ($this->copyImages($basedir, $prefix)) {
	        rsgInstall::writeInstallMsg(JText::_('Files succesfully copied to new structure'),"ok");
	    } else {
	        rsgInstall::writeInstallMsg(JText::_('There were errors copying files to the new structure'),"error");
	    }
	    rsgInstall::installComplete("Migration of ".$this->getName()." completed");
	}
	
	/**
     * Function migrates gallery information of Easy Gallery to RSGallery2
     * Easy Gallery uses Joomla #__categories table for the storage of category information,
     * so a custom migrateGalleries() function is necessary here.
     * 
     * @param string Old gallery tablename
     * @param string Old ID field name
     * @param string Old Category field name
     * @param string Old Parent ID field name
     * @param string Old Description field name
     */
	function migrateGalleries($old_table, $old_catid = "id", $old_catname = "catname", $old_parent_id = "parent_id", $old_descr_name = "description", $max_id) {
		$database = JFactory::getDBO();
	    //Set variables
	    $error = 0;
	    $file = 0;
	    
	    //Select all category details from other gallery system
	    $sql = "SELECT $old_catid, $old_catname, $old_parent_id, $old_descr_name " .
	    		"FROM $old_table " .
	    		"WHERE section = 'com_easygallery'" .
	    		"ORDER BY $old_catname ASC";
	    $database->setQuery($sql);
	    $old = $database->loadObjectList();
	    
	    foreach ($old as $row) {
			//Create new category ID
	        $id             = $row->$old_catid + $max_id;
	        $catname        = $row->$old_catname;
	        $description    = $row->$old_descr_name;
	        $alias			= $database->getEscaped(JFilterOutput::stringURLSafe($catname));
	        if ($row->$old_parent_id == 0) {
	            $parent_id  = 0;
	        } else {
	            $parent_id  = $row->$old_parent_id + $max_id;
	        }
	        
	        //Insert values into RSGallery2 gallery table
	        $sql2 = "INSERT INTO #__rsgallery2_galleries ".
	                "(id, name, parent, description, published, alias) VALUES ".
	                "('$id','$catname','$parent_id','$description', '1', '$alias')";
	        $database->setQuery($sql2);
			//Count errors and migrated files
	        if (!$database->query()) {
	            $error++;
	        } else {
	            $file++;
	        }
		}
		
	    $total = $error + $file;
	    if ($error > 0) {
	        rsgInstall::writeInstallMsg(JText::_('MIGRATE_NOT_ALL_GAL')."<strong>$file</strong>".JText::_(' out of ')."<strong>$processed</strong>".JText::_(' entries processed)'),"error");
		} else {
	        rsgInstall::writeInstallMsg(JText::_('All gallery information migrated to RSGallery2 database(')."<strong>$file</strong>".JText::_(' entries processed)'),"ok");
	    }
    }
    
    /**
     * Migrates item information of Easy Gallery to RSGallery2
     * Easy Gallery stores the filename, including the path in one field.
     * We need to retrieve the filename, without the path to be able to
     * store the filename in the DB
     * 
     * @param string Old files tablename
     * @param string Old image name
     * @param string Old image filename
     * @param timestamp Old image date
     * @param string Old description
     * @param integer Old User ID
     * @param integer Old category ID
     * @param integer Highest value in new table
     */
    function migrateItems($old_table, $old_image_name, $old_image_filename, $old_image_date, $old_description, $old_uid, $old_catid, $max_id, $prefix) {
		$database = JFactory::getDBO();
	    //Set variables
	    $error = 0;
	    $file = 0;
	    
	    //Get all information from images table
	    $sql = "SELECT * FROM $old_table";
	    $database->setQuery($sql);
	    $old = $database->loadObjectList();
	    
	    foreach ($old as $row) {
	        //Retrieve correct filename, without path information
	        $filename 	= array_reverse( explode("/", $row->$old_image_filename) );
	        $filename   = $prefix.$filename[0];
	        $imagename  = $row->$old_image_name;
	        $date       = $row->$old_image_date;
	        $descr      = $row->$old_description;
	        $uid        = $row->$old_uid;
	        $catid      = $row->$old_catid + $max_id;
	        $alias		= $database->getEscaped(JFilterOutput::stringURLSafe($imagename));
			
	        //Insert data into RSGallery2 files table
	        $sql2 = "INSERT INTO #__rsgallery2_files ".
	                "(name, descr, title, date, userid, gallery_id, alias) VALUES ".
	                "('$filename', '$descr', '$imagename', '$date', '$uid', '$catid', '$alias')";
	        $database->setQuery($sql2);
	        
	        //Error and file counting
	        if (!$database->query()) {
	            $error++;
	        } else {
	            $file++;
	        }
		}
	    $total = $error + $file;
	    if ($error > 0) {
	        rsgInstall::writeInstallMsg(JText::_('MIGRATE_NOT_ALL')."<strong>$file</strong>".JText::_(' out of ')."<strong>$total</strong>".JText::_(' entries processed)'),"error");
		} else{
	        rsgInstall::writeInstallMsg(JText::_('All file information migrated to RSGallery2 database(')."<strong>$file</strong>".JText::_(' entries processed)'),"ok");
		}
	}
	
	/**
	 * Copies original images from Pony Gallery to the RSGallery2 file structure
	 * and then creates display and thumb images.
	 * @param string full path to the original Pony Images
	 * @return True id succesfull, false if not
	 */
	function copyImages($basedir, $prefix = "easy_"){
        global $rsgConfig;
        $database = JFactory::getDBO();
        
        $sql = "SELECT * FROM #__easygallery";
        $database->setQuery( $sql );
        $result = $database->loadObjectList();
        $i = 0;
        foreach ($result as $image) {
        	$source 		= $basedir ."/" . $image->path;
        	$filename = array_reverse( explode("/", $image->path) );
        	$destination	= JPATH_ORIGINAL . "/" . $prefix.$filename[0];

			//First move image to original folder
			
        	$newpath = fileUtils::move_uploadedFile_to_orignalDir($source, $destination);
        	if ($newpath) {
        		imgUtils::makeDisplayImage($newpath, '', $rsgConfig->get('image_width'));
        		imgUtils::makeThumbImage($newpath);
        	} else {
        		$i++;
        	}
        }
		if ($i > 0) {
			return false;
		} else {
			return true;
		}
    }
}

/**
 * rsgallery migrator
 * @package RSGallery2
 */
class migrate_com_rsgallery extends GenericMigrator{
    /* public functions */
    
    /**
     * @return string containing the technical name.  no spaces, special characters, etc allowed as this will be used in GET/POST.  advisable to use the class name.  we would just use get_class(), but it's implementation is differs in PHP 4 and 5.
     */
    function getTechName(){
        return 'com_rsgallery';
    }

    /**
     * @return String containing name and version(s) of which gallery this class migrates
     */
    function getName(){
        return 'RSGallery2 1.10.2+';
    }

	/**
     * detect if the gallery version this class handles is installed
     * @return true or false
     */
function detect(){
		global $mainframe; 
		$database =& JFactory::getDBO();
		
		if( in_array( $database->getPrefix().'rsgallery2_config', $database->getTableList() ) === false ){ 
            // rsgallery2_config table does not exist
            return false;
        } else {
        	// if #__rsgallery2_config exists, then we can handle the upgrade
        	return true;
        }
    }
	/**
	* do the migration thing
	* @return true on success, anything else a failure
	*/
	function migrate(){
		global $rsgConfig;
		$database =& JFactory::getDBO();
		
		// in versions prior to 1.11.0, if the config had never been saved, no variables (including the version) would exist
		// if this is the case, we set the version to something appropiate
		$database->setQuery( "SELECT * FROM #__rsgallery2_config" );
		$database->query();
		if( $database->getNumRows() == 0 )
			$rsgConfig->set( 'version', '1.10.?' );

		// match version numbers.  each update is applied successively until finished.
		// this will happen because there are no break statements

		switch( true ){
			case $this->beforeVersion( '1.11.0' ):
				$this->handleSqlFile( 'upgrade_1.10.14_to_1.11.0.sql' );

			case $this->beforeVersion( '1.11.1' ):
				$this->handleSqlFile( 'upgrade_1.11.0_to_1.11.1.sql' );

			case $this->beforeVersion( '1.11.8' ):
				$this->handleSqlFile( 'upgrade_1.11.7_to_1.11.8.sql' );

			case $this->beforeVersion( '1.11.11' ):
				$this->handleSqlFile( 'upgrade_1.11.10_to_1.11.11.sql' );

			case $this->beforeVersion( '1.12.0' ):
				$this->handleSqlFile( 'upgrade_1.11.11_to_1.12.0.sql' );

			case $this->beforeVersion( '1.12.2' ):
				$this->upgradeTo_1_12_2();

			case $this->beforeVersion( '1.13.2' ):
				$this->handleSqlFile( 'upgrade_1.12.2_to_1.13.2.sql' );

			case $this->beforeVersion( '1.14.0' ):
				$this->handleSqlFile( 'upgrade_1.13.2_to_1.14.0.sql' );
			
			case $this->beforeVersion( '1.14.1' ):
				$this->handleSqlFile( 'upgrade_1.14.0_to_1.14.1.sql' );
			
			case $this->beforeVersion( '2.2.1' ):
				$this->handleSqlFile( 'upgrade_2.2.0_to_2.2.1.sql' );
				$this->upgradeTo_2_2_1();			
			
			default:
				// if we reach this point then everything was a success, update the version number and exit.
				$this->updateVersionNumber();
				return true;
		}
	}

    /**
     * check if installed version is less than (before) $ver
     * @param string version to check against
     * @return true if installed version is less than $ver otherwise false
     */
    function beforeVersion( $ver ){
        global $rsgConfig;

        // version in existing database (the version we are migrating from)
        // get version number as an array, with major, minor and revision numbers being keyed 0, 1, 2 respectively.
        $installedVer = explode( '.', $rsgConfig->get( 'version' ) );

        $ver = explode( '.', $ver );

        // check major versions
        if( $installedVer[0] < $ver[0] )
            return true;
        else if( $installedVer[0] > $ver[0] )
            return false;

        // major versions match, check minor versions
        if( $installedVer[1] < $ver[1] )
            return true;
        else if( $installedVer[1] > $ver[1] )
            return false;

        // minor versions match, check revision
        if( $installedVer[2] < $ver[2] )
            return true;
        else if( $installedVer[2] > $ver[2] )
            return false;

        // version numbers match exactly
        return false;
    }

    /**
     * updates the version number in database to the hardcoded version number
     */
    function updateVersionNumber(){
        global $rsgConfig;

        $rsgConfig->set( 'version', $rsgConfig->getDefault( 'version' ));
        $rsgConfig->saveConfig();
    }

    /** special upgrade handling for various versions is below **/

    /**
     * in some version prior to 1.12.2 #__rsgallery2_acl was hardcoded with the prefix jos.
     * if Joomla! was installed using a different prefix then #__rsgallery2_acl will be missing.
     * @todo this needs to be tested
     */
    function upgradeTo_1_12_2(){
        global $mosConfig_dbprefix;
		$database =& JFactory::getDBO();
		
        if( $mosConfig_dbprefix == 'jos_' )
            return;  // prefix is jos, so it doesn't matter.

        if( in_array( $mosConfig_dbprefix.'rsgallery2_acl', $database->getTableList() ) === false ){
            // #__rsgallery2_acl does not exist

            // first we create the table
            $this->handleSqlFile( 'upgrade_1.12.1_to_1.12.2.sql' );

            // now remove jos_rsgallery2_acl if it does not belong
            // we only want to do this if it is empty and there is no other joomla installed using jos_
            $database->setQuery( "SHOW TABLES LIKE 'jos_content'" );
            $database->query();
            if( $database->getNumRows() == 1 ) return; // joomla using jos_ exists

            $database->setQuery( "SELECT * FROM `jos_rsgallery2_acl`" );
            $database->query();
            if( $database->getNumRows() > 0 ) return; // table not empty, leave it alone

            $database->setQuery( "DROP TABLE `jos_rsgallery2_acl`" );
            $database->query();
        }
    }
	function upgradeTo_2_2_1(){
		//There is a new field 'alias in tables #__rsgallery2_galleries and 
		// #__rsgallery2_files and it needs to be filled as our SEF router uses it
		$error = false;
		$db =& JFactory::getDBO();
		
		//Get id, name for the galleries
		$query = 'SELECT id, name FROM #__rsgallery2_galleries';
		$db->setQuery($query);
		$result = $db->loadAssocList();
		//...and make alias from name
		foreach ($result as $key => $value) {
			jimport( 'joomla.filter.filteroutput' );
			$result[$key]['alias'] = JFilterOutput::stringURLSafe($value['name']);
		}
		//save the alias
		foreach ($result as $key => $value) {
			$query = 'UPDATE #__rsgallery2_galleries '
					.' SET `alias` = '. $db->quote($value['alias'])
					.' WHERE `id` = '. $db->quote($value['id']);
			$db->setQuery($query);
			$result = $db->query();
			if (!$result) {
				$msg = JText::_('COM_RSGALLERY2_MIGRATE_ERROR_FILLING_ALIAS_GALLERY',$value[id], $value[name]);
				JError::raiseNotice( 100, $msg);
				$error = true;
			}
		}
	
		//Get id, title for the items
		$query = 'SELECT id, title FROM #__rsgallery2_files';
		$db->setQuery($query);
		$result = $db->loadAssocList();
		//...and make alias from title
		foreach ($result as $key => $value) {
			jimport( 'joomla.filter.filteroutput' );
			$result[$key]['alias'] = JFilterOutput::stringURLSafe($value['title']);
		}
		//save the alias
		foreach ($result as $key => $value) {
			$query = 'UPDATE #__rsgallery2_files '
					.' SET `alias` = '. $db->quote($value['alias'])
					.' WHERE `id` = '. $db->quote($value['id']);
			$db->setQuery($query);
			$result = $db->query();
			if (!$result) {
				$msg = JText::_('COM_RSGALLERY2_MIGRATE_ERROR_FILLING_ALIAS_ITEM',$value[id], $value[title]);
				JError::raiseNotice( 100, $msg);
				$error = true;
			}
		}
		if ($error) {
			rsgInstall::writeInstallMsg(JText::_('COM_RSGALLERY2_FINISHED_CREATING_ALIASES'), 'error');
		} else {		
			rsgInstall::writeInstallMsg(JText::_('COM_RSGALLERY2_FINISHED_CREATING_ALIASES'), 'ok');
		}
	}
}

/**
 * convert from flat gallery to hierarchical folder sturcture
 * @author John Caprez (john@swizzysoft.com)
 **/
class migrate_com_rsgallery_flat_structure extends GenericMigrator {
// TODO:implement	
}

/**
 * convert from hierarchical gallery to flat folder sturcture
 * @author John Caprez (john@swizzysoft.com)
 **/
class migrate_com_rsgallery_hierarchical_structure extends GenericMigrator {
	// TODO:implement	
}


?>