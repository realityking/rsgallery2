<?php
/**
 * Class handles all configuration parameters for RSGallery2
 * @version $Id: config.class.php 1013 2011-02-07 17:06:58Z mirjam $
 * @package RSGallery2
 * @copyright (C) 2003 - 2011 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Generic Config class
 * @package RSGallery2
 */
class rsgConfig {

	//	General
	var $intro_text 		= '';
	var $version    		= 'depreciated';  // this is set and loaded from includes/version.rsgallery2.php
	var $debug      		= false;
	var $allowedFileTypes 	= "jpg,jpeg,gif,png";
	var $hideRoot			= false; // hide the root gallery and it's listing.  this is to publish multiple independant galleries.
	var $advancedSef		= false; // use category and image name instead of numeric identifiers in url.
	
	// new image paths, use imgUtils::getImg*() instead of calling these directly
	var $imgPath_thumb 		= '/images/rsgallery/thumb';
	var $imgPath_display 	= '/images/rsgallery/display';
	var $imgPath_original 	= '/images/rsgallery/original';
	var $imgPath_watermarked 	= '/images/rsgallery/watermarked';
	var $createImgDirs 		= false;
	var $gallery_folders	= false; // defines if galleries are stored in separate folders

	//Image upload settings
	var $useIPTCinformation = false;
	
	// graphics manipulation
	var $graphicsLib        = 'gd2';   // imagemagick, netbpm, gd1, gd2
	var $keepOriginalImage	= true;
	var $jpegQuality        = '85';
	var $image_width		= '400';  //todo: rename to imgWidth_display
	var $resize_portrait_by_height = true;
    var $thumb_style        = 1; //0 = proportional, 1 = square
	var $thumb_width        = '80';  //todo: rename to imgWidth_thumb
	var $imageMagick_path	= '';
	var $netpbm_path		= '';
	var $ftp_path			= '';

	var $videoConverter_path			= '';
	var $videoConverter_param			= '-i {input} -ar 22050 -ab 56 -b 200 -r 12 -f flv -s 320x240 -acodec mp3 -ac 1 {output}';
	var $videoConverter_thumbParam		= ' -i {input} -f mjpg -vframes 1 -an -s 320x240 {output}';
	var $videoConverter_extension			= 'flv';
	
	// front display
    var $display_thumbs_style = 'table'; // float, table, magic
    var $display_thumbs_floatDirection = 'left'; // left, right
	var $display_thumbs_colsPerPage	= 3;
    var $display_thumbs_maxPerPage = 9;
    var $display_thumbs_showImgName = true;
	var $display_img_dynamicResize	= 5;
    var $displayRandom	            = 1;
	var $displayLatest	            = 1;
	var $displayBranding			= true;
	var $displayDesc		        = 1;
    var $displayHits                = 0;
	var $displayVoting	            = 1;
	var $displayComments	        = 1;
	var $displayEXIF		        = 1;
	var $displaySlideshow 			= 1;
	var $displaySearch				= 1;
	var $current_slideshow			= "slideshow_parth";
	var $displayDownload			= true;
	var $displayPopup				= 1; //0 = Off; 1 = Normal; 2 = Fancy;
	var $displayStatus				= 1;
	var $dispLimitbox				= 1; //0 = never; 1 = If more galleries then limit; 2 = always
	var $galcountNrs				= 5;
	var $template					= 'semantic';
	var $showGalleryOwner			= 1;
	var $showGallerySize			= 1;
	var $showGalleryDate			= 1;
	var $exifTags					= 'FileName|FileDateTime|resolution';
	
	var $filter_order				= 'ordering';
	var $filter_order_Dir			= 'ASC';
	
	/* var $gallery_sort_order			= 'order_id';*/ //'order_id' = ordering by DB ordering field; 'desc' = Last uploaded first; 'asc' = Last uploaded last

    // user uploads
	var $uu_enabled         = 0;
	//var $uu_registeredOnly  = 1;
	var $uu_createCat       = 0;
	var $uu_maxCat          = 5;
	var $uu_maxImages       = 50;
	var $acl_enabled		= 0;
	var $show_mygalleries	= 0;
    
    // watermarking
    var $watermark           = 0;
    var $watermark_type		 = "text"; //Values are text or image
    var $watermark_text      = "(c) 2011 - RSGallery2";
    var $watermark_image	 = "watermark.png";
    var $watermark_angle     = 0;
    var $watermark_position  = 5;
    var $watermark_font_size = 20;
    var $watermark_font		 = "arial.ttf";
    var $watermark_transparency = 50;
    
    // Commenting system
    var $comment						= 1;
    var $comment_security				= 0;
    var $comment_once		 			= 0;
    var $comment_allowed_public			= 1;
    
    //Voting system
    var $voting					= 1;
    var $voting_once			= 1;
    var $cookie_prefix			= "rsgvoting_";

    /**
     * constructor
     * @param bool true loads config from db, false will retain defaults
     * @todo: fix why we can't get the version from $rsgVersion!
     */
    function rsgConfig( $loadFromDB = true ){
        // get version
        // global $rsgVersion;
        // $this->version = $rsgVersion->getVersionOnly();
		// Version needs to be changed here!
        $this->version = '2.2.1';

        if( $loadFromDB )
            $this->_loadConfig();
    }

