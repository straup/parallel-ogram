Installing parallel-ogram
--

**THIS DOCUMENT IS NOT COMPLETE**

privatesquare is built on top of [Flamework](https://github.com/exflickr/flamework) which means it's nothing more
than a vanilla Apache + PHP + MySQL application. You can run it as a dedicated
virtual host or as a subdirectory of an existing host.

You will need to make a copy of the [config.php.example](https://github.com/straup/parallel-ogram/blob/master/www/include/config.php.example) file and name it
`config.php`. You will need to update this new file and add the various
specifics for databases and third-party APIs.

	# You will need valid Instagram OAuth credentials
	# See also: FIX ME

	$GLOBALS['cfg']['instagram_oauth_key'] = '';
	$GLOBALS['cfg']['instagram_oauth_secret'] = '';
	
	# Don't change this. If you do update the main .htaccess
	# file accordingly.
	
	$GLOBALS['cfg']['instagram_oauth_callback'] = 'auth/';

	# You will need to setup a MySQL database and plug in the specifics
	# here: https://github.com/straup/privatesquare/blob/master/schema
	# See also: https://github.com/straup/flamework-tools/blob/master/bin/setup-db.sh

	$GLOBALS['cfg']['db_main'] = array(
		'host' => 'localhost',
		'name' => 'parallelogram',
		'user' => 'parallelogram',
		'pass' => '',
		'auto_connect' => 1,
	);

	# You will need to set up secrets for the various parts of the site
	# that need to be encrypted. Don't leave these empty. Really.
	# You can create new secrets by typing `make secret`.
	# See also: https://github.com/straup/privatesquare/blob/master/bin/generate_secret.php

	$GLOBALS['cfg']['crypto_cookie_secret'] = '';
	$GLOBALS['cfg']['crypto_crumb_secret'] = '';
	$GLOBALS['cfg']['crypto_password_secret'] = '';

	# If you don't have memcache installed (or don't even know what that means)
	# just leave this blank. Otherwise change the 'cache_remote_engine' to
	# 'memcache'.

	$GLOBALS['cfg']['cache_remote_engine'] = '';
	$GLOBALS['cfg']['memcache_host'] = 'localhost';
	$GLOBALS['cfg']['memcache_port'] = '11211';

	# This is only relevant if are running parallel-ogram on a machine where you
	# can not make the www/templates_c folder writeable by the web server. If that's
	# the case set this to 0 but remember that you'll need to pre-compile all
	# of your templates before they can be used by the site.
	# See also: https://github.com/straup/parallel-ogram/blob/master/bin/compile-templates.php

	$GLOBALS['cfg']['smarty_compile'] = 1;

That's it. Or should be. If I've forgotten something please let me know or
submit a pull request.

