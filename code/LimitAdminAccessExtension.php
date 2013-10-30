<?php

class LimitAdminAccessExtension extends Extension {
	
	function onBeforeInit() {
		if(Config::inst()->get('IpAccess', 'enabled')) {
			$ipAccess = new IpAccess($this->owner->getRequest()->getIP(), Config::inst()->get('IpAccess', 'allowed_ips'));
				
			if(!$ipAccess->hasAccess()) {
				$reponse = '';
				if(class_exists('ErrorPage', true)) {
					$response = ErrorPage::response_for(404);
				}
				return $this->owner->httpError(404, $response ? $response : 'The requested page could not be found.');
			}
		}
	}
}