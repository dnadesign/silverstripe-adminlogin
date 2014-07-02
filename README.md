silverstripe-adminlogin
=======================

[![Build Status](https://secure.travis-ci.org/axyr/silverstripe-adminlogin.png)](https://travis-ci.org/axyr/silverstripe-adminlogin)

Use a custom login screen when log in to the admin section 

## Usage 

### Custom login screen

Provide a `AdminLogin.ss` file in your theme to style the page. It must contain
at least a `$Form` variable and your <% base_tag %>

![Screenshot](https://raw.github.com/axyr/silverstripe-adminlogin/master/images/screenshot.png)

### Limiting access to admin

You can also limit the admin/cms section by ip address (ranges). The following 
formats are supported:

* 192.168.1.101
* 192.168.1.100-200
* 192.168.1.0/24'
* 192.168.1.*

To set what IP addresses have access to the admin add the following block to 
`mysite/_config/adminips.yml`

```
---
name: appaccess
after: '#IpAccess'
---
IpAccess:
  enabled: true
  allowed_ips:
    - '192.168.1.1'

```

And ensure you flush the cache to update the allowed IP addresses.

## Removing an extension

If you want to disable the custom login form (and just use the restricted IP 
functionality) then add the following line in your `mysite/_config.php`

	Security::remove_extension('AdminLoginExtension');


## Known issues

When you are logged in, but don't have admin permissions, the normal site theme 
will be used.
