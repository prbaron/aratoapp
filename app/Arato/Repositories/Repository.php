<?php

namespace Arato\Repositories;

use Arato\utils\PostValidator;
use Illuminate\Database\Eloquent\Model;
use models\enum\Action;

abstract class Repository
{
    protected $defaultLimit = 20;
    protected $defaultSort = 'id';
    protected $defaultOrder = 'desc';

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public abstract function filter(Array $filters);

    public function isValidForCreation($type, Array $data)
    {
        $user = new $type();
        $validated = $user->validate($data, Action::CREATION);

        $object = new PostValidator($validated, $user->errors());

        return $object;
    }

    public function isValidForUpdate($type, Array $data)
    {
        $user = new $type();
        $validated = $user->validate($data, Action::UPDATE);

        $object = new PostValidator($validated, $user->errors());

        return $object;
    }


    public function all()
    {
        return $this->model->all()->toArray();
    }

    public function allWith(array $with)
    {
        return $this->model->with($with)->get()->toArray();
    }

    public function create($inputs)
    {
        return $this->model->create($inputs);
    }

    public function update($id, $input)
    {
        $updated = $this->model->find($id)->update($input);

        if ($updated) {
            $resource = $this->model->find($id);
            $resource->touch();

            return $resource;
        }

        return null;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            return $model;
        }

        return null;
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}