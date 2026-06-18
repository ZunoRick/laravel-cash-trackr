<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(ExpenseRequest $request, Budget $budget)
    {
        $data = $request->validated();
        $budget->expenses()->create($data);
        return redirect()
            ->route('budgets.show', $budget)
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function update(ExpenseRequest $request, Budget $budget, Expense $expense)
    {
        $expense->update($request->validated());
        return redirect()
            ->route('budgets.show', $budget)
            ->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Expense $expense)
    {
        //
    }
}
