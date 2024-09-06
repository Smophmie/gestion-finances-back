<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Finance Management API",
 *     description="API permettant de gérer les transactions financières des utilisateurs",
 *     @OA\Contact(
 *         email="support@financeapi.com"
 *     ),
 * )
 * 
 * @OA\Tag(
 *     name="Transactions",
 *     description="Gestion des transactions financières"
 * )
 */
class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/transactions",
     *     tags={"Transactions"},
     *     summary="Récupérer toutes les transactions de l'utilisateur connecté",
     *     operationId="getTransactions",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des transactions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->get();
        return $transactions;
    }

    /**
     * @OA\Get(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     summary="Récupérer une transaction spécifique",
     *     operationId="getTransaction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la transaction",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction non trouvée",
     *     )
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
     *     summary="Créer une nouvelle transaction",
     *     operationId="createTransaction",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","type","amount","date"},
     *             @OA\Property(property="name", type="string", example="Salaire"),
     *             @OA\Property(property="type", type="string", example="earning"),
     *             @OA\Property(property="amount", type="number", format="float", example="1000.50"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-09-04")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *     )
     * )
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'string',
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
            'description' => $request->description,
        ]);

        $transaction->save();

        return response()->json($transaction, 201);
    }

    /**
     * @OA\Put(
     *     path="/transactions/{id}",
     *     tags={"Transactions"},
     *     summary="Mettre à jour une transaction existante",
     *     operationId="updateTransaction",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","type","amount","date"},
     *             @OA\Property(property="name", type="string", example="Salaire"),
     *             @OA\Property(property="type", type="string", example="earning"),
     *             @OA\Property(property="amount", type="number", format="float", example="1000.50"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-09-04")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction mise à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction non trouvée",
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
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
     *     summary="Supprimer une transaction",
     *     operationId="deleteTransaction",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction supprimée avec succès",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction non trouvée",
     *     )
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
     *     path="/earnings",
     *     tags={"Transactions"},
     *     summary="Récupérer les revenus de l'utilisateur connecté",
     *     operationId="getEarnings",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des revenus",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     )
     * )
     */
    public function getEarnings()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'earning')
            ->get();
        return response()->json($transactions, 200);
    }

    /**
     * @OA\Get(
     *     path="/earnings-sum",
     *     tags={"Transactions"},
     *     summary="Récupérer la somme des revenus de l'utilisateur connecté",
     *     operationId="getEarningsSum",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Somme des revenus",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_earnings", type="number", format="float", example="1500.00")
     *         )
     *     )
     * )
     */
    public function getEarningsSum()
    {
        $user = Auth::user();
        $totalEarnings = Transaction::where('user_id', $user->id)
            ->where('type', 'earning')
            ->sum('amount');

        return response()->json(['total_earnings' => $totalEarnings], 200);
    }

    /**
     * @OA\Get(
     *     path="/expenses",
     *     tags={"Transactions"},
     *     summary="Récupérer les dépenses de l'utilisateur connecté",
     *     operationId="getExpenses",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des dépenses",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Transaction")
     *         )
     *     )
     * )
     */
    public function getExpenses()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
        return response()->json($transactions, 200);
    }

    /**
     * @OA\Get(
     *     path="/expenses-sum",
     *     tags={"Transactions"},
     *     summary="Récupérer la somme des dépenses de l'utilisateur connecté",
     *     operationId="getExpensesSum",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Somme des dépenses",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_expenses", type="number", format="float", example="500.00")
     *         )
     *     )
     * )
     */
    public function getExpensesSum()
    {
        $user = Auth::user();
        $totalExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->sum('amount');

        return response()->json(['total_expenses' => $totalExpenses], 200);
    }

    /**
     * @OA\Get(
     *     path="/total-sum",
     *     tags={"Transactions"},
     *     summary="Récupérer la somme totale des transactions de l'utilisateur connecté",
     *     operationId="getTotalSum",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Somme totale",
     *         @OA\JsonContent(
     *             @OA\Property(property="sum", type="number", format="float", example="1000.00")
     *         )
     *     )
     * )
     */
    public function getTotalSum()
    {
        $user = Auth::user();
        $total = Transaction::where('user_id', $user->id)
            ->sum('amount');

        return response()->json(['sum' => $total], 200);
    }
}
