TomahawkPHP Framework
-----------------

TomahawkPHP is a PHP 5.3 full-stack web framework built on top of the Symfony2 Components.

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
   run Symfony2 (https://bugs.php.net/bug.php?id=62715)
