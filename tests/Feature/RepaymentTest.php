<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Generator as Faker;
use App\{User, Loan, Repayment};

class RepaymentTest extends TestCase
{
	use RefreshDatabase;

	const API_LOAN      = '/api/v1/loans/';
	const API_REPAYMENT = '/api/v1/repayments/';


	public function testRepaymentCreateLoanNotFound()
	{
		$user     = factory(User::class)->create();
		$token    = $user->generateToken();

		$response = $this->json('post', self::API_LOAN . '99/repayments', [], [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(404);
	}

	public function testRepaymentCreateUserNotFound()
	{
		$user  = factory(User::class)->create();
		$token = $user->generateToken();
		$loan  = factory(Loan::class)->create();

		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', [], [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(404);
	}

	public function testRepaymentCreateRequiresAll()
	{
		$user  = factory(User::class)->create();
		$token = $user->generateToken();
		$loan  = factory(Loan::class)->create();
		$user->loans()->save($loan);

		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', [], [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(422)
				 ->assertJson([
					'error' => 'The given data was invalid.',
				 ]);
	}

	public function testRepaymentCreateWhenLoanIsCompleted()
	{
		$user = factory(User::class)->create();
		$loan = factory(Loan::class)->create([
			'is_completed' => true
		]);
		$user->loans()->save($loan);
		$token = $user->generateToken();

		$installmentAmount = (($loan->amount + $loan->arrangement_fee) / $loan->term) * ($loan->interest_rate / 100);
		$repayment         = [
			'payment_amount' => $installmentAmount
		];
		
		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', $repayment, [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(422);
	}

	public function testRepaymentSuccess()
	{
		$user = factory(User::class)->create();
		$loan = factory(Loan::class)->create([
			'amount'          => 5000,
			'arrangement_fee' => 0,
			'interest_rate'   => 5.9,
			'term'            => 12,
		]);
		$user->loans()->save($loan);
		$token = $user->generateToken();

		$installmentAmount = (($loan->amount + $loan->arrangement_fee) / $loan->term) + 
                             (($loan->amount + $loan->arrangement_fee) / $loan->term) * ($loan->interest_rate / 100);
		$repayment         = [
			'payment_amount' => $installmentAmount
		];

		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', $repayment, [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(200);
	}

	public function testRepaymentCreateWhenPaidTooMuch()
	{
		$user = factory(User::class)->create();
		$loan = factory(Loan::class)->create([
			'amount'          => 5000,
			'arrangement_fee' => 0,
			'interest_rate'   => 5.9,
			'term'            => 12,
		]);
		$user->loans()->save($loan);
		$token = $user->generateToken();

		$installmentAmount = (($loan->amount + $loan->arrangement_fee) / $loan->term) + 
                             (($loan->amount + $loan->arrangement_fee) / $loan->term) * ($loan->interest_rate / 100);
		$repayment         = [
			'payment_amount' => $installmentAmount
		];

		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', $repayment, [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(200);


		$repayment         = [
			'payment_amount' => 5000
		];	
		$response = $this->json('post', self::API_LOAN . $loan->id . '/repayments', $repayment, [
			'Authorization' => "Bearer $token"
		]);
		$response->assertStatus(422);
	}
}