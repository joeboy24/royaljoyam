<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\CompanyBranch;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth']);
    }

    public function index(Request $request)
    {
        if (session('sales_permit') == 0) {
            return redirect('/dashboard')->with(
                'error',
                'Oops..! Contact administrator to initialize '.date('F, Y').' opening'
            );
        }

        $salesDate = session('date_today') ?: now()->format('Y-m-d');
        $user = $request->user();
        $isAdmin = $user->status === 'Administrator';

        $query = Expense::with('companybranch')
            ->where('del', 'no')
            ->where('created_at', 'LIKE', '%'.$salesDate.'%')
            ->orderByDesc('id');

        if (! $isAdmin) {
            $query->where('companybranch_id', $user->bv);
        }

        $expenses = $query->get();
        $branches = CompanyBranch::where('del', 'no')->orderBy('tag')->get();
        $activeBranches = $isAdmin
            ? $branches
            : $branches->where('id', (int) $user->bv)->values();

        return view('pages.dash.expenses', [
            'expenses' => $expenses,
            'salesDate' => $salesDate,
            'salesDateLabel' => \Carbon\Carbon::parse($salesDate)->format('l, d M Y'),
            'isAdmin' => $isAdmin,
            'activeBranches' => $activeBranches,
            'expenseTotal' => (float) $expenses->sum('expense_cost'),
        ]);
    }

    public function store(StoreExpenseRequest $request)
    {
        Expense::create([
            'user_id' => (string) $request->user()->id,
            'companybranch_id' => $request->branchId(),
            'title' => $request->input('title'),
            'desc' => $request->input('desc'),
            'expense_cost' => $request->input('expense_cost'),
            'del' => 'no',
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function destroy(Expense $expense)
    {
        if (! $this->canManage($expense)) {
            abort(403);
        }

        if ($expense->del === 'yes') {
            return redirect()
                ->route('expenses.index')
                ->with('error', 'That expense record was already removed.');
        }

        $expense->del = 'yes';
        $expense->save();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense record deleted.');
    }

    private function canManage(Expense $expense): bool
    {
        $user = auth()->user();

        if ($user->status === 'Administrator') {
            return true;
        }

        return (string) $expense->companybranch_id === (string) $user->bv;
    }
}
