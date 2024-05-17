<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class LoanControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndex()
    {
        $loan = Loan::create([
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'active',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);

        $response = $this->get('/loans');
        $response->seeStatusCode(200);

        $response->seeJsonStructure([
            '*' => [
                'id',
                'borrower_name',
                'amount',
                'interest_rate',
                'term',
                'status',
                'last_payment_date',
                'total_paid_amount',
                'created_at',
                'updated_at',
                'remaining_amount'
            ]
        ]);

        // Количество записей в ответе совпадает с количеством записей в базе данных
        $this->assertEquals(DB::table('loans')->count(), count(json_decode($response->response->getContent(), true)));
    }

    public function testStore()
    {
        $response = $this->post('/loans', [
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);
        $response->seeStatusCode(201);

        $response->seeJsonStructure([
            'id',
            'borrower_name',
            'amount',
            'interest_rate',
            'term',
            'status',
            'last_payment_date',
            'total_paid_amount',
            'created_at',
            'updated_at',
            'remaining_amount'
        ]);

        $this->assertEquals(DB::table('loans')->count(), 1);

        $this->seeInDatabase('loans', [
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);
    }

    public function testShow()
    {
        $loan = Loan::create([
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);

        $response = $this->get('/loans/' . $loan->id);
        $response->seeStatusCode(200);

        $response->seeJsonStructure([
            'id',
            'borrower_name',
            'amount',
            'interest_rate',
            'term',
            'status',
            'last_payment_date',
            'total_paid_amount',
            'created_at',
            'updated_at',
            'remaining_amount'
        ]);

        $this->assertEquals(DB::table('loans')->count(), 1);

        $this->seeInDatabase('loans', [
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);
    }

    public function testUpdate()
    {
        $loan = Loan::create([
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);

        $response = $this->put('/loans/' . $loan->id, [
            'borrower_name' => 'Igor',
            'amount' => 2000,
            'interest_rate' => 0.2,
            'term' => 24,
        ]);
        $response->seeStatusCode(200);

        $response->seeJsonStructure([
            'id',
            'borrower_name',
            'amount',
            'interest_rate',
            'term',
            'status',
            'last_payment_date',
            'total_paid_amount',
            'created_at',
            'updated_at',
            'remaining_amount'
        ]);
        $response->seeJson([
            'borrower_name' => 'Igor',
            'amount' => 2000,
            'interest_rate' => 0.2,
            'term' => 24,
        ]);

        $this->assertEquals(DB::table('loans')->count(), 1);

        $this->seeInDatabase('loans', [
            'borrower_name' => 'Igor',
            'amount' => 2000,
            'interest_rate' => 0.2,
            'term' => 24,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);
    }

    public function testDestroy()
    {
        $loan = Loan::create([
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);

        $response = $this->delete('/loans/' . $loan->id);
        $response->seeStatusCode(200);
        $response->seeJson(['message' => 'Loan deleted']);

        $this->assertEquals(DB::table('loans')->count(), 0);

        $this->notSeeInDatabase('loans', [
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);
    }

    public function testRepay()
    {
        $loan = Loan::create([
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => null,
            'total_paid_amount' => 0,
        ]);

        $response = $this->post('/loans/' . $loan->id . '/repay', [
            'repayment_amount' => 500,
        ]);

        $response->seeStatusCode(200);

        $response->seeJsonStructure([
            'id',
            'borrower_name',
            'amount',
            'interest_rate',
            'term',
            'status',
            'last_payment_date',
            'total_paid_amount',
            'created_at',
            'updated_at',
            'remaining_amount'
        ]);
        $response->seeJson([
            'total_paid_amount' => 500,
            'remaining_amount' => 500,
        ]);

        $this->assertEquals(DB::table('loans')->count(), 1);

        $this->seeInDatabase('loans', [
            'borrower_name' => 'Igor',
            'amount' => 1000,
            'interest_rate' => 0.1,
            'term' => 12,
            'status' => 'pending',
            'last_payment_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'total_paid_amount' => 500,
        ]);
    }
}
