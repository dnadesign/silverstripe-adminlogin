<?php

/**
 * @package adminlogin
 */
class AdminSecurity extends Security {
	
	/**
	 * @var array
	 */
	private static $allowed_actions = array( 
		'passwordsent'
	);
	
	/**
	 * Template thats used to render the pages.
	 *
	 * @var string
	 */
	private static $template_main = 'AdminLogin';
	
	public function init() {
		// if the extension has been disabled then redirect back to the Security
		$security = singleton('Security');

		if(!$security->has_extension('AdminLoginExtension')) {
			return $this->redirect(Controller::join_links(
				$security->Link($this->request->param('Action')),
				(isset($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : ''
			));
		}

		parent::init();

		
		// this prevents loading frontend css and javscript files
		Object::useCustomClass('Page_Controller','AdminLoginPageController');
		Object::useCustomClass('MemberLoginForm','AdminLoginForm');

		Requirements::css('adminlogin/css/style.css');
	}
	
	/**
	 * @param string $action
	 *
	 * @return string
	 */
	public function Link($action = null) {
		return "AdminSecurity/$action";
	}
	
	/**
	 * @return boolean
	 */
	public static function isAdminLogin() {
		return strstr(self::getBackUrl(), '/admin/');
	}
	
	/**
	 * @return string
	 */
	public static function getBackUrl() {
		if(isset($_REQUEST['BackURL'])) {
			return $_REQUEST['BackURL'];
		} elseif(isset($_SESSION['BackURL'])) {
			return $_SESSION['BackURL'];
		}
	}
	
	/**
	 * @see Security::getPasswordResetLink()
	 *
	 * We overload this, so we can add the BackURL to the password resetlink
	 */
	public static function getPasswordResetLink($member, $autologinToken) {
		$autologinToken = urldecode($autologinToken);		
		$selfControllerClass = __CLASS__;
		$selfController = new $selfControllerClass();
		
		return $selfController->Link('changepassword') . "?m={$member->ID}&t=$autologinToken";
	}
	
	/**
	 * @return ChangePasswordForm
	 */
	public function ChangePasswordForm() {
		return new ChangePasswordForm($this, 'ChangePasswordForm');
	}
}