<?php

namespace App\Repositories\Eloquent;

use OkayBueno\Repositories\src\EloquentRepository;
use App\Models\User;
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

    public function updateBy(array $data, $value = null, $field = 'id')
    {
        if (!isset($data['password'])) {
            return parent::updateBy($data, $value, $field);
        }
        $original_fields = $this->model->getFillable();
        $fillable_fields = $this->model->getFillable();
        $fillable_fields[] = 'password';
        $this->model->fillable($fillable_fields);
        $data['password'] = bcrypt($data['password']);
        $created_user = parent::updateBy($data, $value, $field);
        $this->model->fillable($original_fields);
        return $created_user;
    }

    public function create(array $data)
    {
        if (!isset($data['password'])) {
            $data['password'] = Str::random(16);
        }
        $original_fields = $this->model->getFillable();
        $fillable_fields = $this->model->getFillable();
        $fillable_fields[] = 'password';
        $this->model->fillable($fillable_fields);
        $data['password'] = bcrypt($data['password']);
        $created_user = parent::create($data);
        $this->model->fillable($original_fields);
        return $created_user;
    }
}
