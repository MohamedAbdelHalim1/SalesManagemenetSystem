<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\Transfer;
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


    public function general_report(){
        $openCloses = OpenClose::with(['transactions' , 'transactions.transfers' , 'transactions.expenses' , 'transactions.coin' ,'transactions.user'])->get();
        return view('reports.general', compact('openCloses'));
    }

    public function general_transaction_show($transactionId)
    {
        $transaction = Transaction::with('user')->findOrFail($transactionId);
        return view('reports.transactions-show', compact('transaction'));
    }

    public function general_tranfer_show($transactionId)
    {
        $transfers = Transfer::where('transaction_id', $transactionId)->get();
        return view('reports.transfers-show', compact('transfers'));
    }

    public function general_expenses_show($transactionId)
    {
        $expenses = Expenses::where('transaction_id', $transactionId)->get();
        return view('reports.expenses-show', compact('expenses'));
    }

    public function special_report(Request $request)
    {
        // Retrieve the filters from the request (if provided)
        $openDay = $request->input('openDay');
        $closeDay = $request->input('closeDay');
        $accountantId = $request->input('accountant');
        $salesId = $request->input('sales');
    
        // Start building the query for transactions
        $query = Transaction::with(['openclose.user', 'user', 'transfers', 'expenses'])
                            ->orderBy('created_at', 'desc');
    
        if ($openDay) {
            $query->whereHas('openclose', function($q) use ($openDay) {
                $q->whereDate('open_at', '>=', $openDay);
            });
        }
        
        if ($closeDay) {
            $query->whereHas('openclose', function($q) use ($closeDay) {
                $q->whereDate('close_at', '<=', $closeDay);
            });
        }

        if ($accountantId) {
            $query->whereHas('openclose.user', function($q) use ($accountantId) {
                $q->where('id', $accountantId);
            });
        }
    
        if ($salesId) {
            $query->where('user_id', $salesId);
        }
    
        // Get all the filtered transactions
        $transactions = $query->get();
    
        // Fetch the accountants and sales for the filter dropdowns
        $accountants = User::where('role_id', 2)->get();
        $sales = User::where('role_id', 3)->get();
    
        return view('reports.special', compact('transactions', 'accountants', 'sales'));
    }
    
    
    


}
