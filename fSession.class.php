<?php
/**
 * Handles session-related data
 * 
 * @copyright  Copyright (c) 2007 William Bond
 * @author     William Bond [wb] <will@flourishlib.com>
 * @license    http://flourishlib.com/license
 * 
 * @link  http://flourishlib.com/fSession
 * 
 * @uses  fCore
 * @uses  fProgrammerException
 * 
 * @version  1.0.0
 * @changes  1.0.0    The initial implementation [wb, 2007-06-14]
 */
class fSession
{
	/**
	 * If the session is open
	 * 
	 * @var boolean 
	 */
	static private $open = FALSE;
	
	
	/**
	 * Prevent instantiation
	 * 
	 * @since  1.0.0
	 * 
	 * @return fSession
	 */
	private function __construct() { }
	
	
	/**
	 * Sets the session to run on the main domain, not just the specific subdomain currently being accessed
	 * 
	 * @since  1.0.0
	 * 
	 * @return void
	 */
	static public function ignoreSubdomain()
	{
		if (!self::$open) {
			session_set_cookie_params(0, '/', preg_replace('#.*?([a-z0-9\\-]+\.[a-z]+)$#i', '.\1', $_SERVER['SERVER_NAME']));
		} else {
			fCore::toss('fProgrammerException', 'fSession::ignoreSubdomain() must be called before fSession::open()');	
		}
	}
	
	
	/**
	 * Opens the session for writing
	 * 
	 * @since  1.0.0
	 *  
	 * @return void
	 */
	static public function open()
	{
		if (!self::$open) {
			session_start();
			self::$open = TRUE;
		}
	}
	
	
	/**
	 * Closes the session for writing
	 * 
	 * @since  1.0.0
	 * 
	 * @return void
	 */
	static public function close()
	{
		if (self::$open) {
			session_write_close();
			self::$open = FALSE;
		}
	}
	
	
	/**
	 * Destroys the session, removing all traces
	 * 
	 * @since  1.0.0
	 * 
	 * @return void
	 */
	static public function destroy()
	{
		self::open();
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time()-43200, $params['path'], $params['domain']);
		}
		session_destroy();
	}
	
	
	/**
	 * Sets data to the session superglobal, prefixing it with fSession:: to prevent issues with $_REQUEST
	 * 
	 * @since  1.0.0
	 * 
	 * @param  string $key      The name to save the value under
	 * @param  mixed  $value    The value to store
	 * @param  string $prefix   The prefix to stick before the key
	 * @return void
	 */
	static public function set($key, $value, $prefix='fSession::')
	{
		if (!self::$open) {
			fCore::toss('fProgrammerException', 'fSession::open() must be called before fSession::set()');	
		}
		$_SESSION[$prefix . $key] = $value;
	}
	
	
	/**
	 * Gets data from the session superglobal, prefixing it with fSession:: to prevent issues with $_REQUEST
	 * 
	 * @since  1.0.0
	 * 
	 * @param  string $key       The name to get the value for
	 * @param  mixed  $default   The default value to use if the requested key is not set
	 * @param  string $prefix    The prefix to stick before the key
	 * @return mixed  The data element requested
	 */
	static public function get($key, $default=NULL, $prefix='fSession::')
	{
		if (!self::$open) {
			fCore::toss('fProgrammerException', 'fSession::open() must be called before fSession::get()');	
		}
		return (isset($_SESSION[$prefix . $key])) ? $_SESSION[$prefix . $key] : $default;
	}
	
	
	/**
	 * Gets all of the data from the session superglobal, filtering by fSession:: (can be overridden)
	 * 
	 * @since  1.0.0
	 * 
	 * @param  string $prefix   The prefix to filter key => values by
	 * @return array  All of the session values for the prefix specified
	 */
	static public function getAll($prefix='fSession::')
	{
		if (!self::$open) {
			fCore::toss('fProgrammerException', 'fSession::open() must be called before fSession::getAll()');	
		}
		$output = array();
		foreach ($_SESSION as $key => $value) {
			if (strpos($key, $prefix) === 0) {
				$output[str_replace($prefix, '', $key)] = $value;
			}	
		}
		return $output;
	}
}


/**
 * Copyright (c) 2007 William Bond <will@flourishlib.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */  
?>