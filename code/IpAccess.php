<?php
/**
 * Check Access based on remote IP address
 *
 * @Example entries :
 * 192.168.178.8
 * 192.168.178.0/24
 * 192.168.178.0-50
 * 192.168.178.*
 * 192.168.*
 *
 * @return false || string : entry the ip address was matched against
 */
class IpAccess {
	
	public $allowedIps	= array();
	
	private $ip	= '';
	
	public function __construct($ip = '', $allowedIps = array()) {
		$this->ip			= $ip;
		$this->allowedIps	= $allowedIps;
	}
	
	public function setIp($ip) {
		$this->ip			= $ip;
	}
	
	public function hasAccess() {
		if(!$this->allowedIps) {
			return 'allowed';
		}elseif($match = $this->matchExact()){
			return $match;
		}elseif($match = $this->matchRange()){
			return $match;
		}elseif($match = $this->matchCIDR()){
			return $match;
		}elseif($match = $this->matchWildCard()){
			return $match;
		}
	}
	
	public function matchExact() {
		if(in_array($this->ip, $this->allowedIps)) {
			return $this->ip;
		}
	}
	
	/**
	 * try to match against a ip range
	 * Example : 192.168.1.50-100
	 */
	public function matchRange() {
		if($ranges = array_filter($this->allowedIps, function($ip) { return strstr($ip, '-'); })) {
			foreach($ranges as $range) {
				$first = substr($range, 0, strrpos($range ,'.') + 1);
				$last = substr(strrchr($range,'.'), 1);
				list ($start, $end) = explode('-',$last);
				for($i = $start; $i <= $end; $i++) {
					if($this->ip === $first . $i) {
						return $range;
					}
				}
			}
		}
	}
	
	/**
	 * try to match cidr range
	 * Example : 192.168.1.0/24
	 */
	public function matchCIDR() {
		if($ranges = array_filter($this->allowedIps, function($ip) { return strstr($ip, '/'); })) {
			foreach($ranges as $cidr) {
				// copied from https://github.com/symfony/http-foundation/blob/master/IpUtils.php
				if (false !== strpos($cidr, '/')) {
					list($address, $netmask) = explode('/', $cidr, 2);
					if ($netmask === '0') {
						// Ensure IP is valid - using ip2long below implicitly validates, but we need to do it manually here
						return filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
					}
					if ($netmask < 0 || $netmask > 32) {
						return false;
					}
				} else {
					$address = $cidr;
					$netmask = 32;
				}
				if (0 === substr_compare(sprintf('%032b', ip2long($this->ip)), sprintf('%032b', ip2long($address)), 0, $netmask)) {
					return true;
				}
			}
		}
	}
	
	/**
	 * try to match against a range that ends with a wildcard *
	 * Example : 192.168.1.*
	 * Example : 192.168.*
	 */
	public function matchWildCard() {
		if($ranges = array_filter($this->allowedIps, function($ip) { return substr($ip, -1) === '*'; })) {
			foreach($ranges as $range) {
				if(substr($this->ip, 0, strlen(substr($range, 0, -1))) === substr($range, 0, -1)) {
					return $range;
				}
			}
		}
	}
	
}