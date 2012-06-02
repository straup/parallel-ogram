parallel-ogram
--

It's like parallel-flickr but for Instagram.

parallel-ogram is a simple web application to create an archive of your
Instragram photos (and "likes") and to make that archive a living, breathing
website of its own. It is not a replacement for Instagram but a shadow copy
running in... parallel.

You can filter you photos by well, Instagram filters and your likes by filter as
well as photographer.

You can archive photos for other Instagram users or just yourself. That's your
business.

parallel-ogram uses the Instagram API as a single-sign-on (SSO) provider so you
simply log in via the Instagram website itself.

Caveats
--

parallel-ogram is not hard to set up but there is still no "one-button" install
yet. In the meantime, setting up parallel-ogram is not very complicated if you've
ever set up a plain-vanilla website that uses PHP and MySQL.

The Instagram API does not currently expose any consistent indication of whether
a person's photos are public or not-public. In the absence of that information
parallel-ogram treats everything as private, meaning you're the only person who can see your photos. The exception to this
rule is that you can see other people's photos that you've liked and vice
versa. At some point in the near-future I would like to add the ability to mark
photos 
in parallel-ogram as public or private, independent of any Instagram settings,
but that's not possible right now.

parallel-ogram does nothing about geo. Location information for every photo is
recorded in the database but currently 
it is not used for anything.

Installing parallel-ogram
--

Installation instructions are outlined in a separate [INSTALL.md](https://github.com/straup/parallel-ogram/blob/master/INSTALL.md) file.

Actually backing up your photos
--

Photos and likes are backed up using the
[backup_photos.php](https://github.com/straup/parallel-ogram/blob/master/bin/backup_photos.php)
and
[backup_likes.php](https://github.com/straup/parallel-ogram/blob/master/bin/backup_likes.php)
scripts, respectively. Those scripts are located in the `parallel-ogram/bin`
directory. They need to be run by hand, like this:

	$> php -q ./bin/backup_photos.php
	
If you want to automate the process you'll need to stick them in a local cron
tab or some other similar scheduling tool.

Automagic backing up of your photos (using the Instagram PuSH feeds)
--

parallel-ogram can also be configured to archive the photos for registered users
using the
[real-time photo update PuSH feeds](http://instagram.com/developer/realtime/)
from Instagram.

By default this functionality is disabled  default because in order to use it
you need to ensure that the directory specified in the
$GLOBALS['cfg']['instagram_static_path'] config variable is writeable by the web
server. To enable the PuSH features you'll need to update the following in your
config file: 

	$GLOBALS['cfg']['enable_feature_instagram_push'] = 1;

Also, two are two important caveats about the PuSH stuff:

1) It does not magically start happening as soon as you turn it on. You will
need to create a new subscription using: 

	$> [bin/subscribe-push-feed.php](https://github.com/straup/parallel-ogram/blob/master/bin/subscribe-push-feed.php) -t user -u http://your-website.com
	
Or:

	[http://your-website.com/god/push/subscriptions/](https://github.com/straup/parallel-ogram/blob/master/www/god/push_subscriptions.php)

Note that you'll need to have poor man's god auth enabled,
[in the config file](https://github.com/straup/parallel-ogram/blob/master/www/include/config.php.example),
for that 'god' URL to work.

2) It's only for archiving the photos belonging to user's who've authed with
this particular instance of parallel-ogram. It does not expose any "public" UI
or deal with photos people have liked. This is because of privacy issues (or
simply a lack of functionality) in the Instagram implementation.

See also
--

* [flamework](https://github.com/straup/flamework)

* [flamework-instagramapp](https://github.com/straup/flamework-instagramapp)

* ["flinstagram"](https://gist.github.com/1926097)

Shout outs
--

* [I blame Insam](https://github.com/tominsam/instabackup)
