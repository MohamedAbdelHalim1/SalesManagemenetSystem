<?php

namespace App\Http\Controllers;

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
            ->get();

        return view("reports.index", compact('openCloses'));
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
}
