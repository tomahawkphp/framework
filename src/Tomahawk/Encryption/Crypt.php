<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Encryption;

use \Crypt_Rijndael;
use Symfony\Component\Security\Core\Util\StringUtils;

class Crypt implements CryptInterface
{
    /**
     * The mode used for encryption.
     *
     * @var string
     */
    protected $mode;

    /**
     * The block length of the cipher.
     *
     * @var int
     */
    protected $block_length;

    /**
     * @var object
     */
    protected $cipher;

    /**
     * @var string
     */
    protected $key;

    public function __construct($key, $mode = MCRYPT_MODE_CBC, $block_length = 256)
    {
        $this->key = $key;
        $this->block_length = $block_length;
        $this->mode = $mode;
        $this->cipher = new Crypt_Rijndael($this->mode);
        $this->cipher->setKey($key);
        $this->cipher->setBlockLength($this->block_length);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function encrypt($value)
    {
        $value = $this->cipher->encrypt($value);
        return $this->safeB64encode($this->addHmac($value));
    }

    /**
     * @param $value
     * @return mixed
     */
    public function decrypt($value)
    {
        $value = $this->safeB64decode($value);

        if ($value = $this->validateHmac($value)) {
            return $this->cipher->decrypt($value);
        }

        return false;
    }

    /**
     * @param int $block_length
     */
    public function setBlockLength($block_length)
    {
        $this->block_length = $block_length;
    }

    /**
     * @return int
     */
    public function getBlockLength()
    {
        return $this->block_length;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param $value
     * @return mixed|string
     */
    private function safeB64encode($value)
    {
        $data = base64_encode($value);
        $data = str_replace(array('+','/','='), array('-','_',''), $data);
        return $data;
    }

    /**
     * @param $value
     * @return string
     */
    private function safeB64decode($value)
    {
        $data = str_replace(array('-','_'), array('+','/'), $value);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * @param $value
     * @return string
     */
    private function addHmac($value)
    {
        $hmac = $this->safeB64encode(hash_hmac('sha256',$value, $this->key, true));
        // append it and return the hmac protected string
        return $value.$hmac;
    }

    /**
     * @param $value
     * @return bool|string
     */
    private function validateHmac($value)
    {
        // strip the hmac-sha256 hash from the value
        $hmac = substr($value, strlen($value)-43);

        // and remove it from the value
        $value = substr($value, 0, strlen($value)-43);
        $hmac1 = hash_hmac('sha256',$value, $this->key, true);

        return StringUtils::equals($this->safeB64encode($hmac1), $hmac) ? $value : false;
    }
}
