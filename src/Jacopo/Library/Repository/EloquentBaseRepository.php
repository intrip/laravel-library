<?php namespace Jacopo\Library\Repository;
/**
 * Class EloquentBaseRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Jacopo\Library\Repository\Interfaces\BaseRepositoryInterface;
use Event;

class EloquentBaseRepository implements BaseRepositoryInterface
{
    /**
     * The name of the model: needs to be eloquent model
     * @var String
     */
    protected $model_name;

    public function __construct($model_name = null)
    {
        if($model_name) $this->model_name = $model_name;
    }

    /**
     * Create a new object
     *
     * @return mixed
     */
    public function create(array $data)
    {
        $model = $this->model_name;
        return $model::create($data);
    }

    /**
     * Update a new object
     * @param       id
     * @param array $data
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id, array $data)
    {
        $obj = $this->find($id);
        Event::fire('repository.updating', [$obj]);
        $obj->update($data);
        return $obj;
    }

    /**
     * Deletes a new object
     * @param $id
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete($id)
    {
        $obj = $this->find($id);
        Event::fire('repository.deleting', [$obj]);
        return $obj->delete();
    }

    /**
     * Find a model by his id
     * @param $id
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        $model = $this->model_name;
        return $model::findOrFail($id);
    }

    /**
     * Obtains all models
     * @return mixed
     */
    public function all()
    {
        $model = $this->model_name;
        return $model::all();
    }
} 