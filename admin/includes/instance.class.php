<?php
/**
* @version $Id: instance.class.php 1010 2011-01-26 15:26:17Z mirjam $
* @package RSGallery2
* @copyright (C) 2005 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Create the request global object
 */
$GLOBALS['_RSGINSTANCE'] = null;

/**
* Represents an instance of RSGallery2
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgInstance{//as of v2.1.0 SVN 975 no longer extending JRequest
	
	/**
	 * Creates a new RSG2 instance and executes it.
	 *
	 * @static
	 * @param	array	$instance		What parameters to use for the new instance.  Your options are:
	 * array			A custom array.
	 * 'request'	Use the request array (default).
	 * @param boolean show a template or not.
	 */
	function instance( $newInstance = 'request', $showTemplate = true ){
		static $instanceStack = array();
		$stacked = false;

		// if rsg2 is already instanced then push the current instance to be pop'd later
		if( $GLOBALS['_RSGINSTANCE'] ){
			$stacked = true;

			if( count( $instanceStack ) > 9 )
				JError::raiseError( '9', 'RSGallery2 instance stack exceeds 9.  Probable endless recursion, $instanceStack:<pre>'.print_r( $instanceStack,1).'</pre>' );

			// push current instance on stack
			array_push( $instanceStack, $GLOBALS['_RSGINSTANCE'] );
		}

		$GLOBALS['_RSGINSTANCE'] = $newInstance;

		if( $showTemplate ){
			// execute a frontend template based instance
			require_once( JPATH_RSGALLERY2_SITE . '/main.rsgallery2.php' );
			rsgInstance::mainSwitch();
		}

		if( $stacked )
			$GLOBALS['_RSGINSTANCE'] = array_pop( $instanceStack );
	}
	
	/**
	* This is the main task switch where we decide what to do.
	*/
	function mainSwitch(){
		switch( rsgInstance::getVar( 'rsgOption', '' )) {
			case 'rsgComments':
				require_once(JPATH_RSGALLERY2_SITE . '/lib/rsgcomments/rsgcomments.php');
				break;
			case 'rsgVoting':
				require_once(JPATH_RSGALLERY2_SITE . '/lib/rsgvoting/rsgvoting.php');
				break;
			case 'myGalleries':
				require_once(JPATH_RSGALLERY2_SITE . '/lib/mygalleries/mygalleries.php');
				break;
			case'search':
				require_once(JPATH_RSGALLERY2_SITE . '/lib/rsgsearch/search.php');
				break;
			default:
				switch( rsgInstance::getVar( 'task', '' ) ){
					case 'xml':
						xmlFile();
						break;
					case "downloadfile":
						downloadFile();
						break;
					default:
						// require the base class rsgDisplay
						require_once( JPATH_RSGALLERY2_SITE . '/templates/meta/display.class.php' );
						// show the template
						template();
				}
		}
	}
	
	/**
		Various rsg2 object retrevial functions
	**/
	
	/**
	 * Returns the selected gallery.
	 *
	 * @static
	 * @deprecated Use rsgGalleryManager::get(); instead!
	 */
	function getGallery(){
		return rsgGalleryManager::get( );
	}
	
	/**
	 * Returns the selected item or the first item in the set if a set of items has been requested.
	 *
	 * @static
	 * @deprecated Use rsgGallery->getItem(); instead!
	 */
	function getItem(){
		$gallery = rsgInstance::getGallery();
		return $gallery->getItem();
	}
	
	/**
	 * Returns: an array of items in the order requested
	 * @todo default values should be obtained from config or elsewhere for limits, ordering, etc.
	 * @static
	 * @deprecated Use rsgGallery->items(); instead!
	 */
	function getItems(){
		$gallery = rsgInstance::getGallery();
		return $gallery->items();
	}
	
	/**
		JRequest functions
		
		Here we override functions necessary to use our rsgInstance::instance array.
	**/
	
	/**
	 * Fetches and returns a given variable.
	 *
	 * The default behaviour is fetching variables depending on the
	 * current request method: GET and HEAD will result in returning
	 * an entry from $_GET, POST and PUT will result in returning an
	 * entry from $_POST.
	 *
	 * You can force the source by setting the $hash parameter:
	 *
	 *   post		$_POST
	 *   get		$_GET
	 *   files		$_FILES
	 *   cookie		$_COOKIE
	 *   method		via current $_SERVER['REQUEST_METHOD']
	 *   default	$_REQUEST
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @param	string	$type		Return type for the variable, for valid values see {@link JInputFilter::clean()}
	 * @param	int		$mask		Filter mask for the variable
	 * @return	mixed	Requested variable
	 * @since	1.5
	 */
	function getVar($name, $default = null, $hash = 'default', $type = 'none', $mask = 0)
	{
		// Ensure hash and type are uppercase
		$hash = strtoupper( $hash );
		if ($hash === 'METHOD') {
			$hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
		}
		$type	= strtoupper( $type );
		$sig	= $hash.$type.$mask;

		// Get the input hash
		switch ($hash)
		{
			case 'GET' :
				$input = &$_GET;
				break;
			case 'POST' :
				$input = &$_POST;
				break;
			case 'FILES' :
				$input = &$_FILES;
				break;
			case 'COOKIE' :
				$input = &$_COOKIE;
				break;
			case 'REQUEST':
				$input = &$_REQUEST;
			default:
				if( $GLOBALS['_RSGINSTANCE'] == 'request' ){
					// check if a param exists first and return that if so
					global $mainframe;
					if( $mainframe instanceof JSite ){
						$params = &$mainframe->getParams();
						if( $params->get( $name ) )
							return $params->get( $name );
					}

					$input = &$_REQUEST;
					$hash = 'REQUEST';
				}
				else{
					$input = $GLOBALS['_RSGINSTANCE'];
					$hash = 'rsgInstance';
					// kludge!
					if( isset( $GLOBALS['_RSGINSTANCE'][$name] ))
						return $GLOBALS['_RSGINSTANCE'][$name];
					else
						return $default;
				}
				break;
		}

		if (isset($GLOBALS['_JREQUEST'][$name]['SET.'.$hash]) && ($GLOBALS['_JREQUEST'][$name]['SET.'.$hash] === true)) {
			// Get the variable from the input hash
			$var = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;
		}
		elseif (!isset($GLOBALS['_JREQUEST'][$name][$sig]))
		{
			if (isset($input[$name]) && $input[$name] !== null) {
				// Get the variable from the input hash and clean it
				$var = JRequest::_cleanVar($input[$name], $mask, $type);

				// Handle magic quotes compatability
				if (get_magic_quotes_gpc() && ($var != $default) && ($hash != 'FILES')) {
					$var = JRequest::_stripSlashesRecursive( $var );
				}

				$GLOBALS['_JREQUEST'][$name][$sig] = $var;
			} elseif ($default !== null) {
				// Clean the default value
				$var = JRequest::_cleanVar($default, $mask, $type);
			} else {
				$var = $default;
			}
		} else {
			$var = $GLOBALS['_JREQUEST'][$name][$sig];
		}

		return $var;
	}

	/**
	 * Fetches and returns a given filtered variable. The integer
	 * filter will allow only digits to be returned. This is currently
	 * only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return	integer	Requested variable
	 * @since	1.5
	 */
	function getInt($name, $default = 0, $hash = 'default') {
		return rsgInstance::getVar($name, $default, $hash, 'int');
	}

	/**
	 * Fetches and returns a given filtered variable.  The float
	 * filter only allows digits and periods.  This is currently
	 * only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return	float	Requested variable
	 * @since	1.5
	 */
	function getFloat($name, $default = 0.0, $hash = 'default') {
		return rsgInstance::getVar($name, $default, $hash, 'float');
	}

	/**
	 * Fetches and returns a given filtered variable. The bool
	 * filter will only return true/false bool values. This is
	 * currently only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return	bool		Requested variable
	 * @since	1.5
	 */
	function getBool($name, $default = false, $hash = 'default') {
		return rsgInstance::getVar($name, $default, $hash, 'bool');
	}

	/**
	 * Fetches and returns a given filtered variable. The word
	 * filter only allows the characters [A-Za-z_]. This is currently
	 * only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return	string	Requested variable
	 * @since	1.5
	 */
	function getWord($name, $default = '', $hash = 'default') {
		return rsgInstance::getVar($name, $default, $hash, 'word');
	}

	/**
	 * Fetches and returns a given filtered variable. The cmd
	 * filter only allows the characters [A-Za-z0-9.-_]. This is
	 * currently only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return	string	Requested variable
	 * @since	1.5
	 */
	function getCmd($name, $default = '', $hash = 'default') {
		return rsgInstance::getVar($name, $default, $hash, 'cmd');
	}

	/**
	 * Fetches and returns a given filtered variable. The string
	 * filter deletes 'bad' HTML code, if not overridden by the mask.
	 * This is currently only a proxy function for getVar().
	 *
	 * See getVar() for more in-depth documentation on the parameters.
	 *
	 * @static
	 * @param	string	$name		Variable name
	 * @param	string	$default	Default value if the variable does not exist
	 * @param	string	$hash		Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
 	 * @param	int		$mask		Filter mask for the variable
	 * @return	string	Requested variable
	 * @since	1.5
	 */
	function getString($name, $default = '', $hash = 'default', $mask = 0)
	{
		// Cast to string, in case JREQUEST_ALLOWRAW was specified for mask
		return (string) rsgInstance::getVar($name, $default, $hash, 'string', $mask);
	}

	/**
	 * Set a variabe in on of the request variables
	 *
	 * @access	public
	 * @param	string	$name		Name
	 * @param	string	$value		Value
	 * @param	string	$hash		Hash
	 * @param	boolean	$overwrite	Boolean
	 * @return	string	Previous value
	 * @since	1.5
	 */
	function setVar($name, $value = null, $hash = 'default', $overwrite = true)
	{
		//If overwrite is true, makes sure the variable hasn't been set yet
		if(!$overwrite && isset($_REQUEST[$name])) {
			return $_REQUEST[$name];
		}

		// Clean global request var
		$GLOBALS['_JREQUEST'][$name] = array();

		// Get the request hash value
		$hash = strtoupper($hash);
		if ($hash === 'METHOD') {
			$hash = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		$previous	= array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : null;

		switch ($hash)
		{
			case 'GET' :
				$_GET[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'POST' :
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'FILES' :
				$_FILES[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'COOKIE' :
				$_COOKIE[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'REQUEST' :
				$_GET[$name] = $value;
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				$GLOBALS['_JREQUEST'][$name]['SET.GET'] = true;
				$GLOBALS['_JREQUEST'][$name]['SET.POST'] = true;
				break;
			default:
				if( $GLOBALS['_RSGINSTANCE'] == 'request' ){
					$hash = 'REQUEST';
					$_GET[$name] = $value;
					$_POST[$name] = $value;
					$_REQUEST[$name] = $value;
					$GLOBALS['_JREQUEST'][$name]['SET.GET'] = true;
					$GLOBALS['_JREQUEST'][$name]['SET.POST'] = true;
				}
				else{
					$GLOBALS['_RSGINSTANCE'][$name] = $value;
					$hash = 'rsgInstance';
				}
				break;
		}

		// Mark this variable as 'SET'
		$GLOBALS['_JREQUEST'][$name]['SET.'.$hash] = true;
		$GLOBALS['_JREQUEST'][$name]['SET.REQUEST'] = true;

		return $previous;
	}

	/**
	 * Fetches and returns a request array.
	 *
	 * The default behaviour is fetching variables depending on the
	 * current request method: GET and HEAD will result in returning
	 * $_GET, POST and PUT will result in returning $_POST.
	 *
	 * You can force the source by setting the $hash parameter:
	 *
	 *   post		$_POST
	 *   get		$_GET
	 *   files		$_FILES
	 *   cookie		$_COOKIE
	 *   method		via current $_SERVER['REQUEST_METHOD']
	 *   default	$_REQUEST
	 *
	 * @static
	 * @param	string	$hash	to get (POST, GET, FILES, METHOD)
	 * @param	int		$mask	Filter mask for the variable
	 * @return	mixed	Request hash
	 * @since	1.5
	 */
	function get($hash = 'default', $mask = 0)
	{
		$hash = strtoupper($hash);

		if ($hash === 'METHOD') {
			$hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
		}

		switch ($hash)
		{
			case 'GET' :
				$input = $_GET;
				break;

			case 'POST' :
				$input = $_POST;
				break;

			case 'FILES' :
				$input = $_FILES;
				break;

			case 'COOKIE' :
				$input = $_COOKIE;
				break;

			case 'REQUEST' :
				$input = $_REQUEST;
				break;
				
			default:
				$input = $GLOBALS['_RSGINSTANCE'];
				$hash = 'rsgInstance';
				break;
		}

		$result = JRequest::_cleanVar($input, $mask);

		// Handle magic quotes compatability
		if (get_magic_quotes_gpc() && ($hash != 'FILES')) {
			$result = JRequest::_stripSlashesRecursive( $result );
		}

		return $result;
	}

	function set($array, $hash = 'default', $overwrite = true)
	{
		foreach($array as $key => $value) {
			rsgInstance::setVar($key, $value, $hash, $overwrite);
		}
	}

	/**
	 * Cleans the request from script injection.
	 *
	 * @static
	 * @return	void
	 * @since	1.5
	 */
	function clean()
	{
		JRequest::_cleanArray( $_FILES );
		JRequest::_cleanArray( $_ENV );
		JRequest::_cleanArray( $_GET );
		JRequest::_cleanArray( $_POST );
		JRequest::_cleanArray( $_COOKIE );
		JRequest::_cleanArray( $_SERVER );

		if (isset( $_SESSION )) {
			JRequest::_cleanArray( $_SESSION );
		}

		$REQUEST	= $_REQUEST;
		$GET		= $_GET;
		$POST		= $_POST;
		$COOKIE		= $_COOKIE;
		$FILES		= $_FILES;
		$ENV		= $_ENV;
		$SERVER		= $_SERVER;

		if (isset ( $_SESSION )) {
			$SESSION = $_SESSION;
		}

		foreach ($GLOBALS as $key => $value) {
			if ( $key != 'GLOBALS' ) {
				unset ( $GLOBALS [ $key ] );
			}
		}
		$_REQUEST	= $REQUEST;
		$_GET		= $GET;
		$_POST		= $POST;
		$_COOKIE	= $COOKIE;
		$_FILES		= $FILES;
		$_ENV 		= $ENV;
		$_SERVER 	= $SERVER;

		if (isset ( $SESSION )) {
			$_SESSION = $SESSION;
		}

		// Make sure the request hash is clean on file inclusion
		$GLOBALS['_JREQUEST'] = array();
	}

	/**
	 * Adds an array to the GLOBALS array and checks that the GLOBALS variable is not being attacked
	 *
	 * @access	protected
	 * @param	array	$array	Array to clean
	 * @param	boolean	True if the array is to be added to the GLOBALS
	 * @since	1.5
	 */
	function _cleanArray( &$array, $globalise=false )
	{
		static $banned = array( '_files', '_env', '_get', '_post', '_cookie', '_server', '_session', 'globals' );

		foreach ($array as $key => $value)
		{
			// PHP GLOBALS injection bug
			$failed = in_array( strtolower( $key ), $banned );

			// PHP Zend_Hash_Del_Key_Or_Index bug
			$failed |= is_numeric( $key );
			if ($failed) {
				die( 'Illegal variable <b>' . implode( '</b> or <b>', $banned ) . '</b> passed to script.' );
			}
			if ($globalise) {
				$GLOBALS[$key] = $value;
			}
		}
	}
}
