<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\{Model, Collection};
use App\Repositories\Repository;

class LoanRepository extends Repository
{
	public function model()
	{
		return 'App\Loan';
	}

	public function resourceModel()
	{
		return 'App\Http\Resources\LoanResource';
	}

	// ------------------------------------------------------------------------

	public function getByUser(Model $user)
	{
		// We could add some criteria here 
		// for example, we want to get the list of loans from
		// this specific user that have no repayments yet

		return $user->loans;
	}

	public function createLoan(array $data, Model $user)
	{
		// Let's validate it
		parent::validator($data, self::validatorConstraints())->validate();

		// As far as I know, some loan management accounting designs
		// when creating new loan, the installment recordset will also be generated
		// automatically. When the user makes a repayment, the design will update
		// the one of installments.
		// 
		// However, as my understanding. Accounting design should be easy to audit, 
		// which means it is much better to make it append-only and not edit any old rows.
		// 
		// So, we just create new loan without any installment recordset.
		// And when the user make a repayment, then the installment/repayment transaction
		// will be created.

		$loan = parent::create($data);
		$user->loans()->save($loan);

		// Trigger some events here
		// ------------------------------------------------------------------------
		
		// event(new LoanEvent());

		// ------------------------------------------------------------------------

		return $loan;
	}

	// ------------------------------------------------------------------------

	protected function validatorConstraints()
    {
        return [
			'amount'          => 'required',
			'arrangement_fee' => 'required',
			'interest_rate'   => 'required',
			'term'            => 'required',
			'frequency'       => 'required',
        ];
    }
}