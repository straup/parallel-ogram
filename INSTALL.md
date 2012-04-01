Installing parallel-ogram
--

**THIS DOCUMENT IS NOT COMPLETE**

privatesquare is built on top of [Flamework](https://github.com/exflickr/flamework) which means it's nothing more
than a vanilla Apache + PHP + MySQL application. You can run it as a dedicated
virtual host or as a subdirectory of an existing host.

You will need to make a copy of the [config.php.example](https://github.com/straup/parallel-ogram/blob/master/www/include/config.php.example) file and name it
`config.php`. You will need to update this new file and add the various
specifics for databases and third-party APIs.

The basics
===

	# You will need valid Instagram OAuth credentials
	# See also: http://instagram.com/developer/client/register/

	$GLOBALS['cfg']['instagram_oauth_key'] = '';
	$GLOBALS['cfg']['instagram_oauth_secret'] = '';
	
	# Don't change this (but if you do be sure to update the main
	# .htaccess file accordingly).
	
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
	# that need to use encrypted cookies. Don't leave these empty. Really.

	# You can create new secrets by typing `make secret`.
	# See also: https://github.com/straup/privatesquare/blob/master/bin/generate_secret.php

	$GLOBALS['cfg']['crypto_cookie_secret'] = '';
	$GLOBALS['cfg']['crypto_crumb_secret'] = '';
	$GLOBALS['cfg']['crypto_password_secret'] = '';

	# Backups - by default anyone who knows where your copy of
	# parallel-ogram is (on the Internet) could register to have
	# their photos (and likes) backed up on your machine. If you
	# don't want to let anyone else backup their photos then you
	# should disable the 'enable_feature_backups_registration' flag.
	# If you want to limit who can register take a look at the invite
	# code flags below.

	$GLOBALS['cfg']['enable_feature_backups'] = 1;
	$GLOBALS['cfg']['enable_feature_backups_registration'] = 1;

Limiting access (invite codes and "god" auth)
===

	# Invite codes – these are used to limit who can register
	# to have their photos backed up. You'll need to do a
	# few things to get this working:

	# 1) enable the feature flags below for invite codes and
	#    god auth (which is explained below)

	# 2) generate a new secret for encrypting invite cookies

	# 3) set up poorman's 'god auth' – basically this is just
	#    restricting access to a list of logged in user using
	#    cookies; it works but I wouldn't call it "secure"

	# Once that's done you can manage or create new invites
	# here:

	# $GLOBALS['cfg']['abs_root_url']/god/invites/
	# $GLOBALS['cfg']['abs_root_url']/god/invites/generate/

	# In addition, if a user tries to go to the backup page
	# ($GLOBALS['cfg']['abs_root_url']/account/instagram/backups)
	# they'll got stopped by an invite code wall which will
	# allow them to request an invite code but you'll still
	# need to send it manually (by pressing a button on the
	# god page).

	# A word about "sending" invites: It is assumed that your
	# server it set up to send email. If it's not then the
	# problem is sort of out of scope for this project. The
	# alternative is to generate the invite code and then
	# just copy/paste it someone in plain old email client.
	
	$GLOBALS['cfg']['enable_feature_invite_codes'] = 0;

	$GLOBALS['cfg']['crypto_invite_secret'] = '';

	$GLOBALS['cfg']['auth_enable_poormans_god_auth'] = 0;

	$GLOBALS['cfg']['auth_poormans_god_auth'] = array(

	# They are keyed off a user's 'id' from the 'users'
	# table in db_main. If you're wondering: Yes, that
	# means you'll need to look at the database at least
	# once to see your user ID. I may display this in
	# future versions of the account page(s) but right
	# now I don't so it remains a bit of a chicken and
	# egg problem. Not hard just not elegant yet.

	#	'0' => array(
	#		'roles' => array('admin')
	#	),

	);

Remaining details
===

	# This is only relevant if are running parallel-ogram on a machine where you
	# can not make the www/templates_c folder writeable by the web server. If that's
	# the case set this to 0 but remember that you'll need to pre-compile all
	# of your templates before they can be used by the site.
	# See also: https://github.com/straup/parallel-ogram/blob/master/bin/compile-templates.php

	$GLOBALS['cfg']['smarty_compile'] = 1;

That's it. Or should be. If I've forgotten something please let me know or
submit a pull request.

