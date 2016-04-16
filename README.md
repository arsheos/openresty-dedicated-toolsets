# openresty-dedicated-toolsets : Livecode-CGI
Citalis-Livecode-CGI (OpenLiteSpeed, Livecode CGI Server, MariaDB version)

Note: This CGI version of the Citalis portal will runs without any changes needs on top of the Apache2, OpenResty and Lighttpd platforms as long as the Livecode CGI server middleware components will be installed as expected on each of the targeted platforms.

Installation :

Be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

1.- Download and unzip the Parallelism JQuery/CSS3 template in your Citalis app webroot directory.</br >
2.- Drag n'Drop the 40x.html, 50x.html, citalis_lc.html and citalis.lc files to the same place.</br >
3.- Add the arsheos.js file to the /assets/js/ subdirectory.</br >
4.- Add the arsheos.css file to the /assets/css/ subdirectory.</br >
5.- Use your prefered mysql/mariadb managment tool (phpMyAdmin, etc...) to add the citalis db (one table) on your db server.</br >
6.- If needed, download the GPL3 licensed version of the Livecode 7.xx or 8.xx CGI server suited to your server's OS from http://downloads.livecode.com/livecode/ and install it on your server's box.</br >
7.- Adapt all the local paths and db user:password to your own configuration in updating them in the citalis.lc file.

Just type the http(s)://yoursite/path-to-citalis.lc right url to connect your new installed citalis app portal and have fun.


