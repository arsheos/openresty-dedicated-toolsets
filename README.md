# openresty-dedicated-toolsets : Citalis-LuaJIT
Citalis-LuaJIT (OpenResty, Redis, PostgreSQL version)

Note: This LuaJIT version of the Citalis portal will not run without notable changes on top of the Apache2, OpenLiteSpeed and Lighttpd platforms as long as it's using the native NGINX PostgreSQL, secure and persistant, upstream pool and driver way to go.

Installation :

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.
2.- Drag n'Drop the 40x.html, 50x.html, citalis_lc.html and citalis.lc files to the same place.
3.- Add the arsheos.js file to the /assets/js/ subdirectory.
4.- Add the arsheos.css file to the /assets/css/ subdirectory.
5.- Use your prefered mysql/mariadb managment tool (phpMyAdmin, etc...) to add the citalis db (one table) on your db server.
6.- Adapt all the local paths and db user:password to your own configuration in updating them in the citalis.lc file.

Just type the http(s)://yoursite/path-to-citalis.lc right url to connect your new installed citalis app portal and have fun.