	/**
	 * @return array An array of the public vars in the class
	 */
	function getPublicVars() {
		$public = array();
		$vars = array_keys( get_class_vars( get_class( $this ) ) );
		sort( $vars );
		foreach ($vars as $v) {
			if ($v{0} != '_') {
				$public[] = $v;
			}
		}
		return $public;
	}

	/**
	 *	binds a named array/hash to this object
	 *	@param array $hash named array
	 *	@return null|string	null is operation was satisfactory, otherwise returns an error
	 */
	function _bind( $array, $ignore='' ) {
		if (!is_array( $array )) {
			$this->_error = strtolower(get_class( $this )).'::bind failed.';
			return false;
		} else {
			return $this->rsgBindArrayToObject( $array, $this, $ignore );
		}
	}
	function rsgBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true )
	{
		if (!is_array( $array ) || !is_object( $obj )) {
			return (false);
		}
		foreach (get_object_vars($obj) as $k => $v)
		{
			if( substr( $k, 0, 1 ) != '_' )
			{
				// internal attributes of an object are ignored
				if (strpos( $ignore, $k) === false)
				{
					if ($prefix) {
						$ak = $prefix . $k;
					} else {
						$ak = $k;
					}
					if (isset($array[$ak])) {
						if ($checkSlashes && get_magic_quotes_gpc()) {
							if (is_string($array[$ak])) {
								//if it is a string, we can use stripslashes e.g. when multiple exifTags are selected is is an array
								$obj->$k = stripslashes( $array[$ak] );
							} else {							
								$obj->$k = $array[$ak];
							}
						} else {
							$obj->$k = $array[$ak];
						}
					}
				}
			}
		}

		return true;
	}


	/**
	 * Binds the global configuration variables to the class properties
	 */
	function _loadConfig() {
		$database =& JFactory::getDBO();

		$query = "SELECT * FROM #__rsgallery2_config";
		$database->setQuery($query);

		if( !$database->query() ){
			// database doesn't exist, use defaults.
			return;
		}

		$vars = $database->loadAssocList();

		foreach ($vars as $v) {
			$this->$v['name'] = $v['value'];
		}
	}

	/**
	 * takes an array, binds it to the class and saves it to the database
	 * @param array of settings
	 * @return false if fail
	 */
	function saveConfig( $config=null ) {
		$db =& JFactory::getDBO();
		
		//bind array to class
		if( $config !== null){
			$this->_bind($config);
			if(array_key_exists('exifTags', $config))
				$this->exifTags = implode("|", $config['exifTags']);
		}
	
		$db->setQuery( "TRUNCATE #__rsgallery2_config" );
		$db->query() or JError::raiseError( $dg->getErrorNum, $db->getErrorMsg() ); 

		$query = "INSERT INTO #__rsgallery2_config ( `name`, `value` ) VALUES ";

		$vars = $this->getPublicVars();
		foreach ( $vars as $name ){
			$query .= "( '$name', '" . addslashes($this->$name) . "' ), ";
		}

		$query = substr( $query, 0, -2 );
		$db->setQuery( $query );
		$db->query() or JError::raiseError( $dg->getErrorNum, $db->getErrorMsg() ); 

		return true;
	}

	/**
	 * @param string name of variable
	 * @return the requested variable
	 */
	function get($varname){
		return $this->$varname;
	}
    
    /**
     * @param string name of variable
     * @param var new value
     */
    function set( $varname, $value ){
        $this->$varname = $value;
    }
    
    /**
     * @param string name of variable
     * @return the default value of requested variable
     */
    function getDefault( $varname ){
        $defaultConfig = new rsgConfig( false );
        return $defaultConfig->get( $varname );
    }

	/**
	 * Taken from ApplicationHelper J1.5 framework
	 * Gets information on a specific client id.  This method will be useful in
	 * future versions when we start mapping applications in the database.
	 *
	 * @access	public
	 * @param	int			$id		A client identifier
	 * @param	boolean		$byName	If True, find the client by it's name
	 * @return	mixed	Object describing the client or false if not known
	 * @since	1.5
	 */
	function getClientInfo($id = null, $byName = false)
	{
		static $clients;
		
		// Only create the array if it does not exist
		if (!is_array($clients))
		{
			$obj = new stdClass();
			
			// Site Client
			$obj->id		= 0;
			$obj->name	= 'site';
			$obj->path	= JPATH_RSGALLERY2_SITE;
			$clients[0] = clone($obj);
			
			// Administrator Client
			$obj->id		= 1;
			$obj->name	= 'administrator';
			$obj->path	= JPATH_RSGALLERY2_ADMIN;
			$clients[1] = clone($obj);
			
		}
		
		//If no client id has been passed return the whole array
		if(is_null($id)) {
			return $clients;
		}
		
		// Are we looking for client information by id or by name?
		if (!$byName)
		{
			if (isset($clients[$id])){
				return $clients[$id];
			}
		}
		else
		{
			foreach ($clients as $client)
			{
				if ($client->name == strtolower($id)) {
					return $client;
				}
			}
		}
		$null = null;
		return $null;
	}
	
}
