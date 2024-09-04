<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::all();
        return $transactions;
    }

    public function show(string $id)
    {
        $transaction = Transaction::find($id);
        return $transaction;
    }

    
     public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $user = Auth::user();

        $transaction = new Transaction([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'user_id' => $user->id,
        ]);

        $transaction->save();

        return response()->json($transaction, 201);
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=> 'required|string|max:255',
            'type'=> 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);
        $transaction = Transaction::find($id);
        $transaction->update($request->all());
        return $transaction;
    }


    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return 'Transaction deleted successfully';
    }

    public function getTransactionsByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)->get();
        return response()->json($transactions, 200);
    }

    public function getEarningsByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)
                                    ->where('type', 'earning')
                                    ->get();
        return response()->json($transactions, 200);
    }


    public function getExpensesByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)
                                    ->where('type', 'expense')
                                    ->get();
        return response()->json($transactions, 200);
    }
}
