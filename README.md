# openresty-dedicated-toolsets : Citalis-Livecode-AS
Citalis-Livecode-AS (OpenResty, Livecode TCP application's server, PostgreSQL and MySQL/MariaDB versions)

Note: because this Livecode TCP application's server version of the Citalis portal is using the native Livecode PostgreSQL and MySQL/MariaDB drivers, it will run without notable changes on top of the Apache2, OpenLiteSpeed and Lighttpd platforms.

Installation :

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.<br />
2.- Drag n'Drop the 40x.html, 50x.html and citalis_orc.html files to the same place.<br />
3.- Add the arsheos.js file to the /assets/js/ subdirectory.<br />
4.- Add the arsheos.css file to the /assets/css/ subdirectory.<br />
5.- Use your prefered postgresql managment tool (phpPgAdmin, etc...) to add the citalis db (one table) on your pg server.<br />
6.- Use your prefered mysql/mariadb managment tool (phpMyAdmin, etc...) to add the citalis db (one table) on your mysql/mariadb server.<br />
7.- Download the GPL3 licensed version of the Livecode 8.xx suited to your OS from downloads.livecode.com/livecode/ and install it on your box.<br />
8.- Compile the <br />
6.- Adapt all the local paths and db user:password to your own configuration in updating them in the citalis.lc file.<br />
7.- Open your ../openresty/nginx/conf/ directory and update your nginx.conf file in picking up the adequate citalis and postgresql configuration setups available inside the nginx.conf file we provide you.<br />
8.- Add the nginx_citalis.conf file to your ../openresty/nginx/conf/ directory and don't forget to reference it inside your nginx.conf file.<br />
9.- Add the nginx_citalis.lua application's file to your ../openresty/nginx/ directory and don't forget to restart nginx to let him takes all the upon configuration changes in account.<br />

Just type the http(s)://yoursite/path-to-citalis.lls nginx handled virtual location url to connect your new installed citalis app portal and have fun.
