<?php

namespace Tomahawk\Auth;

interface UserInterface
{
    /**
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * @return mixed
     */
    public function getAuthPassword();
}