<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OpenClose; // Import the OpenClose model
use App\Models\Transaction; // Import the Transaction model

class ReportController extends Controller
{
    public function index($userId)
    {
        
        // Retrieve all open_close records for the authenticated user
        $openCloses = OpenClose::where('user_id', $userId)
            ->with(['transactions']) // Load the related transactions
            ->orderBy('created_at','desc')
            ->get();
            
        return view("reports.index", compact('openCloses'));
    }

    public function show($id)
    {
        // Fetch openClose record with transactions, transfers, expenses, and coin
        $openClose = OpenClose::with(['transactions.transfers', 'transactions.expenses', 'transactions.coin'])->findOrFail($id);
    
        // Initialize totals
        $totalTransferValueSum = 0;
        $totalExpenseValueSum = 0;
        $totalMyExpenseValueSum = 0;
    
        $generalExpenses = [];
        $accountingExpenses = [];
    
        foreach ($openClose->transactions as $transaction) {
            foreach ($transaction->transfers as $transfer) {
                $totalTransferValueSum += $transfer->transfer_value;
            }
    
            foreach ($transaction->expenses as $expense) {
                if ($expense->transaction->user->role_id == 2) {
                    $accountingExpenses[] = $expense;
                    $totalMyExpenseValueSum += $expense->expenses_value;
                } else {
                    $generalExpenses[] = $expense;
                    $totalExpenseValueSum += $expense->expenses_value;
                }
            }
        }
    
        return view('reports.show', compact(
            'openClose',
            'generalExpenses',
            'accountingExpenses',
            'totalTransferValueSum',
            'totalExpenseValueSum',
            'totalMyExpenseValueSum'
        ));
    }
    


    public function accountingReport($userId)
    {
        $accountant = User::findOrFail($userId);
        $openCloses = OpenClose::where('user_id', $userId)->with('transactions')->get();

        return view('reports.accounting', compact('accountant', 'openCloses'));
    }

    public function salesReport($userId)
    {
        $salesUser = User::findOrFail($userId);
        $transactions = Transaction::where('user_id', $userId)->get();

        return view('reports.sales', compact('salesUser', 'transactions'));
    }

    public function reopen($id)
    {
        // Check if the authenticated user is an admin
        if (Auth::user()->role_id != 1) {
            abort(403, 'Unauthorized action.');
        }

        $openClose = OpenClose::findOrFail($id);
        $openClose->close_at = null;
        $openClose->save();

        return response()->json(['success' => true]);
    }


}
