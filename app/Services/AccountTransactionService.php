<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Enums\Enum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountTransactionService
{
    /**
     * ดึงข้อมูลธุรกรรมทั้งหมดพร้อม pagination
     */
    public function getAllTransactions(int $perPage = 10): LengthAwarePaginator
    {
        return AccountTransaction::with(['account', 'user', 'createdBy'])
            ->orderBy('at_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหาธุรกรรมตาม ID
     */
    public function findTransactionById(int $id): AccountTransaction
    {
        return AccountTransaction::with(['account', 'user', 'createdBy'])
            ->findOrFail($id);
    }

    /**
     * สร้างธุรกรรมใหม่
     */
    public function createTransaction(array $data): AccountTransaction
    {
        DB::beginTransaction();
        try {
            $transaction = new AccountTransaction();
            $transaction->account_no = $data['account_no'];
            $transaction->user_id = $data['user_id'];
            $transaction->at_date = $data['at_date'] ?? now();
            $transaction->at_unit = $data['at_unit'];
            $transaction->at_amount = $data['at_amount'];
            $transaction->at_type = $data['at_type'];
            $transaction->reference_id = $data['reference_id'] ?? null;
            $transaction->at_remark = $data['at_remark'] ?? null;
            $transaction->at_created_by = Auth::id();
            $transaction->at_created_date = now();
            $transaction->save();

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * สร้างธุรกรรมตอนปิดบัญชี
     */
    public function createTransactionForCloseAccount(Account $account): void
    {
            $transaction = new AccountTransaction();
            $transaction->account_no = $account->account_no;
            $transaction->user_id = $account->user_id;
//            $transaction->at_date = $data['at_date'] ?? now();
            $transaction->at_unit = $account->account_final_unit;
            $transaction->at_amount = $account->account_balance;
            $transaction->at_type = Enum::AT_TYPE_WD;
//            $transaction->reference_id = $data['reference_id'] ?? null;
//            $transaction->at_remark = $data['at_remark'] ?? null;
            $transaction->at_created_by = Auth::id();
            $transaction->at_created_date = now();
            $transaction->save();

    }

    /**
     * ดึงธุรกรรมตามเลขบัญชี
     */
    public function getTransactionsByAccountNo(string $accountNo): Collection
    {
        return AccountTransaction::with(['user', 'createdBy'])
            ->where('account_no', $accountNo)
            ->orderBy('at_date', 'desc')
            ->get();
    }

    /**
     * ดึงธุรกรรมตามประเภท
     */
    public function getTransactionsByType(string $type): Collection
    {
        return AccountTransaction::with(['account', 'user', 'createdBy'])
            ->where('at_type', $type)
            ->orderBy('at_date', 'desc')
            ->get();
    }

    /**
     * ดึงธุรกรรมตามช่วงวันที่
     */
    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection
    {
        return AccountTransaction::with(['account', 'user', 'createdBy'])
            ->whereBetween('at_date', [$startDate, $endDate])
            ->orderBy('at_date', 'desc')
            ->get();
    }

    /**
     * ดึงธุรกรรมตามผู้ใช้
     */
    public function getTransactionsByUser(int $userId): Collection
    {
        return AccountTransaction::with(['account', 'createdBy'])
            ->where('user_id', $userId)
            ->orderBy('at_date', 'desc')
            ->get();
    }

    /**
     * ดึงธุรกรรมล่าสุด
     */
    public function getRecentTransactions(int $limit = 10): Collection
    {
        return AccountTransaction::with(['account', 'user', 'createdBy'])
            ->orderBy('at_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * ค้นหาธุรกรรมตามเงื่อนไขต่างๆ
     */
    public function searchTransactions(array $conditions): Collection
    {
        $query = AccountTransaction::with(['account', 'user', 'createdBy']);

        if (isset($conditions['account_no'])) {
            $query->where('account_no', $conditions['account_no']);
        }

        if (isset($conditions['user_id'])) {
            $query->where('user_id', $conditions['user_id']);
        }

        if (isset($conditions['type'])) {
            $query->where('at_type', $conditions['type']);
        }

        if (isset($conditions['start_date']) && isset($conditions['end_date'])) {
            $query->whereBetween('at_date', [$conditions['start_date'], $conditions['end_date']]);
        }

        if (isset($conditions['min_amount'])) {
            $query->where('at_amount', '>=', $conditions['min_amount']);
        }

        if (isset($conditions['max_amount'])) {
            $query->where('at_amount', '<=', $conditions['max_amount']);
        }

        return $query->orderBy('at_date', 'desc')->get();
    }
}
