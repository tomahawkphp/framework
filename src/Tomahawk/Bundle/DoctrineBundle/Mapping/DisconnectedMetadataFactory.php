<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The is based on code originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Mapping;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DisconnectedMetadataFactory extends MetadataFactory
{
    /**
     * @return string
     */
    protected function getClassMetadataFactoryClass()
    {
        return 'Doctrine\\ORM\\Tools\\DisconnectedClassMetadataFactory';
    }
}
