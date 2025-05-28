<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTransactionController extends Controller
{
    public function index()
    {
        $transactions = AccountTransaction::with(['user', 'status'])
            ->orderBy('at_date', 'desc')
            ->paginate(10);
        return view('superAdmin.accountTransactions.index', compact('transactions'));
    }

    public function create()
    {
        $users = User::all();
        $accounts = Account::all();
        $statuses = Status::all();
        $transactionTypes = [
            'DP' => 'ฝากเงิน',
            'WD' => 'ถอนเงิน',
            'LN' => 'กู้เงิน',
            'LP' => 'ชำระเงินกู้',
            'LD' => 'จ่ายเงินกู้',
            'DI' => 'รับเงินปันผล',
            'DO' => 'จ่ายเงินปันผล',
            'GU' => 'ค้ำประกัน'
        ];

        return view('superAdmin.accountTransactions.create', compact('users', 'accounts', 'statuses', 'transactionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_no' => 'required|string|max:10',
            'user_id' => 'required|exists:users,id',
            'at_date' => 'required|date',
            'at_unit' => 'required|integer',
            'at_amount' => 'required|numeric',
            'at_type' => 'required|in:DP,WD,LN,LP,LD,DI,DO,GU',
            'reference_id' => 'nullable|integer',
            'at_remark' => 'nullable|string'
        ]);

        $transaction = new AccountTransaction();
        $transaction->account_no = $request->account_no;
        $transaction->user_id = $request->user_id;
        $transaction->at_date = $request->at_date;
        $transaction->at_unit = $request->at_unit;
        $transaction->at_amount = $request->at_amount;
        $transaction->at_type = $request->at_type;
        $transaction->reference_id = $request->reference_id;
        $transaction->at_remark = $request->at_remark;
        $transaction->at_created_by = Auth::id();
        $transaction->save();

        return redirect()->route('accountTransactions.index')
            ->with('success', 'บันทึกรายการเดินบัญชีสำเร็จ');
    }

    public function edit($id)
    {
        $transaction = AccountTransaction::findOrFail($id);
        $users = User::all();
        $accounts = Account::all();
        $statuses = Status::all();
        $transactionTypes = [
            'DP' => 'ฝากเงิน',
            'WD' => 'ถอนเงิน',
            'LN' => 'กู้เงิน',
            'LP' => 'ชำระเงินกู้',
            'LD' => 'จ่ายเงินกู้',
            'DI' => 'รับเงินปันผล',
            'DO' => 'จ่ายเงินปันผล',
            'GU' => 'ค้ำประกัน'
        ];

        return view('superAdmin.accountTransactions.edit',
            compact('transaction', 'users', 'accounts', 'statuses', 'transactionTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_no' => 'required|string|max:10',
            'user_id' => 'required|exists:users,id',
            'at_date' => 'required|date',
            'at_unit' => 'required|integer',
            'at_amount' => 'required|numeric',
            'at_type' => 'required|in:DP,WD,LN,LP,LD,DI,DO,GU',
            'reference_id' => 'nullable|integer',
            'at_remark' => 'nullable|string'
        ]);

        $transaction = AccountTransaction::findOrFail($id);
        $transaction->account_no = $request->account_no;
        $transaction->user_id = $request->user_id;
        $transaction->at_date = $request->at_date;
        $transaction->at_unit = $request->at_unit;
        $transaction->at_amount = $request->at_amount;
        $transaction->at_type = $request->at_type;
        $transaction->reference_id = $request->reference_id;
        $transaction->at_remark = $request->at_remark;
        $transaction->save();

        return redirect()->route('accountTransactions.index')
            ->with('success', 'แก้ไขรายการเดินบัญชีสำเร็จ');
    }
}
