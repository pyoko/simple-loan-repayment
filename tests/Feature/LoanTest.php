<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Generator as Faker;
use App\{User, Loan};

class LoanTest extends TestCase
{
    use RefreshDatabase;

    const API_LOAN = '/api/v1/loans/';
    const API_USER = '/api/v1/users/';


    public function testLoanCreateUserNotFound()
    {
        $user     = factory(User::class)->create();
        $token    = $user->generateToken();
        
        $response = $this->json('post', self::API_USER . '99/loans', [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(404);
    }

    public function testLoanCreateSuccess()
    {
        $user    = factory(User::class)->create();
        $token   = $user->generateToken();
        
        $newLoan = [
            'amount'          => 5000,
            'arrangement_fee' => 10,
            'interest_rate'   => 5.96,
            'term'            => 12,
            'frequency'       => 'fortnightly',
        ];

        $response = $this->json('post', self::API_USER . $user->id . '/loans', $newLoan, [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'id',
                    'amount',
                    'arrangement_fee',
                    'interest_rate',
                    'term',
                    'frequency',
                 ]);
    }

    public function testLoanCreateRequiresAll()
    {
        $user     = factory(User::class)->create();
        $token    = $user->generateToken();
        
        $response = $this->json('post', self::API_USER . $user->id . '/loans', [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(422)
                 ->assertJson([
                    'errors' => [
                        'amount'          => ['The amount field is required.'],
                        'arrangement_fee' => ['The arrangement fee field is required.'],
                        'interest_rate'   => ['The interest rate field is required.'],
                        'term'            => ['The term field is required.'],
                        'frequency'       => ['The frequency field is required.'],
                    ],
                 ]);
    }

    public function testLoanGet()
    {
        $user  = factory(User::class)->create();
        $token = $user->generateToken();
        $loan  = factory(Loan::class)->create();
        $user->loans()->save($loan);

        $response = $this->json('get', self::API_LOAN . $loan->id, [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'id',
                    'amount',
                    'arrangement_fee',
                    'interest_rate',
                    'term',
                    'frequency',
                 ]);
    }

    public function testLoanGetByUser()
    {
        $user  = factory(User::class)->create();
        $token = $user->generateToken();
        $loan  = factory(Loan::class)->create();
        $user->loans()->save($loan);

        $response = $this->json('get', self::API_USER . $user->id . '/loans', [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    '*' => ['id', 'amount', 'arrangement_fee', 'interest_rate', 'term', 'frequency',],
                 ]);
    }

    public function testLoanDelete()
    {
        $user  = factory(User::class)->create();
        $token = $user->generateToken();
        $loan  = factory(Loan::class)->create();
        $user->loans()->save($loan);

        $response = $this->json('delete', self::API_LOAN . $loan->id, [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(204);

        $response = $this->json('get', self::API_LOAN . $loan->id, [], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(404);
    }
}
