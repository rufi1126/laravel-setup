<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->transactions()->with('category')->latest('date')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $transaction = $request->user()->transactions()->create($data);

        return response()->json($transaction, 201);
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'type' => 'sometimes|in:income,expense',
            'amount' => 'sometimes|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'date' => 'sometimes|date',
        ]);

        $transaction->update($data);

        return $transaction;
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== $request->user()->id, 403);

        $transaction->delete();

        return response()->noContent();
    }
}
