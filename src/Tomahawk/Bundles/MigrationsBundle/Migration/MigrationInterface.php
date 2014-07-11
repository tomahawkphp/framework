<?php

namespace Tomahawk\Bundles\MigrationsBundle\Migration;

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