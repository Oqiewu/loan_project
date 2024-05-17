<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Loan;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Loan::query();

        $loans = $query->get();

        return response()->json($loans->append('remaining_amount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string',
            'amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'term' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loan = Loan::create($request->all());

        return response()->json($loan->append('remaining_amount'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loan::find($id);

        if (is_null($loan)) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        return response()->json($loan->append('remaining_amount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (is_null($loan)) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'borrower_name' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric',
            'interest_rate' => 'sometimes|required|numeric',
            'term' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loan->update($request->all());

        return response()->json($loan->append('remaining_amount'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = Loan::find($id);

        if (is_null($loan)) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $loan->delete();

        return response()->json(['message' => 'Loan deleted']);
    }

    public function repay(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (is_null($loan)) {
            return response()->json(['message' => 'Loan not found'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'repayment_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->repayment_amount > $loan->remaining_amount) {
            return response()->json(['message' => 'The repayment amount cannot exceed the remaining amount'], 400);
        }

        $loan->total_paid_amount += $request->repayment_amount;
        $loan->last_payment_date = Carbon::now();
        $loan->save();

        return response()->json($loan->append('remaining_amount'), 200);
    }
}
