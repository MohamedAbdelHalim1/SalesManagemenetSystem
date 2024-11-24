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

    public function createExpenses(){
        return view('transactions.create-expenses');
    }

    public function closeDay(Request $request, $open_close_id)
    {
        $total_cash = $request->total_cash;
    
        // Fetch the current OpenClose record
        $lastOpenClose = OpenClose::find($open_close_id);
    
        if ($lastOpenClose && is_null($lastOpenClose->close_at)) {
            // Flag the record for closing but don't update close_at yet
            $lastOpenClose->update(['pending_close' => true]);
    
            // Redirect to the validation page
            return redirect()->route('transactions.coins', [
                'open_close_id' => $open_close_id,
                'total_cash' => $total_cash
            ]);
        }
    
        return back()->withErrors('Unable to close the day.');
    }

    public function finalizeClose(Request $request, $open_close_id)
    {
        $lastOpenClose = OpenClose::find($open_close_id);

        if ($lastOpenClose && $lastOpenClose->pending_close) {
            // Set the close_at timestamp to mark the day as closed
            $lastOpenClose->update([
                'close_at' => Carbon::now(),
                'pending_close' => false // Clear the pending_close flag
            ]);

            return redirect()->route('transaction.index')->with('success', 'Day closed successfully.');
        }

        return back()->withErrors('Unable to finalize the close.');
    }


    public function show($id)
    {
        $openClose = OpenClose::with([
            'transactions.transfers',
            'transactions.expenses',
            'transactions.coin',
        ])->findOrFail($id);
    
        $totalcashforclose = 0;
    
        // Calculate total cash for closing
        foreach ($openClose->transactions as $transaction) {
            $totalcashforclose += $transaction->total_cash;
    
            // Subtract transfers
            foreach ($transaction->transfers as $transfer) {
                $totalcashforclose -= $transfer->transfer_value;
            }
    
            // Subtract expenses
            foreach ($transaction->expenses as $expense) {
                $totalcashforclose -= $expense->expenses_value;
            }
        }
    
        // Separate expenses entered by users with role_id = 2 (accounting)
        $accountingExpenses = [];
        $generalExpenses = [];
    
        foreach ($openClose->transactions as $transaction) {
            foreach ($transaction->expenses as $expense) {
                if ($expense->transaction->user->role_id == 2) {
                    $accountingExpenses[] = $expense;
                } else {
                    $generalExpenses[] = $expense;
                }
            }
        }
    
        return view('transactions.show', compact('openClose', 'totalcashforclose', 'accountingExpenses', 'generalExpenses'));
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

        // Check if only expenses are provided and other fields are empty
        $isOnlyExpenses = empty($data['reference_collection']) &&
                        empty($data['order_number']) &&
                        empty($data['order_delivered']) &&
                        empty($data['total_cash']) &&
                        empty($data['commission_value']) &&
                        empty($data['transfer_keys']) &&
                        !empty($data['expense_keys']);

        // If only expenses are present, create a simplified transaction with auth user
        if ($isOnlyExpenses) {
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'open_close_id' => $openClose->id,
            ]);
        } else {
            // Create a full transaction with provided details
            $transaction = Transaction::create([
                'reference_collection' => $data['reference_collection'],
                'order_number' => $data['order_number'],
                'order_delivered' => $data['order_delivered'],
                'total_cash' => $data['total_cash'],
                'sales_commission' => $data['commission_value'],
                'total_remaining' => $data['remaining'],
                'user_id' => $data['user_id'] ?? Auth::id(),
                'open_close_id' => $openClose->id,
            ]);

            // Save Transfers with Images if provided
            if (isset($data['transfer_keys']) && isset($data['transfer_values'])) {
                for ($i = 0; $i < count($data['transfer_keys']); $i++) {
                    $transferData = [
                        'transfer_key' => $data['transfer_keys'][$i],
                        'transfer_value' => $data['transfer_values'][$i],
                        'transaction_id' => $transaction->id,
                    ];

                    // Handle the file upload
                    if ($request->hasFile('transfer_images.' . $i)) {
                        $file = $request->file('transfer_images.' . $i);
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $destinationPath = public_path('transfer');

                        // Ensure the directory exists
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        // Move the file to the public/transfer directory
                        $file->move($destinationPath, $fileName);
                        $transferData['image'] = 'transfer/' . $fileName;
                    }

                    Transfer::create($transferData);
                }
            }
        }

        // Save Expenses if provided
        if (isset($data['expense_keys']) && isset($data['expense_values'])) {
            for ($i = 0; $i < count($data['expense_keys']); $i++) {
                Expenses::create([
                    'expenses_key' => $data['expense_keys'][$i],
                    'expenses_value' => $data['expense_values'][$i],
                    'transaction_id' => $transaction->id,
                ]);
            }
        }


            // Save Coins
        Coin::create([
            'coin_200' => $data['coin_200'] ?? 0,
            'coin_100' => $data['coin_100'] ?? 0,
            'coin_50' => $data['coin_50'] ?? 0,
            'coin_20' => $data['coin_20'] ?? 0,
            'coin_10' => $data['coin_10'] ?? 0,
            'coin_5' => $data['coin_5'] ?? 0,
            'coin_1' => $data['coin_1'] ?? 0,
            'transaction_id' => $transaction->id,
            'money_shortage' => ($data['cash_equivalent'] ?? 0) - ($data['total_cash'] ?? 0),
        ]);


        return redirect()->route('transaction.create')->with('success', 'Transaction saved successfully.');
    }




    public function edit($id)
    {
        $transaction = Transaction::with(['transfers', 'expenses', 'coin'])->findOrFail($id);
        return view('transactions.edit', compact('transaction'));
    }


    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $data = $request->all();
    
        // Update the transaction details
        $transaction->update([
            'reference_collection' => $data['reference_collection'],
            'order_number' => $data['order_number'],
            'order_delivered' => $data['order_delivered'],
            'total_cash' => $data['total_cash'],
            'sales_commission' => $data['sales_commission'],
            'total_remaining' => $data['total_remaining'],
        ]);
    
        // Update existing Transfers
        if (isset($data['transfer_ids'])) {
            foreach ($data['transfer_ids'] as $transferId) {
                $transfer = Transfer::find($transferId);
                if ($transfer) {
                    $transfer->transfer_key = $data['transfer_keys'][$transferId];
                    $transfer->transfer_value = $data['transfer_values'][$transferId];
    
                    // Check if a new image is uploaded
                    if ($request->hasFile("transfer_images.$transferId")) {
                        $file = $request->file("transfer_images.$transferId");
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $destinationPath = public_path('transfer');
    
                        // Ensure the directory exists
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
    
                        // Save the new file and delete the old one if it exists
                        $file->move($destinationPath, $fileName);
                        if ($transfer->image && file_exists(public_path($transfer->image))) {
                            unlink(public_path($transfer->image));
                        }
    
                        $transfer->image = 'transfer/' . $fileName;
                    }
                    $transfer->save();
                }
            }
        }
    
        // Add new Transfers
        if (isset($data['new_transfer_keys']) && isset($data['new_transfer_values'])) {
            for ($i = 0; $i < count($data['new_transfer_keys']); $i++) {
                $newTransfer = new Transfer();
                $newTransfer->transaction_id = $transaction->id;
                $newTransfer->transfer_key = $data['new_transfer_keys'][$i];
                $newTransfer->transfer_value = $data['new_transfer_values'][$i];
    
                // Check if an image is uploaded
                if ($request->hasFile("new_transfer_images.$i")) {
                    $file = $request->file("new_transfer_images.$i");
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $destinationPath = public_path('transfer');
    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
    
                    $file->move($destinationPath, $fileName);
                    $newTransfer->image = 'transfer/' . $fileName;
                }
                $newTransfer->save();
            }
        }
    
        // Delete Transfers
        if ($request->filled('deleted_transfers')) {
            $deletedTransferIds = explode(',', $request->deleted_transfers);
            foreach ($deletedTransferIds as $deletedTransferId) {
                $transfer = Transfer::find($deletedTransferId);
                if ($transfer) {
                    if ($transfer->image && file_exists(public_path($transfer->image))) {
                        unlink(public_path($transfer->image));
                    }
                    $transfer->delete();
                }
            }
        }
    
        // Update existing Expenses
        if (isset($data['expense_ids'])) {
            foreach ($data['expense_ids'] as $expenseId) {
                $expense = Expenses::find($expenseId);
                if ($expense) {
                    $expense->expenses_key = $data['expense_keys'][$expenseId];
                    $expense->expenses_value = $data['expense_values'][$expenseId];
                    $expense->save();
                }
            }
        }
    
        // Add new Expenses
        if (isset($data['new_expense_keys']) && isset($data['new_expense_values'])) {
            for ($i = 0; $i < count($data['new_expense_keys']); $i++) {
                Expenses::create([
                    'transaction_id' => $transaction->id,
                    'expenses_key' => $data['new_expense_keys'][$i],
                    'expenses_value' => $data['new_expense_values'][$i],
                ]);
            }
        }
    
        // Delete Expenses
        if ($request->filled('deleted_expenses')) {
            $deletedExpenseIds = explode(',', $request->deleted_expenses);
            Expenses::whereIn('id', $deletedExpenseIds)->delete();
        }
    
         // Update coin distribution
        if ($transaction->coin) {
            $transaction->coin->update([
                'coin_200' => $data['coin_200'] ?? 0,
                'coin_100' => $data['coin_100'] ?? 0,
                'coin_50' => $data['coin_50'] ?? 0,
                'coin_20' => $data['coin_20'] ?? 0,
                'coin_10' => $data['coin_10'] ?? 0,
                'coin_5' => $data['coin_5'] ?? 0,
                'coin_1' => $data['coin_1'] ?? 0,
            ]);
        } else {
            // Create a new coin record if none exists
            $transaction->coin()->create([
                'coin_200' => $data['coin_200'] ?? 0,
                'coin_100' => $data['coin_100'] ?? 0,
                'coin_50' => $data['coin_50'] ?? 0,
                'coin_20' => $data['coin_20'] ?? 0,
                'coin_10' => $data['coin_10'] ?? 0,
                'coin_5' => $data['coin_5'] ?? 0,
                'coin_1' => $data['coin_1'] ?? 0,
            ]);
        }


        return redirect()->back()->with('success', 'Transaction updated successfully!');
    }
    



    public function getTransactionsByRecord($recordId)
    {
        $record = OpenClose::with('transactions.user')->findOrFail($recordId);
        return response()->json(['transactions' => $record->transactions]);
    }


    public function coins($open_close_id)
    {
        $openClose = OpenClose::with('transactions.coin')->findOrFail($open_close_id);

        // Aggregate total coins across all transactions
        $totalCoins = [
            'coin_200' => 0,
            'coin_100' => 0,
            'coin_50' => 0,
            'coin_20' => 0,
            'coin_10' => 0,
            'coin_1' => 0,
        ];

        foreach ($openClose->transactions as $transaction) {
            if ($transaction->coin) {
                $totalCoins['coin_200'] += $transaction->coin->coin_200;
                $totalCoins['coin_100'] += $transaction->coin->coin_100;
                $totalCoins['coin_50'] += $transaction->coin->coin_50;
                $totalCoins['coin_20'] += $transaction->coin->coin_20;
                $totalCoins['coin_10'] += $transaction->coin->coin_10;
                $totalCoins['coin_1'] += $transaction->coin->coin_1;
            }
        }

        $totalCash = $openClose->transactions->sum('total_cash');
        $totalCoinsValue = array_sum(array_map(fn($count, $value) => $count * $value, $totalCoins, [200, 100, 50, 20, 10, 1]));
        $moneyShortage = $totalCash - $totalCoinsValue;

        return view('transactions.coins', [
            'open_close_id' => $open_close_id,
            'total_cash' => $totalCash,
            'totalCoins' => $totalCoins,
            'moneyShortage' => $moneyShortage,
        ]);
    }


    // public function storeCoins(Request $request, $open_close_id)
    // {
    //     Coin::create([
    //         'coin_0_5' => $request->input('coin_0_5'),
    //         'coin_1' => $request->input('coin_1'),
    //         'coin_10' => $request->input('coin_10'),
    //         'coin_20' => $request->input('coin_20'),
    //         'coin_50' => $request->input('coin_50'),
    //         'coin_100' => $request->input('coin_100'),
    //         'coin_200' => $request->input('coin_200'),
    //         'open_close_id' => $open_close_id,
    //         'money_shortage' => $request->money_shortage,
    //     ]);
    //     $userId = Auth::id();

    //     return redirect()->route('reports.index' , $userId)->with('success', 'Day Closed Successfully!');
    // }




}
