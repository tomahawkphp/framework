<?php

namespace Tomahawk\Encryption;

interface CryptInterface
{
    /**
     * @param $value
     * @return mixed
     */
    public function encrypt($value);

    /**
     * @param $value
     * @return mixed
     */
    public function decrypt($value);

    /**
     * @param int $block_length
     */
    public function setBlockLength($block_length);

    /**
     * @return int
     */
    public function getBlockLength();

    /**
     * @param string $mode
     */
    public function setMode($mode);

    /**
     * @return string
     */
    public function getMode();
}
