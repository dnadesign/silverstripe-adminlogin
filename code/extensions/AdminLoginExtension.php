<?php

/**
 * Custom Admin Login form screen
 * This login screen get also ip based access protection when enabled
 *
 * @package adminlogin
 */
 
class AdminLoginExtension extends Extension {

	public function onBeforeSecurityLogin() {
		if(!$this->owner instanceof AdminSecurity) {
			if(!isset($_GET['BackURL']) || strstr($_GET['BackURL'], '/admin/')) {
				if(Controller::curr()->class != 'AdminSecurity') {
					$link = 'AdminSecurity/login' . '?BackURL=' . urlencode($_GET['BackURL']);

					return $this->owner->redirect($link);
				}
			}
		}
	}
}