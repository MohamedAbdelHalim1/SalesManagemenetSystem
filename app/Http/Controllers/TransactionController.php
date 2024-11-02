<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Expenses;
use App\Models\Coin;
use App\Models\OpenClose;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TransactionController extends Controller
{
    public function transaction()
    {
        $userId = Auth::id();
        // Get the last OpenClose record for the authenticated user
        $lastOpenClose = OpenClose::where('user_id', $userId)->latest()->first();
    
        if ($lastOpenClose && is_null($lastOpenClose->close_at)) {
            // Retrieve OpenClose records and load related transactions
            $openCloseRecords = OpenClose::with('transactions')->where('user_id', $userId)->get();
    
            return view('transactions.index', compact('openCloseRecords', 'lastOpenClose'));
        } else {
            // Show the modal to confirm opening a new transaction day
            return view('transactions.confirm_open');
        }
    }
    
    public function create(){
        return view('transactions.create');
    }

    public function closeDay(Request $request , $open_close_id)
    {
        $total_cash = $request->total_cash;
        // Get the last OpenClose record for the authenticated user
        $lastOpenClose = OpenClose::find($open_close_id);

        if ($lastOpenClose && is_null($lastOpenClose->close_at)) {
            // Update the close_at timestamp to close the day
            $lastOpenClose->update(['close_at' => Carbon::now()]);

            // Redirect to the profile page
        return redirect()->route('transactions.coins', ['open_close_id' => $open_close_id , 'total_cash'=>$total_cash]);
    }

        return back()->withErrors('Unable to close the day.');
    }

    public function show($id)
{
    $openClose = OpenClose::with(['transactions.transfers', 'transactions.expenses'])->findOrFail($id);
    $totalcashforclose = 0;

    // Calculate total cash from all transactions
    foreach ($openClose->transactions as $transaction) {
        $totalcashforclose += $transaction->total_cash;

        // Subtract transfer values for this transaction
        foreach ($transaction->transfers as $transfer) {
            $totalcashforclose -= $transfer->transfer_value;
        }

        // Subtract expense values for this transaction
        foreach ($transaction->expenses as $expense) {
            $totalcashforclose -= $expense->expenses_value;
        }
    }

    return view('transactions.show', compact('openClose', 'totalcashforclose'));
}


    public function openTransaction()
    {
        $open_close = OpenClose::create([
            'user_id' => Auth::id(),  // Set the user_id to the authenticated user's ID
            'open_at' => now(),       // Set the open_at to the current timestamp
            'closed_at' => null       // Leave closed_at as null
        ]);   
             
        return view('transactions.create',compact('open_close'));
    }

    public function cancelTransaction()
    {
        $user = Auth::user();

        if ($user->role_id != 1) {
            // Redirect to profile if role_id is not 1
            return redirect()->route('profile.edit');
        } else {
            // Redirect to dashboard if role_id is 1
            return redirect()->route('dashboard');
        }
    }


    public function store(Request $request)
    {
        $data = $request->all();

        // Fetch open_close_id for the current user
        $openClose = OpenClose::where('user_id', Auth::id())
            ->whereNotNull('open_at')
            ->whereNull('close_at')
            ->first();
    
        if (!$openClose) {
            return redirect()->back()->withErrors('No active OpenClose found for the current user.');
        }
    
        // Create the Transaction
        $transaction = Transaction::create([
            'reference_collection' => $data['reference_collection'],
            'order_number' => $data['order_number'],
            'order_delivered' => $data['order_delivered'],
            'total_cash' => $data['total_cash'],
            'sales_commission' => $data['commission_value'],
            'total_remaining' => $data['remaining'],
            'user_id' => $data['user_id'],
            'open_close_id' => $openClose->id,
        ]);
    
        // Save Transfers
        if (isset($data['transfer_keys']) && isset($data['transfer_values'])) {
            for ($i = 0; $i < count($data['transfer_keys']); $i++) {
                Transfer::create([
                    'transfer_key' => $data['transfer_keys'][$i],
                    'transfer_value' => $data['transfer_values'][$i],
                    'transaction_id' => $transaction->id,
                ]);
            }
        }
    
        // Save Expenses
        if (isset($data['expense_keys']) && isset($data['expense_values'])) {
            for ($i = 0; $i < count($data['expense_keys']); $i++) {
                Expenses::create([
                    'expenses_key' => $data['expense_keys'][$i],
                    'expenses_value' => $data['expense_values'][$i],
                    'transaction_id' => $transaction->id,
                ]);
            }
        }
    
        return redirect()->back();
        // Redirect to coin form to set currency values
       // return redirect()->route('transactions.coins', ['transaction_id' => $transaction->id]);
    }

    public function coins($open_close_id , $total_cash)
    {
        return view('transactions.coins', ['open_close_id' => $open_close_id , 'total_cash'=>$total_cash]);
    }

    public function storeCoins(Request $request, $open_close_id)
    {
        Coin::create([
            'coin_0_5' => $request->input('coin_0_5'),
            'coin_1' => $request->input('coin_1'),
            'coin_10' => $request->input('coin_10'),
            'coin_20' => $request->input('coin_20'),
            'coin_50' => $request->input('coin_50'),
            'coin_100' => $request->input('coin_100'),
            'coin_200' => $request->input('coin_200'),
            'open_close_id' => $open_close_id,
            'money_shortage' => $request->money_shortage,
        ]);
        $userId = Auth::id();

        return redirect()->route('reports.index' , $userId)->with('success', 'Day Closed Successfully!');
    }




}
