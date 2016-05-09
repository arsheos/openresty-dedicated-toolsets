# openresty-dedicated-toolsets
Saas, RIA and web applications toolsets using mainly OpenResty, Redis and PostgreSQL

This repository contains three branches subdirectories aimed to presents different ways to build lightweight web applications in using OpenResty and Lua, OpenResty and a Livecode-based TCP application server and OpenLiteSpeed and the regular Livecode CGI server in its stock's version as provided to customers by the Livecode Ltd Corp. Both of those apps provides the same service : the Citalis sentences library consultation portal. The underlaying technical solutions presents some differences indeed, mainly in performance terms.

1.- The Citalis-LuaJIT solution provide the most suitable way to scale up such kind of solutions in a very affordable way. In using LuaJIT (there in FastCGI mode) + Redis2 as the passive cache layer, the performance average permits to handle with a perfect predictible regularity 34 X more requests peer seconds than a more simplest way to go (CGI).

2.- The Citalis-LiveCode-AS solution provide a second reliable way to handle such kind of solutions in a less affordable way. In using a Livecode standalone and service stack to empower the solution, the performance average vary from 15 X to 100 X in regard to a more simplest way to go (CGI). The lowest performance (15 X) have to be attendee each time a request will have to handle dynamic charsets conversions on the fly (UTF-8 to Latin-1 or vice-versa, as an example) while against full-native UTF-8 data sources, this Livecode TCP application server approch will performs very well (100 X) in getting advantage of its ability to cache redundent requests in a RAM stored hash-table.

3.- The Citalis-LiveCode-CGI solution - provided there for AB-testing needs only - presents the advantage to show how the same code base used in Citalis-LiveCode-AS solution runs in a lots slower way by design in a CGI magnitude context. For practical reasons, it's provided there in its OpenLiteSpeed / Livecode CGI server / MariaDB version as the direct reflector of its online presentation version available at https://www.gnmsaas.com/citalis/citalis.lc .

Because their C end to end coded roots, nope of the above solutions never lies in heap memory managment inconsitancies or lackings alike it's so often the case with JVM based solutions. In case of a big aboundance of concurrent incoming requests, the worst witch can happen will have to do with some slowdown of the responses served back to client's browsers.

Be aware that all the provided material went always installed on Linux-64 powered servers. Any installation done against Windows, Mac OS X, BSD or Solaris based platforms won't probably not run out of the box without adequate adaptations.

Before installing one or both of those solutions for any testing or development needs, be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the HTML5 UP copyright notice remains in place without any changes. Don't forget to download and install it in the Citalis app webroot directory before going further.

Please refer to each dedicated README.md file available inside the different subdirectories for installation instructions.
