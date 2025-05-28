<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\AccountMainTransaction;
use App\Models\AccountTransaction;
use App\Models\Deposit;
use App\Models\UnitTran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositService
{

    /**
     * ดึงข้อมูล Deposit ทั้งหมดพร้อม pagination
     */
    public function getAllDeposits(int $perPage = 10): LengthAwarePaginator
    {
        return Deposit::with(['account', 'user'])
            ->orderBy('deposit_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหา Deposit ตาม ID
     *
     * @param int $id
     * @return Deposit
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findDepositById(int $id): Deposit
    {
        return Deposit::with(['account', 'user'])->findOrFail($id);
    }

    /**
     * ดึงข้อมูล Deposit ตามบัญชีและปี
     *
     * @param int $accountId
     * @param int $year
     * @return Collection
     */
    public function getDepositsByAccountAndYear(int $accountId, int $year): Collection
    {

        return Deposit::where('account_id', $accountId)
            ->where('deposit_year', $year)
            ->orderBy('deposit_month_no', 'asc')
            ->with(['account', 'user'])
            ->get();
    }

    /**
     * ดึงข้อมูล Deposit ตามบัญชี ปี และเดือน
     *
     * @param int $accountId
     * @param int $year
     * @param int|null $month
     * @return Collection
     */
    public function getDepositsByAccountYearMonth(int $accountId, int $year, ?int $month = null): Collection
    {
        $query = Deposit::where('account_id', $accountId)
            ->where('deposit_year', $year);

        if ($month) {
            $query->where('deposit_month', $month);
        }

        return $query->orderBy('deposit_month', 'asc')
            ->with(['account', 'user'])
            ->get();
    }

    /**
     * ดึงข้อมูล Deposit ตามสถานะ
     *
     * @param string $flag
     * @param int|null $accountId
     * @return Collection
     */
    public function getDepositsByFlag(string $flag, ?int $accountId = null): Collection
    {
        $query = Deposit::where('deposit_flag', $flag);

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        return $query->with(['account', 'user'])
            ->orderBy('deposit_date', 'desc')
            ->get();
    }

    /**
     * สร้าง Deposit ใหม่
     */
    public function createDeposit(array $data): Deposit
    {
        DB::beginTransaction();
        try {
            $deposit = new Deposit($data);
            $deposit->deposit_flag = $data['deposit_flag'] ?? Enum::DEPOSIT_FLAG_P;
            $deposit->deposit_created_by = Auth::id();
            $deposit->deposit_updated_by = Auth::id();
            $deposit->save();

            DB::commit();
            return $deposit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // public function createDepositPlan(UnitTran $unitTran, int $months = Enum::DEFAULT_DEPOSIT_MONTHS)
    // {
    //     $startDate = Carbon::now();
    //     $deposits = [];
    //     $userId = $unitTran->account->user_id;
    //     $monthNames = Enum::getMonthsDropdown(); // ดึงชื่อเดือนภาษาไทยจาก Enum

    //     for ($i = 0; $i < $months; $i++) {
    //         $currentDate = $startDate->copy()->addMonths($i);
    //         $monthNumber = $currentDate->format('m'); // ดึงตัวเลขเดือน

    //         $deposit = new Deposit([
    //             'account_id' => $unitTran->account_id,
    //             'user_id' => $userId,
    //             'deposit_year' => (int)$currentDate->format('Y') + 543,
    //             'deposit_month_no' => $currentDate->format('m'),
    //             'deposit_month' => $monthNames[$monthNumber],
    //             'deposit_date' => $currentDate->format('Y-m-d'),
    //             'deposit_unit' => $unitTran->unit_tran_num,
    //             'deposit_amount' => $unitTran->unit_tran_num * $unitTran->unit_tran_amt,
    //             'deposit_flag' => Enum::DEPOSIT_FLAG_P,
    //             'deposit_created_by' => auth()->id(),
    //             'deposit_updated_by' => auth()->id()
    //         ]);

    //         $deposit->save();
    //         $deposits[] = $deposit;
    //     }

    //     return $deposits;
    // }

    /**
     * สร้างแผนการฝากเงิน
     */
    public function createDepositPlan(UnitTran $unitTran, int $startPeriod = 0): \Illuminate\Support\Collection
    {
        DB::beginTransaction();
        $monthNames = Enum::getMonthsDropdown(); // ดึงชื่อเดือนภาษาไทยจาก Enum
        try {
            $deposits = collect();
            $startDate = now();
            $endDate = now()->endOfYear(); // วันสิ้นปี

            // คำนวณจำนวนเดือนที่เหลือจนถึงสิ้นปี
            $remainingMonths = $startDate->diffInMonths($endDate);

            // หาลำดับงวดเริ่มต้น
            if ($startPeriod === 0) {
                // หางวดล่าสุดจาก Deposit
                $lastDeposit = Deposit::where('account_id', $unitTran->account_id)
                    ->where('user_id', $unitTran->account->user_id)
                    ->orderBy('deposit_no', 'desc')
                    ->first();

                // ถ้ามีงวดล่าสุด ให้เริ่มงวดถัดไป ถ้าไม่มีให้เริ่มที่ 1
                $startPeriod = $lastDeposit ? $lastDeposit->deposit_no + 1 : 1;
            }

            for ($i = 0; $i < $remainingMonths; $i++) {
                $depositDate = $startDate->copy()->addMonths($i);
                $monthNumber = $depositDate->format('m'); // ดึงตัวเลขเดือน

                // กำหนดวันที่ครบกำหนดชำระเป็นวันที่ 5 ของแต่ละเดือน
                $dueDate = $depositDate->copy()->startOfMonth()->addDays(4);

                $deposit = new Deposit([
                    'account_id' => $unitTran->account_id,
                    'user_id' => $unitTran->account->user_id,
//                    'deposit_year' => $depositDate->year + 543,
                    'deposit_year' => $depositDate->year,
                    'deposit_month_no' => $depositDate->format('m'),
                    'deposit_month' => $monthNames[$monthNumber],
                    'deposit_unit' => $unitTran->unit_tran_num,
                    'deposit_amount' => $unitTran->unit_tran_num * $unitTran->unit_tran_amt,
                    'deposit_due_date' => $dueDate, // เพิ่มวันครบกำหนด
                    'deposit_no' => $startPeriod + $i, // เพิ่มลำดับงวด
                    'deposit_flag' => Enum::DEPOSIT_FLAG_P,
                    'deposit_created_by' => Auth::id(),
                    'deposit_updated_by' => Auth::id()
                ]);

                $deposit->save();
                $deposits->push($deposit);
            }

            DB::commit();
            return $deposits;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * อัพเดท Deposit
     */
    public function updateDeposit(Deposit $deposit, array $data): Deposit
    {
        DB::beginTransaction();
        try {
            $deposit->fill($data);
            $deposit->deposit_updated_by = Auth::id();
            $deposit->deposit_updated_date = now();
            $deposit->save();

            DB::commit();
            return $deposit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ประมวลผลการจ่ายเงิน
     */
    public function processPayment(Deposit $deposit, string $payStatus): void
    {
        DB::beginTransaction();
        try {
            $accountService = app(AccountService::class);

//            dd($accountService);
            // อัพเดทสถานะการฝาก
            $deposit->deposit_flag = Enum::DEPOSIT_FLAG_Y;
            $deposit->deposit_pay_type = $payStatus;
            $deposit->deposit_date = now();
            $deposit->deposit_updated_by = Auth::id();
            $deposit->deposit_updated_date = now();
            $deposit->save();

            $myAccount = $accountService->findAccountByAccountNo($deposit->account->account_no);

//            dd($myAccount);

            if ($myAccount) {
                $myAccount->account_balance += $deposit->deposit_amount;
                $myAccount->save();

                $paymentType = Enum::getTransactionPaymentTypeByTransactionType(Enum::AT_TYPE_DP, $myAccount->account_no);

                // สร้างรายการธุรกรรม
                $accountTransaction = new AccountTransaction([
                    'account_no' => $deposit->account->account_no,
                    'user_id' => Auth::id(),
                    'at_date' => now(),
                    'at_unit' => $deposit->deposit_unit,
                    'at_amount' => $deposit->deposit_amount,
                    'at_balance' => $myAccount->account_balance,
                    'at_payment_type' => $paymentType,
                    'at_payment_method' =>  $deposit->deposit_pay_type,
                    'at_type' => Enum::AT_TYPE_DP,
                    'at_remark' => 'จ่ายตรง',
                    'at_created_by' => Auth::id()
                ]);
                $accountTransaction->save();
            }
            $savingsAccount = $accountService->findAccountByAccountNo(Enum::ACCOUNT_SAVINGS);

            if ($savingsAccount) {
                $savingsAccount->account_balance += $deposit->deposit_amount;
                $savingsAccount->save();

                $paymentType = Enum::getTransactionPaymentTypeByTransactionType(Enum::AMT_TYPE_DP, $savingsAccount->account_no);

                // สร้างรายการรับเงิน
                $accountTransactionDS = new AccountMainTransaction([
                    'account_no' => $savingsAccount->account_no,
                    'user_id' => $savingsAccount->user_id,
                    'amt_date' => now(),
                    'amt_unit' => $deposit->deposit_unit,
                    'amt_amount' => $deposit->deposit_amount,
                    'amt_balance' => $myAccount->account_balance,
                    'amt_payment_type' => $paymentType,
                    'amt_payment_method' => $deposit->deposit_pay_type,
                    'amt_type' => Enum::AMT_TYPE_DP,
                    'reference_id' => $deposit->account->account_no,
                    'amt_remark' => 'ได้รับเงินจาก ' . $deposit->user->name,
                    'amt_created_by' => Auth::id()
                ]);
                $accountTransactionDS->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
//            dd($e);
            throw $e;
        }
    }

    /**
     * อัพโหลดเอกสาร
     *
     * @param Deposit $deposit
     * @param $file
     * @throws \Exception
     */
    public function uploadSlipDocument(Deposit $deposit, $file): void
    {
        DB::beginTransaction();
        try {
            // ตรวจสอบไฟล์
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            // ตรวจสอบขนาดไฟล์ (mediumblob max = 16MB)
            $maxSize = 16 * 1024 * 1024; // 16MB in bytes
            if ($file->getSize() > $maxSize) {
                throw new \Exception('File size exceeds maximum limit of 16MB');
            }

            // ตรวจสอบประเภทไฟล์
            $allowedTypes = Enum::ALLOWED_FILE_TYPES;
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG and PDF are allowed');
            }

            // อ่านไฟล์เป็น binary
            $fileContent = file_get_contents($file->getRealPath());
            if ($fileContent === false) {
                throw new \Exception('Failed to read file content');
            }

            // บันทึกข้อมูล
            $deposit->deposit_flag = Enum::DEPOSIT_FLAG_W;
            $deposit->deposit_pic = $fileContent;
            $deposit->deposit_pic_type = $file->getMimeType(); // เก็บ mime type ไว้ด้วย (ถ้ามีฟิลด์นี้)
            $deposit->deposit_updated_by = Auth::id();
            $deposit->deposit_updated_date = now();
            $deposit->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูล Deposit ตามปีและเดือน
     */
    public function getDepositsByYearMonth(int $year, ?int $month = null): Collection
    {
        $query = Deposit::where('deposit_year', $year);

        if ($month) {
            $query->where('deposit_month', $month);
        }

        return $query->get();
    }

    /**
     * ดึงข้อมูล Deposit ตามบัญชี
     */
    public function getDepositsByAccount(int $accountId): Collection
    {
        return Deposit::where('account_id', $accountId)
            ->orderBy('deposit_year', 'desc')
            ->orderBy('deposit_month', 'desc')
            ->get();
    }

}
