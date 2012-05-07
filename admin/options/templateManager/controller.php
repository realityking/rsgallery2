<?php
/**
 * @version		$Id: controller.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package		Joomla
 * @subpackage	Installer
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

/**
 * Installer Controller
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerController extends JController
{
	/**
	 * Display the extension installer form
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function installform()
	{
		$model	= &$this->getModel( 'Install' );
		$view	= &$this->getView( 'Install', '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$view->setModel( $model, true );
		$view->display();
	}
	
	/**
	 * Install an extension
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function doInstall()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'Install' );
		$view	= &$this->getView( 'Install' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		if ($model->install()) {
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}
		
		$view->setModel( $model, true );
		$view->display();
	}
	
	/**
	 * List all templates
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function manage()
	{
		$model	= &$this->getModel( 'templates' );
		$view	= &$this->getView( 'templates' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$view->setModel( $model, true );
		$view->display();
	}
	
	/**
	 * Set template as default
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function setDefault()
	{
		
		global $rsgConfig;
		// Check for request forgeries
		JRequest::checkToken( 'request' ) or die( 'Invalid Token' );
		
		$template = JRequest::getVar( 'template' );
		$rsgConfig->set('template', $template);
		$rsgConfig->saveConfig();
		$this->manage();
		
	}
	
	/**
	 * Remove an extension (Uninstall)
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function remove()
	{
		global $rsgConfig;
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$template = JRequest::getVar( 'template' );
		
		if($rsgConfig->template == $template) {
			JError::raiseWarning( 500, 'Can not delete default template.', "Select an other template and then delete this one." );
		}
		else{
			JFolder::delete(JPATH_RSGALLERY2_SITE . DS . "templates" . DS . $template);
		}
		
		$this->manage();		
		
	}
	
	function template(){
		switch($this->get('task_type', 'templateGeneral')){
			
			case "templateCSS": $this->selectCSS();break;
			case "templateHTML": $this->selectHTML();break;
			case "templateGeneral":
			case "templates": $this->editTemplate();break;
		}	
		
	}
	/**
	 * edit the base data of a template
	 * @access	public
	 * @return	void
	 * @since	RSG 1.5
	 * @author John Caprez (john@porelaire.com)
	 */
	function editTemplate(){
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'template' );
		$view	= &$this->getView( 'template' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$model->template = $template;
		
		$view->setModel( $model, true );
		$view->display();
		
	}
	/**
	 * apply chnages to template
	 * @access	public
	 * @return	void
	 * @since	RSG 1.5
	 * @author John Caprez (john@porelaire.com)
	 */
	function applyTemplate(){
		
		$model	= &$this->getModel( 'template' );
		$view	= &$this->getView( 'template' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$params	= JRequest::getVar('params', array(), 'post', 'array');
		
		$model->set('template', $template);
		$model->set('params' , $params);
		$model->update();
		
		$view->setModel( $model, true );
		$view->display();
		
	}
	/**
	* save chenges to template
	* @access	public
	* @return	void
	* @since	RSG 1.5
	* @author John Caprez (john@porelaire.com)
	*/
	function saveTemplate(){
		
		$model	= &$this->getModel( 'template' );
		
		$template = JRequest::getVar( 'template' );
		$params	= JRequest::getVar('params', array(), 'post', 'array');
		
		$model->set('template', $template);
		$model->set('params' , $params);
		
		$model->update();
		
		$this->manage();
	}
	
	/**
	 * select witch css file has to be edited
	 * @access	public
	 * @return	void
	 * @since	RSG 1.5
	 * @author John Caprez (john@porelaire.com)
	 */
	function selectCss(){
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'selectCss' );
		$view	= &$this->getView( 'selectCss' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$model->template = $template;
		
		$view->setModel( $model, true );
		$view->display();
	}
	/**
	* edit a CSS file
	* @access	public
	* @return	void
	* @since	RSG 1.5
	* @author John Caprez (john@porelaire.com)
	*/
	function editCSS(){
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editCss' );
		$view	= &$this->getView( 'editCss' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$model->template = $template;
		$model->filename = JRequest::getVar( 'filename' );
		
		$view->setModel( $model, true );
		$view->display();
	}
	function saveCSS()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editCss' );
		$model->filename = JRequest::getVar( 'filename' );
		$model->content = JRequest::getVar('csscontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$model->template = JRequest::getVar( 'template' );
		
		$model->save();

		$this->selectCss();
	}
	function applyCSS()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editCss' );
		$model->filename = JRequest::getVar( 'filename' );
		$model->content = JRequest::getVar('csscontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$model->template = JRequest::getVar( 'template' );
		
		$model->save();
		
		$this->editCSS();
	}
	function cancelCSS()
	{
		$this->selectCss();
	}
	
	/**
	 * select witch html file has to be edited
	 * @access	public
	 * @return	void
	 * @since	RSG 1.5
	 * @author John Caprez (john@porelaire.com)
	 */
	function selectHTML(){
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'selectHtml' );
		$view	= &$this->getView( 'selectHtml' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$model->template = $template;
		
		$view->setModel( $model, true );
		$view->display();
	}
	/**
	* edit a HTML file
	* @access	public
	* @return	void
	* @since	RSG 1.5
	* @author John Caprez (john@porelaire.com)
	*/
	function editHTML() {
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editHtml' );
		$view	= &$this->getView( 'editHtml' , '', '', array( 'base_path'=>rsgOptions_installer_path ) );
		
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		
		$template = JRequest::getVar( 'template' );
		$model->template = $template;
		$model->filename = JRequest::getVar( 'filename' );
		
		$view->setModel( $model, true );
		$view->display();
	}
	function saveHTML()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editHtml' );
		$model->filename = JRequest::getVar( 'filename' );
		$model->content = JRequest::getVar('htmlcontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$model->template = JRequest::getVar( 'template' );
		
		$model->save();
		
		$this->selectHTML();
	}
	function applyHTML()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model	= &$this->getModel( 'editHtml' );
		$model->filename = JRequest::getVar( 'filename' );
		$model->content = JRequest::getVar('htmlcontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$model->template = JRequest::getVar( 'template' );
		
		$model->save();
		
		$this->editHTML();
	}
	function cancelHTML()
	{
		$this->selectHTML();
	}
}

