<?php

namespace App\Repositories;

use OkayBueno\Repositories\RepositoryInterface;

/**
 * Interface BookRepositoryInterface
 * @package App\Repositories
 */
interface BaseRepositoryInterface extends RepositoryInterface
{
    public function update($id, $input);

    public function grid();
}
