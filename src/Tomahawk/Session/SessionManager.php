<?php

namespace Tomahawk\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Tomahawk\Session\InputOldBag;

class SessionManager implements SessionInterface
{

    protected $metadataBag;
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    public function __construct(array $config)
    {
        switch($config['session_type'])
        {
            case 'cookie':
                $this->setupCookie($config);
                break;
            case 'file':
                $this->setupFile($config);
                break;
            case 'database':
                $this->setupDatabase($config);
                break;
            case 'array':
                $this->setupArray($config);
                break;
        }

        $inputOldBag = new InputOldBag('_input_old');
        $inputOldBag->setName('tomahawk_input_old');
        $this->session->registerBag($inputOldBag);

        // PHP Sessions auto start in 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE !== session_status()) {
            $this->session->start();
        }
    }

    /**
     * @return \Tomahawk\Session\InputOldBag
     */
    public function getOldBag()
    {
        return $this->session->getBag('tomahawk_input_old');
    }

    public function setOld($name, $value)
    {
        $this->getOldBag()->set($name, $value);
    }

    public function set($name, $value)
    {
        $this->session->set($name, $value);
    }

    public function get($name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    public function has($name)
    {
        return $this->session->has($name);
    }

    public function remove($name)
    {
        $this->session->remove($name);
    }

    public function hasFlash($name)
    {
        return $this->getFlashBag()->has($name);
    }

    public function getFlash($name)
    {
        return $this->getFlashBag()->get($name);
    }

    public function setFlash($name, $value)
    {
        $this->getFlashBag()->set($name, $value);

        return $this;
    }

    public function getFlashBag()
    {
        return $this->session->getFlashBag();
    }

    public function save()
    {
        $this->session->save();

        return $this;
    }

    protected function setupDatabase($config)
    {

        $pdo = null;

        $pdoSessionHandler = new PdoSessionHandler($pdo, array(
            'db_table'    => 'tbl_session',
            'db_id_col'   => 'session_id',
            'db_data_col' => 'session_data',
            'db_time_col' => 'session_timestamp',
        ));

        $session_settings = array(
            'id'   	   => $config['session_name'],
            'name' 	   => $config['cookie_name'],
            'lifetime' => $config['cookie_lifetime'],
            'path'     => $config['cookie_path'],
            'domain'   => $config['cookie_domain'],
            'secure'   => $config['cookie_secure'],
            'httponly' => $config['cookie_http_only'],
        );

        $storage = new NativeSessionStorage($session_settings, $pdoSessionHandler);
        $session = new Session($storage);

        $this->session = $session;
    }

    protected function setupFile($config)
    {
        $nativeFileSessionHandler = new NativeFileSessionHandler($config['save_path']);

        $storage = new NativeSessionStorage(array(), $nativeFileSessionHandler);
        $session = new Session($storage);

        $this->session = $session;
    }

    protected function setupCookie($config)
    {
        $session_settings = array(
            'id'   	   => $config['session_name'],
            'name' 	   => $config['cookie_name'],
            'lifetime' => $config['cookie_lifetime'],
            'path'     => $config['cookie_path'],
            'domain'   => $config['cookie_domain'],
            'secure'   => $config['cookie_secure'],
            'httponly' => $config['cookie_http_only'],
        );

        $storage = new NativeSessionStorage($session_settings);
        $session = new Session($storage);

        $this->session = $session;
    }

    protected function setupArray($config)
    {
        $storage = new MockArraySessionStorage($config['session_name']);
        $session = new Session($storage);

        $this->session = $session;
    }

}