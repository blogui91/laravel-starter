<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Book;
use App\Repositories\BookRepositoryInterface;

/**
 * Class BookRepository
 * @package App\Repositories\Eloquent
 */
class BookRepository extends BaseRepository implements BookRepositoryInterface
{

    /**
     * @param Book $model
     */
    public function __construct( Book $model )
    {
        parent::__construct( $model );
    }

    public function grid()
    {
        return new Book();
    }
}
