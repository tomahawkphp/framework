<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MigrationsBundle\Migrator;

use Illuminate\Database\Schema\Builder;

interface MigrationInterface
{
    /**
     * Migration up command
     *
     * @param Builder $schema
     * @return mixed
     */
    public function up(Builder $schema);

    /**
     * Migration down command
     *
     * @param Builder $schema
     * @return mixed
     */
    public function down(Builder $schema);
}