# openresty-dedicated-toolsets
Saas, RIA and web applications toolsets using mainly OpenResty, HHVM, PHP, Hack, Kotlin, Redis and PostgreSQL

#Installation

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.<br />
2.- Drag n'Drop the 40x.html, 50x.html, citalis.php and citalis_php.html files to the same place.<br />
3.- Add the arsheos.js file to the /assets/js/ subdirectory.<br />
4.- Add the arsheos.css file to the /assets/css/ subdirectory.<br />
5.- Use your prefered mysql/mariadb managment tool (phpPgAdmin, etc...) to add the citalis db (one table) on your pg server.<br />
6.- Adapt all the local paths and db user:password to your own configuration in updating them in the citalis.lc file.<br />
7.- Open your ../openresty/nginx/conf/ directory and update your nginx.conf file in picking up the adequate citalis and postgresql configuration setups available inside the nginx.conf file we provide you.<br />
8.- Add the php-fpm.conf file to your ../openresty/nginx/conf/ directory and don't forget to reference it inside your nginx.conf file.<br />
10.- Add the nginx.conf file to your ../openresty/nginx/conf/ directory and don't forget to restart nginx to let him takes all the upon configuration changes in account.<br />

Just type the http(s)://yoursite/path-to-citalis.php url to connect your new installed citalis app portal and have fun.

Be aware that all the provided material went always installed on Linux-64 powered servers. Any installation done against Windows, Mac OS X, BSD or Solaris based platforms won't probably not run out of the box without adequate adaptations.

Before installing one or both of those solutions for any testing or development needs, be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

Please refer to each dedicated README.md file available inside the different subdirectories for installation instructions.
