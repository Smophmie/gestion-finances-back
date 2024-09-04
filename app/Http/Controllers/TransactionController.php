<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/transactions",
     *     tags={"Transactions"},
     *     summary="Get all transactions",
     *     description="Retrieve a list of all transactions.",
     *     @OA\Response(
     *         response=200,
     *         description="A list of transactions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function index()
    {
        $transactions = Transaction::all();
        return $transactions;
    }

    /**
     * @OA\Get(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     summary="Get a specific transaction",
     *     description="Retrieve a specific transaction by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transaction",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A specific transaction",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(response=404, description="Transaction not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function show(string $id)
    {
        $transaction = Transaction::find($id);
        return $transaction;
    }

    /**
     * @OA\Post(
     *     path="/transactions",
     *     tags={"Transactions"},
     *     summary="Create a new transaction",
     *     description="Create a new transaction for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","type","amount","date"},
     *             @OA\Property(property="name", type="string", example="Salary"),
     *             @OA\Property(property="type", type="string", example="earning"),
     *             @OA\Property(property="amount", type="number", format="float", example="2500.50"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-09-04")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction created",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    
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


    /**
     * @OA\Put(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     summary="Update a transaction",
     *     description="Update an existing transaction by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *        
     *         in="path",
     *         description="ID of the transaction to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","type","amount","date"},
     *             @OA\Property(property="name", type="string", example="Updated Salary"),
     *             @OA\Property(property="type", type="string", example="earning"),
     *             @OA\Property(property="amount", type="number", format="float", example="3000.00"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-09-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction updated",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Transaction not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     summary="Delete a transaction",
     *     description="Delete a specific transaction by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transaction to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction deleted successfully",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Transaction deleted successfully"
     *         )
     *     ),
     *     @OA\Response(response=404, description="Transaction not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return 'Transaction deleted successfully';
    }

    /**
     * @OA\Get(
     *     path="/transactions/user/{id}",
     *     tags={"Transactions"},
     *     summary="Get all transactions by user ID",
     *     description="Retrieve all transactions for a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of transactions for the user",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function getTransactionsByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)->get();
        return response()->json($transactions, 200);
    }

    /**
     * @OA\Get(
     *     path="/transactions/user/{id}/earnings",
     *     tags={"Transactions"},
     *     summary="Get all earnings by user ID",
     *     description="Retrieve all transactions of type 'earning' for a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of earnings for the user",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function getEarningsByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)
                                    ->where('type', 'earning')
                                    ->get();
        return response()->json($transactions, 200);
    }

    /**
     * @OA\Get(
     *     path="/transactions/user/{id}/expenses",
     *     tags={"Transactions"},
     *     summary="Get all expenses by user ID",
     *     description="Retrieve all transactions of type 'expense' for a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of expenses for the user",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function getExpensesByIdUser($id)
    {
        $transactions = Transaction::where('user_id', $id)
                                    ->where('type', 'expense')
                                    ->get();
        return response()->json($transactions, 200);
    }
}
