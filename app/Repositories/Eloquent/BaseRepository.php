<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\MessageBag;
use OkayBueno\Repositories\src\EloquentRepository;
use App\Book;
use App\Repositories\BookRepositoryInterface;

/**
 * Class BookRepository
 * @package App\Repositories\Eloquent
 */
class BaseRepository extends EloquentRepository implements BookRepositoryInterface
{

    /**
     * @param Book $model
     */
    public function __construct(Book $model)
    {
        parent::__construct($model);
        $this->messages = new MessageBag();
    }

    public function grid()
    {
        return new Book();
    }

    public function createModel()
    {
        return new Book();
    }

    public function store($resource_id, $input)
    {
        return $resource_id ? $this->update($resource_id, $input) : $this->create($input);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // Data handler 
        $input = $this->cleanUnfillableFields($data);

        // Validator

        // Execution

        // Event being created
        $model = $this->createModel()->fill($input)->save();

        // Event created
        $this->resetScope();
        $messages = $this->messages;
        return [$messages, $model];
    }

    /**
     * @param array $data
     * @param $value
     * @param string $field
     * @return mixed
     */
    public function update($id, array $data)
    {
        $input = $this->cleanUnfillableFields($data);
        $model = $this->model->find($id);
        $model->fill($input)->save();
        $this->resetScope();
        $messages = $this->messages;
        return [$messages, $model];
    }
}
