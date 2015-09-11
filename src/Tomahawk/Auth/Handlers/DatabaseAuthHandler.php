<?php

namespace Tomahawk\Auth\Handlers;

use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\User;
use Tomahawk\Auth\UserInterface;
use Tomahawk\Hashing\HasherInterface;
use Illuminate\Database\ConnectionInterface;

class DatabaseAuthHandler implements AuthHandlerInterface
{
    protected $connection;
    /**
     * @var \Tomahawk\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $passwordField;

    public function __construct(
        HasherInterface $hasher,
        ConnectionInterface $connection,
        $table,
        $key,
        $passwordField
    )
    {
        $this->hasher = $hasher;
        $this->connection = $connection;
        $this->table = $table;
        $this->key = $key;
        $this->passwordField = $passwordField;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        $result = $this->getConnection()
            ->table($this->table)
            ->where($this->key, '=', $identifier)
            ->first();

        if ($result) {
            $user = new User((array)$result);
            $user->setPrimaryKey($this->key);
            $user->setPasswordField($this->passwordField);
            return $user;
        }

        return null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->getConnection()
            ->table($this->table);

        foreach ($credentials as $key => $value) {
            if ($key !== $this->passwordField) {
                $query->where($key, $value);
            }
        }

        $result = $query->first();

        if ($result) {
            $user = new User((array)$result);
            $user->setPrimaryKey($this->key);
            $user->setPasswordField($this->passwordField);
            return $user;
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Tomahawk\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        $plain = $credentials[$this->passwordField];
        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
