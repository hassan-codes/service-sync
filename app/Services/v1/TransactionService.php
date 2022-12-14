<?php

namespace App\Services\v1;

use App\Http\Requests\v1\StoreTransactionRequest;
use App\Http\Resources\v1\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionService
{
    public function post(StoreTransactionRequest $request)
    {
        $user = auth()->user();
        $transaction = new Transaction();

        try {
            $transaction->id = strtoupper(uniqid());
            $transaction->amount = $request->amount;
            $transaction->currency = $request->currency;
            $transaction->description = strtoupper($request->description);
            $transaction->type = strtoupper($request->type);
            $transaction->posted_by = $user->id;

            $transaction->save();

            // TODO: post transaction to fulfillment service
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to post transaction. Try again!',
            ], 422);
        }

        return $transaction;
    }

    public function fetch(string $transactionId = null)
    {
        $user = auth()->user();

        if (! $this->isPasswordReset($user)) {
            return response()->json([
                'message' => 'Unauthorized. You must reset your password',
            ], 401);
        }

        try {
            if ($transactionId == null) {
                if ($user->role == 'admin') {
                    $transactions = TransactionResource::collection(
                        Transaction::orderBy('posted_at', 'DESC')->paginate());
                } elseif ($user->role == 'agent') {
                    $transactions = TransactionResource::collection(
                        Transaction::where('posted_by', $user->id)->orderBy('posted_at', 'DESC')->paginate());
                }
            } else {
                $transactions = new TransactionResource(Transaction::findOrFail($transactionId));
            }
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to list agents',
            ], 422);
        }

        return $transactions;
    }

    public function fetchByDateRange(Request $request)
    {
        $user = auth()->user();

        $dateRange = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ];

        $validator = Validator::make($dateRange, [
            'date_from' => 'required|date_format:Y-m-d\TH:i:s',
            'date_to' => 'required|date_format:Y-m-d\TH:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        if (! $this->isPasswordReset($user)) {
            return response()->json([
                'message' => 'Unauthorized. You must reset your password',
            ], 401);
        }

        try {
            if ($user->role == 'admin') {
                $transactions = TransactionResource::collection(
                    Transaction::where([
                        ['posted_at', '>=', $dateRange['date_from']],
                        ['posted_at', '<=', $dateRange['date_to']]
                    ])->orderBy('posted_at', 'DESC')->paginate());
            } elseif ($user->role == 'agent') {
                $transactions = TransactionResource::collection(
                    Transaction::where([
                        ['posted_by', '=', $user->id],
                        ['posted_at', '>=', $dateRange['date_from']],
                        ['posted_at', '<=', $dateRange['date_to']]
                    ])->orderBy('posted_at', 'DESC')->paginate());
            }
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to list agents',
            ], 422);
        }

        return $transactions;
    }

    public function fetchByUser(int $postedBy)
    {
        $user = auth()->user();

        if (! $this->isPasswordReset($user)) {
            return response()->json([
                'message' => 'Unauthorized. You must reset your password',
            ], 401);
        }

        if (! $this->isAdmin($user)) {
            return response()->json([
                'message' => 'Unauthorized. Illegal action attempted',
            ], 401);
        }

        try {
            $transactions = TransactionResource::collection(
                Transaction::where('posted_by', $postedBy)->orderBy('posted_at', 'DESC')->paginate());
        } catch (QueryException $exception) {
            error_log($exception->getMessage());
            // TODO: email error to sysadmin
            return response()->json([
                'error' => 'Failed to list agents',
            ], 422);
        }

        return $transactions;
    }

    protected function isPasswordReset(User $user)
    {
        if (! is_null($user->password_reset_at)){
            return true;
        }

        return false;
    }

    protected function isAdmin(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }
}
