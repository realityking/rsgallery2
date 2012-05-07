<?php
/**
* This file contains the main template file for RSGallery2.
* @package RSGallery2
* @copyright (C) 2003 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

/**

ATTENTION!

This is built to imitate the Joomla 1.5.* style of templating.  Hopefully that is enlightening.

**/

defined( '_JEXEC' ) or die( 'Restricted Access' );

// bring in display code
$templatePath = JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . 'semantic';
require_once( $templatePath . DS . 'display.class.php');

$rsgDisplay = new rsgDisplay_semantic();

global $mainframe;
$template_dir = JURI_SITE . "/components/com_rsgallery2/templates/" . $rsgConfig->get('template');

$rsgDisplay->metadata();
// append to Joomla's pathway
$rsgDisplay->showRSPathWay();

//Load Tooltips
JHTML::_('behavior.tooltip');

//include page navigation
//require_once(JPATH_ROOT.'/includes/pageNavigation.php');//J!1.0, bothering sh404SEF in J!1.5
jimport( 'joomla.html.pagination');//J!1.5

$doc =& JFactory::getDocument();
$doc->addStyleSheet($template_dir."/css/template.css","text/css");
?>

<div class="rsg2">
	<?php $rsgDisplay->mainPage(); ?>
</div>