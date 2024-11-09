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
        $openClose = OpenClose::with(['transactions.transfers', 'transactions.expenses', 'coin'])->findOrFail($id);
        $totalcashforclose = 0;
    
        // Get the authenticated user's role
        $userRole = Auth::user()->role_id;
    
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

        return redirect()->route('transaction.create')->with('success', 'Transaction saved successfully.');
    }




    public function edit($id)
    {
        $transaction = Transaction::with(['transfers', 'expenses'])->findOrFail($id);
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

    return redirect()->route('transaction.index')->with('success', 'Transaction updated successfully!');
}




    public function getTransactionsByRecord($recordId)
    {
        $record = OpenClose::with('transactions.user')->findOrFail($recordId);
        return response()->json(['transactions' => $record->transactions]);
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
