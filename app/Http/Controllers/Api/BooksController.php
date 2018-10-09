<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api;
use App\Http\Controllers\CRUDController;
use App\Repositories\BookRepositoryInterface;
use App\Transformers\BookTransformer;

class BooksController extends CRUDController
{
    public function __construct(BookRepositoryInterface $books, BookTransformer $Tbook)
    {
        $this->repository = $books;
        $this->transformer = $Tbook;
        parent::__construct();
    }
}
