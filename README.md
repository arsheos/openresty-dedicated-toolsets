# openresty-dedicated-toolsets
Saas, RIA and web applications toolsets using mainly OpenResty, Redis and PostgreSQL

This repertory contains three subdirectories aimed to presents three ways to build very lightweight web applications in using OpenResty and Lua, OpenResty and a Livecode-based TCP application server and OpenLiteSpeed and the regular Livecode CGI server provided to its customers by the Livecode Ltd Corp. Both of those apps provide the same service : the Citalis sentences library consultation portal. The underlaying technical solutions presents some differences indeed, mainly in performance terms.

The Citalis-LuaJIT solution provide the most suitable way to scale up such kind of solutions in a very affordable way. In using LuaJIT (fastCGI) + Redis2 as the passive cache layer of the solution, the performance average permits to handle 34 X more requests peer seconds than a more simplest way to go (CGI).

The Citalis-LiveCode-AS solution provide a second reliable way to handle such kind of solutions in a less affordable way. In using a Livecode standalone and service stack to empower the solution, the performance average vary from 15 X to 100 X in regard to a more simplest way to go (CGI). The lowest performance (15 X) have to be attendee each time a request will have to handle dynamic charsets conversions on the fly (UTF-8 to Latin-1 or vice-versa, as an example) while against 100% UTF-8 data sources, this Livecode TCP application server approch will performs very well (100 X) in getting advantage of its ability to cache redundent requests in RAM stored hash-tables.

The Citalis-LiveCode-CGI solution provided there too for AB-testing needs only presents the advantage to show how the same code base as the one used in Citalis-LiveCode-AS solution runs in a lots slower way by design in a CGI magnitude context.

Before installing one or both of those solutions for any testing or development needs, be aware that the presentation's layer and user interface rely on the very elegant and lightweight Parallelism JQuery/CSS3 template, freely available from http://html5up.net/parallelism for any use as long as the copyright notice remains in place. Don't forget to download and install it in the Citalis app root directory before going further.

Please refer to each dedicated README.md files available inside the different subdirectories for installation details.
