<?php
/**
* This file contains xxxxxxxxxxxxxxxxxxxxxxxxxxx.
* @version xxx
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once( JPATH_RSGALLERY2_SITE . DS . 'lib' . DS . 'rsgvoting' . DS . 'rsgvoting.class.php' );

$cid    = rsgInstance::getInt('cid', array(0) );
$task    = rsgInstance::getVar('task', '' );
$id    = rsgInstance::getInt('id','' );

switch( $task ){
    case 'save':
        saveVote( $option );
        break;
}

function test( $id ) {
		echo "<pre>";
		print_r($_COOKIE);
		echo "</pre>";
		$cookie_prefix = strval("rsgvoting_".$id);
		echo $cookie_prefix;
	if (!isset($_COOKIE[$cookie_prefix])) {
		//Cookie valid for 1 year!
		setcookie($cookie_prefix ,$id ,time()+60*60*24*365, "/");
	}

}
function saveVote( $option ) {
	
	global $rsgConfig,$mainframe;
	
	$database = JFactory::getDBO();
	$my = JFactory::getUser();
	
	if ( $rsgConfig->get('voting') < 1 ) {
		$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2"), JText::_('Voting is disabled!'));
	} else {
		$rating 	= rsgInstance::getInt('rating', '');
		$id 		= rsgInstance::getInt('id', '');
		$vote 		= new rsgVoting();
		//Check if user can vote
		if (!$vote->voteAllowed() ) {
			$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&page=inline&id=$id"), JText::_('You are not authorized to vote!'));
		}
		
		//Check if user has already voted for this image
		if ($vote->alreadyVoted($id)) {
		 	$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&page=inline&id=$id"), JText::_('You already voted for this item!'));
		}
		
		//All checks OK, store vote in DB
		$total 		= $vote->getTotal( $id ) + $rating;
		$votecount 	= $vote->getVoteCount( $id ) + 1;
		
		$sql = "UPDATE #__rsgallery2_files SET rating = '$total', votes = '$votecount' WHERE id = '$id'";
		$database->setQuery( $sql );
		if ( !$database->query() ) {
			$msg = JText::_('Vote could not be added to the database!');
		} else {
			$msg = JText::_('Vote added to database!');
			//Store cookie on system
			setcookie($rsgConfig->get('cookie_prefix').$id, $my->id, time()+60*60*24*365, "/");
		}
		$mainframe->redirect(JRoute::_("index.php?option=com_rsgallery2&page=inline&id=$id"), $msg);
	}
}
?>