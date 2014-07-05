<?php

namespace Tomahawk\Auth\Handlers;

use PDO;
use Tomahawk\Auth\AuthHandlerInterface;
use Tomahawk\Auth\UserInterface;
use Tomahawk\Hashing\HasherInterface;

class PdoAuthHandler implements AuthHandlerInterface
{
    /**
     * @var \Tomahawk\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * @var
     */
    protected $table;

    /**
     * @var
     */
    protected $id;

    /**
     * @var \PDO
     */
    protected $connection;

    public function __construct(HasherInterface $hasher, PDO $connection, $table, $id)
    {
        $this->hasher = $hasher;
        $this->table = $table;
        $this->id = $id;
        $this->connection = $connection;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        $stmt = $this->connection->prepare(sprintf('SELECT * FROM %s WHERE %s = :id', $this->table, $this->id));

        //$result = $stmt->b

        //return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Tomahawk\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value)
        {
            if ( ! str_contains($key, 'password')) $query->where($key, $value);
        }

        return $query->first();
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
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
