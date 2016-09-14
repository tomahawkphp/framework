<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Session;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Tomahawk\Session\Bag\OldInputBag;
use Tomahawk\Session\Bag\OldInputBagInterface;

class Session extends SymfonySession implements SessionInterface
{
    /**
     * @var string
     */
    private $oldInputName;

    /**
     * Constructor.
     *
     * @param SessionStorageInterface $storage A SessionStorageInterface instance.
     * @param AttributeBagInterface $attributes An AttributeBagInterface instance, (defaults null for default AttributeBag)
     * @param FlashBagInterface $flashes A FlashBagInterface instance (defaults null for default FlashBag)
     * @param OldInputBagInterface $oldInput
     */
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null, OldInputBagInterface $oldInput = null)
    {
        parent::__construct($storage, $attributes, $flashes);

        $oldInput = $oldInput ?: new OldInputBag();
        $this->oldInputName = $oldInput->getName();
        parent::registerBag($oldInput);
    }

    /**
     * Get Old Input
     *
     * @return OldInputBagInterface
     */
    public function getOldInputBag()
    {
        return $this->getBag($this->oldInputName);
    }
}
