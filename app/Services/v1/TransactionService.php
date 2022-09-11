<?php

namespace App\Services\v1;

use App\Http\Requests\v1\StoreTransactionRequest;
use App\Http\Resources\v1\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;

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

    public function fetch(int $userId = 0)
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
            if ($userId == 0) {
                $transactions = TransactionResource::collection(
                    Transaction::orderBy('posted_at', 'DESC')->paginate());
            } elseif ($userId > 0) {
                $transactions = TransactionResource::collection(
                    Transaction::where('posted_by', $userId)
                        ->orderBy('posted_at', 'DESC')
                        ->paginate());
            }
        } catch (QueryException $exception) {

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
