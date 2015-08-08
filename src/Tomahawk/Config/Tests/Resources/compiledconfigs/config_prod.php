<?php

return array(
'auth' => array (
    'driver' => 'eloquent',
    'username' => 'email',
    'password' => 'password'
),
'session' => array (
    'driver' => 'database',
    'name'   => 'tomahawk_session'
),
);