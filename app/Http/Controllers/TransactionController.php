<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::all();
        return $transactions;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::find($id);
        return $transaction;
    }

    public function create(Request $request)
    {
        // Valider les données de la requête
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            // 'date' => 'required|date',
        ]);

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Créer la transaction avec le user_id de l'utilisateur connecté
        $transaction = new Transaction([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'user_id' => $user->id,
        ]);

        // Sauvegarder la transaction dans la base de données
        $transaction->save();

        // Retourner la transaction nouvellement créée
        return response()->json($transaction, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=> 'required',
            'type'=> 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);
        $transaction = Transaction::find($id);
        $transaction->update($request->all());
        return $transaction;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return 'Transaction deleted successfully';
    }
}
