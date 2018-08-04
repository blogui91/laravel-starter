<?php

namespace App\Repositories\Eloquent;

use OkayBueno\Repositories\src\EloquentRepository;
use App\User;
use App\Repositories\UserRepositoryInterface;

/**
 * Class UserRepository
 * @package App\Repositories\Eloquent
 */
class UserRepository extends EloquentRepository implements UserRepositoryInterface
{

    /**
     * @param User $model
     */
    public function __construct( User $model )
    {
        parent::__construct( $model );
    }

}