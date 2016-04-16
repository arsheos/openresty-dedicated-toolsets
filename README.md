# openresty-dedicated-toolsets : Citalis-LuaJIT
Citalis-LuaJIT (OpenResty, Redis, PostgreSQL version)

Note 1: This LuaJIT version of the Citalis portal will not run without notable changes on top of the Apache2, OpenLiteSpeed or Lighttpd platforms as long as it's using the native NGINX PostgreSQL, secure and persistant, upstream pool and driver way to go.

Note 2: Used without the help of a passive cache server (Redis2, memcached, Tarantool, etc...), the Citalis-LuaJIT version of the Citalis portal won't work as fast as it's expected to do when one of such cache servers is installed (15 X faster). It's the normal contre-party of the way the LuaJIT is running here in a simple FASTCGI mode (we are actively working on a LuaJIT TCP application's server version able to remove this performance limitation).

Installation :

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.<br />
2.- Drag n'Drop the 40x.html, 50x.html and citalis_orc.html files to the same place.<br />
3.- Add the arsheos.js file to the /assets/js/ subdirectory.<br />
4.- Add the arsheos.css file to the /assets/css/ subdirectory.<br />
5.- Use your prefered mysql/mariadb managment tool (phpPgAdmin, etc...) to add the citalis db (one table) on your pg server.<br />
6.- Adapt all the local paths and db user:password to your own configuration in updating them in the citalis.lc file.<br />
7.- Open your ../openresty/nginx/conf/ directory and update your nginx.conf file in picking up the adequate citalis and postgresql configuration setups available inside the nginx.conf file we provide you.<br />
8.- Install the Redis2 cache server suited to your Linux distribution (memcached can be used as a reliable alternative instead).<br />
9.- Add the nginx_citalis.conf file to your ../openresty/nginx/conf/ directory and don't forget to reference it inside your nginx.conf file.<br />
10.- Add the nginx_citalis.lua application's file to your ../openresty/nginx/ directory and don't forget to restart nginx to let him takes all the upon configuration changes in account.<br />

Just type the http(s)://yoursite/path-to-citalis.orc nginx handled virtual location url to connect your new installed citalis app portal and have fun.
