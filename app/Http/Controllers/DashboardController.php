<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import the User model
use App\Models\OpenClose; // Import the OpenClose model
use App\Models\Transaction; // Import the Transaction model

class DashboardController extends Controller
{
    public function index()
    {
        // Retrieve all accounting users (role_id = 2) and their OpenClose sessions
        $accountingUsers = User::where('role_id', 2)
            ->withCount('openCloses') // Get count of open/close days
            ->get();

        // Retrieve all sales users (role_id = 3) and their transactions
        $salesUsers = User::where('role_id', 3)
            ->withCount('transactions') // Get count of transactions
            ->get();

        return view('dashboard', compact('accountingUsers', 'salesUsers'));
    }
}
