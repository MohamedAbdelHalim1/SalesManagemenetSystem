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
            ->with(['transactions','coin']) // Load the related transactions
            ->orderBy('created_at','desc')
            ->get();
            
        return view("reports.index", compact('openCloses'));
    }

    public function show($id)
    {
        // Retrieve the open_close record with related transactions, transfers, expenses, and coin by ID
        $openClose = OpenClose::with(['transactions.transfers', 'transactions.expenses', 'coin'])
            ->findOrFail($id);
    
        // Retrieve general expenses related to the openClose transactions
        $generalExpenses = Expenses::whereIn('transaction_id', $openClose->transactions->pluck('id'))->get();
    
        return view('reports.show', compact('openClose', 'generalExpenses'));
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
