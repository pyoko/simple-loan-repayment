<?php

namespace App\Repositories;

use Illuminate\Support\Facades\{Hash, Validator};
use Illuminate\Database\Eloquent\{Model, Collection};
use Illuminate\Container\Container as App;
use App\Repositories\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
	private $app;
	protected $model;


	abstract function model();
	abstract function resourceModel();

	public function __construct(App $app)
	{
		$this->app = $app;
		$this->makeModel();
	}

	// ------------------------------------------------------------------------

	public function all(array $columns = ['*'])
	{
		return $this->model->get($columns);
	}

	public function paginate(int $perPage = 15, array $columns = ['*'])
	{
		return $this->model->paginate($perPage, $columns);
	}

	public function create(array $data)
	{
		return $this->model->create($data);
	}

	public function update(array $data, $modelOrId)
	{
		if ($modelOrId instanceof Model) {
			$modelOrId->update($data);
			return $modelOrId;
		}

		$this->model->where('id', $modelOrId)->update($data);

		return $this->findBy('id', $modelOrId);
	}

	public function delete($modelOrId)
	{
		if ($modelOrId instanceof Model) {
			return $modelOrId->delete();
		}

		return $this->model->destroy($id);
	}

	public function find(int $id, array $columns = ['*'])
	{
		return $this->model->find($id, $columns);
	}

	public function findBy(string $field, string $value, $columns = ['*'])
	{
		return $this->model->where($field, '=', $value)->first($columns);
	}

	// ------------------------------------------------------------------------

	public function resource(Model $model)
	{
		$resource = $this->resourceModel();
		return new $resource($model);
	}

	public function resourceCollection(Collection $collection)
	{
		return $this->resourceModel()::collection($collection);
	}

	protected function makeModel()
	{
		$model = $this->app->make($this->model());
		if (! $model instanceof Model) {
			throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
		}

		return $this->model = $model;
	}

	// ------------------------------------------------------------------------
	
	public function validator(array $data = [], array $constraints = [])
	{
		return Validator::make($data, $constraints);
	}
}