<?php

namespace App\Repositories;

use Illuminate\Support\Facades\{Hash};
use Illuminate\Database\Eloquent\{Model, Collection};
use App\Repositories\Repository;

class UserRepository extends Repository
{
	public function model()
	{
		return 'App\User';
	}

	public function resourceModel()
	{
		return 'App\Http\Resources\UserResource';
	}

	// ------------------------------------------------------------------------
	
	public function all(array $columns = ['*'])
	{
		// We could add some criteria here
		// for example, get the list of users 
		// that have uncompleted loans

		return parent::all($columns);	
	}

	public function update(array $data, $modelOrId)
	{
		$this->validator($data);

		return parent::update($data, $modelOrId);
	}

	public function delete($modelOrId)
	{
		// we can do something here before 
		// destorying the model

		return parent::delete($modelOrId);
	}

	// ------------------------------------------------------------------------

	protected function validatorConstraints(array $data)
    {
        // we can do some validations here 
        // to make sure whether the data are valid
    }
}