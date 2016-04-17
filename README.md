# openresty-dedicated-toolsets : Citalis-Livecode-AS
Citalis-Livecode-AS (OpenResty, Livecode TCP application's server, PostgreSQL and MySQL/MariaDB versions)

Note: because this Livecode TCP application's server version of the Citalis portal is using the native Livecode PostgreSQL and MySQL/MariaDB drivers, it will run without notable changes on top of the Apache2, OpenLiteSpeed and Lighttpd platforms.

#Installation :

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.<br />
2.- Drag n'Drop the 40x.html, 50x.html and citalis_orc.html files to the same place.<br />
3.- Add the arsheos.js file to the /assets/js/ subdirectory.<br />
4.- Add the arsheos.css file to the /assets/css/ subdirectory.<br />
5.- Use your prefered postgresql managment tool (phpPgAdmin, etc...) to add the citalis db (one table) on your pg server.<br />
6.- Use your prefered mysql/mariadb managment tool (phpMyAdmin, etc...) to add the citalis db (one table) on your mysql/mariadb server.<br />
7.- Download the GPL3 licensed version of the Livecode 8.xx suited to your OS from http://downloads.livecode.com/livecode/ and install it on your development box.<br />
8.- Compile the arsheos_server.livecode stack as a standalone runtime application targeting your server's operating system.<br />
6.- Launch the arsheos_server in graphical mode and edit the arsheos_service.livecode stack script to adapt the local paths, your active db platform choice (postgresql or mysql) and db user:password to your own configuration in updating them in the arsheos_service.livecode stack.<br />
7.- Open your ../openresty/nginx/conf/ directory and update your nginx.conf file in picking up the adequate citalis and postgresql configuration setups available inside the nginx.conf file we provide you.<br />
8.- Add the nginx_lcsrv.conf sockets and load-balancing proxy file to your ../openresty/nginx/conf/ directory and don't forget to reference it in your nginx.conf file. Adapt your nginx_lcsrv.conf file to your own configuration set-up (one or multiple arsheos application's server instances binded with a diffrent TCP port for each one if more than one is handled) and don't forget to restart nginx to let him takes all the upon configuration changes in account.<br />
9.- Set-up the /etc/init.d arsheos server startup item aimed to get it automaticaly started up at boot time.<br />
10.- Start your arsheos application's server from Bash or in rebooting your server.

Just type the http(s)://yoursite/path-to-citalis.lls nginx handled virtual location url to connect your new installed citalis app portal and have fun.

NB: For general reliability (WAF configuration, speed, resources economy), the Citalis-Livecode-AS is aimed to be set to run behind NGINX or OpenResty and LuaJIT. It's, indeed, well suited to run behind Apache2, OpenLiteSpeed or Ligthttpd as long as the Lua or PHP modules are avalaible to the Livecode TCP application's server to set-up the sockets and, if expected, round-robin load-balancing proxy script. The citalis.php and citalis_php.html are provided as the nginx_lcsrv.conf and citalis_lls.html replacement files in case of need. They have to be installed in the Citalis app webroot directory. 

Be aware that the PHP setup will slowdown the solution from 15 to 25% in comparison to the LuaJIT recommanded option to go, in regard to the different httpd servers used instead of NGINX / OpenResty.
