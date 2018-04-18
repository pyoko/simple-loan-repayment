<?php

namespace App\Http\Controllers\API;

use App\{Repayment, Loan};
use App\Repositories\RepaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RepaymentController extends Controller
{
    protected $repaymentRepo;


    public function __construct(RepaymentRepository $repaymentRepo)
    {
        $this->repaymentRepo = $repaymentRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $repayments = $this->repaymentRepo->all();

        return response()->json(
            $this->repaymentRepo->resourceCollection($repayments)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Loan $loan)
    {
        /**
         * I would say, tis but a quite terrible design if we aren't using authentication
         * The user who makes the repayment, should be the same with the user who created
         * the loan. (except users with admin role tho).
         * Hmm ...... need to ask the expert about this :D
         */

        try {
            $repayment = $this->repaymentRepo->createRepayment($request->all(), $loan);

            return response()->json(
                $this->repaymentRepo->resource($repayment)
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Display repayment detail.
     *
     * @param  \App\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function show(Repayment $repayment)
    {
        return response()->json(
            $this->repaymentRepo->resource($repayment)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Repayment $repayment)
    {
        $this->repaymentRepo->delete($repayment);
        return response()->json(null, 204);
    }
}
