[![SensioLabsInsight](https://insight.sensiolabs.com/projects/85348a82-45a1-4ab4-ab32-62eddef3d82f/small.png)](https://insight.sensiolabs.com/projects/85348a82-45a1-4ab4-ab32-62eddef3d82f)
![TravisCI](https://travis-ci.org/tomahawkphp/framework.svg?branch=master)

TomahawkPHP Framework
-----------------

TomahawkPHP is a PHP 5.3 full-stack web framework built on top of the Symfony2 Components.

Please note: although it will work be PHP as low as 5.3 we recommend 5.5 and up. 

Requirements
------------

TomahawkPHP is only supported on PHP 5.3.3 and up.

Be warned that PHP versions before 5.3.8 are known to be buggy and might not
work for you:

 * before PHP 5.3.4, if you get "Notice: Trying to get property of
   non-object", you've hit a known PHP bug (see
   https://bugs.php.net/bug.php?id=52083 and
   https://bugs.php.net/bug.php?id=50027);

 * before PHP 5.3.8, if you get an error involving annotations, you've hit a
   known PHP bug (see https://bugs.php.net/bug.php?id=55156).

 * PHP 5.3.16 has a major bug in the Reflection subsystem and is not suitable to
   run TomahawkPHP and Symfony2 Components (https://bugs.php.net/bug.php?id=62715)
