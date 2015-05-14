Allt om...
===========

Forum site, buildt using Anax-MVC, intended as school-assignment.

### To use:
* Clone: `git clone https://github.com/icedoe/allt-om.git`
* Edit top of app/config/config_mysql_template.php, and rename without _template.
* If on linux: uncomment and edit line beginning with Rewrite base in webroot/.htaccess.
* Point browser to webroot/dbsetup.php.
* Remove dbsetup.php from server for safety.


By Martin Degerman



License
------------------

This software is free software and carries an MIT license.



Use of external libraries
-----------------------------------

The following external modules are included and subject to its own license.



### Anax-MVC
* Website: https://github.com/mosbth/Anax-MVC
* Version: 2.0.4
* License: MIT license
* Path: All paths lead to Anax



### CDatabase
* Website https://github.com/mosbth/cdatabase
* Version: 0.1.x
* Licence: MIT license
* Path: included in `vendor/mos/cdatabase`



### CForm
* Website: https://github.com/mosbth/cform
* Version: 1.9.x
* License: MIT license
* Path: included in `vendor/mos/cform`




### Modernizr
* Website: http://modernizr.com/
* Version: 2.6.2
* License: MIT license
* Path: included in `webroot/js/modernizr.js`



### PHP Markdown
* Website: http://michelf.ca/projects/php-markdown/
* Version: 1.4.0, November 29, 2013
* License: PHP Markdown Lib Copyright © 2004-2013 Michel Fortin http://michelf.ca/
* Path: included in `3pp/php-markdown`




copyright 2015 Martin Degerman (icedoe@live.se)