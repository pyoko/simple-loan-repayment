<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\{Model, Collection};

interface RepositoryInterface
{
	# Query Builder
	function all(array $columns = ['*']);
	function paginate(int $perPage = 15, array $columns = ['*']);
	function create(array $data);
	function update(array $data, $modelOrId);
	function delete($modelOrId);
	function find(int $id, array $columns = ['*']);
	function findBy(string $field, string $value, $columns = ['*']);

	# Eloquent Resource
	function resource(Model $model);
	function resourceCollection(Collection $collection);

	# Validator
	# Illuminate\Support\Facades\Validator
	function validator(array $data, array $constraints);
}