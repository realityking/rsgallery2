<?php
/**
* This class handles version management for RSGallery2
* @version $Id: version.rsgallery2.php 1043 2011-09-19 13:47:20Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Version information class, based on the Joomla version class
 * @package RSGallery2
 */
class rsgalleryVersion {
	// Version also needs to be changed in config.class.php function rsgConfig
    /** @var string Product */
    var $PRODUCT    = 'RSGallery2';
    /** @var int Main Release Level */
    var $RELEASE    = '2.2';				//Main Release Level: x.y for x.y.z
    /** @var string Development Status */
    var $DEV_STATUS = '';					//as of 2.1.1 'beta' removed
    /** @var int Sub Release Level */
    var $DEV_LEVEL  = '1';					//Dev level z for x.y.z
    /** @var int build Number */
    var $BUILD      = 'SVN 1043';
    /** @var string Codename */
    var $CODENAME   = '';
    /** @var string Date */
    var $RELDATE    = '19 September 2011';
    /** @var string Time */
    var $RELTIME    = '14:00';
    /** @var string Timezone */
    var $RELTZ      = 'UTC';
    /** @var string Copyright Text */
    var $COPYRIGHT  = '&copy; 2005 - 2011 <strong><a class="rsg2-footer" href="http://www.rsgallery2.nl">RSGallery2</a></strong>. All rights reserved.';
    /** @var string URL */
    var $URL        = '<strong><a class="rsg2-footer" href="http://www.rsgallery2.nl">RSGallery2</a></strong>';
    /** @var string Whether site is a production = 1 or demo site = 0: 1 is default */
    var $SITE       = 1;
    /** @var string Whether site has restricted functionality mostly used for demo sites: 0 is default */
    var $RESTRICT   = 0;
    /** @var string Whether site is still in development phase (disables checks for /installation folder) - should be set to 0 for package release: 0 is default */
    var $SVN        = 0;

    /**
     * @return string Long format version
     */
    function getLongVersion() {
        return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
            . $this->DEV_STATUS
            .' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
            . $this->RELTIME .' '. $this->RELTZ;
    }

    /**
     * @return string Short version format
     */
    function getShortVersion() {
        return $this->PRODUCT . ' ' . $this->RELEASE .'.'. $this->DEV_LEVEL .' '.$this->DEV_STATUS . ' - '.$this->BUILD.'<br />'.$this->COPYRIGHT;
    }

    /**
     * @return string PHP standardized version format
     */
    function getVersionOnly() {
        return $this->RELEASE .'.'. $this->DEV_LEVEL;
    }
    
    /**
     * checks if checked version is lower, equal or higher that the current version
     * @return int -1 (lower), 0 (equal) or 1 (higher)
     */
    function checkVersion($version) {
        $check = version_compare($version, $this->RELEASE .'.'. $this->DEV_LEVEL);
        return $check;
    }
	//return svn number
	function getSVNonly() {
		return $this->BUILD;
	}
}
?>
