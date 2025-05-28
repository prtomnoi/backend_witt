<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\MemberDividend;
use App\Models\DividendAllocation;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

/**
 * Class MemberDividendService
 * @package App\Services
 */
class MemberDividendService
{
    /**
     * @var AccountTransactionService
     */
    protected $accountTransactionService;

    /**
     * MemberDividendService constructor.
     *
     * @param AccountTransactionService $accountTransactionService
     */
    public function __construct(AccountTransactionService $accountTransactionService)
    {
        $this->accountTransactionService = $accountTransactionService;
    }

    /**
     * สร้างข้อมูลเงินปันผลรายสมาชิก
     *
     * @param array $data ข้อมูลเงินปันผลรายสมาชิก
     * @return MemberDividend
     */
    public function create(array $data): MemberDividend
    {
        // กำหนดค่าเริ่มต้นสำหรับวันที่สร้างและอัพเดต
        $now = now();

        return MemberDividend::create([
            'da_id' => $data['da_id'],
            'account_id' => $data['account_id'],
            'md_year' => $data['md_year'],
            'md_saving_months' => $data['md_saving_months'] ?? 0,
            'md_avg_saving_amount' => $data['md_avg_saving_amount'] ?? 0,
            'md_dividend_amount' => $data['md_dividend_amount'] ?? 0,
            'md_status' => $data['md_status'] ?? Enum::MEMBER_DIVIDEND_STATUS_PENDING,
            'md_payment_date' => $data['md_payment_date'] ?? null,
            'md_payment_method' => $data['md_payment_method'] ?? Enum::MEMBER_DIVIDEND_PAYMENT_METHOD_CASH,
            'md_created_by' => $data['md_created_by'],
            'md_created_date' => $data['md_created_date'] ?? $now,
            'md_updated_by' => $data['md_updated_by'],
            'md_updated_date' => $data['md_updated_date'] ?? $now,
        ]);
    }

    /**
     * อัพเดตข้อมูลเงินปันผลรายสมาชิก
     *
     * @param MemberDividend $memberDividend เงินปันผลรายสมาชิกที่ต้องการอัพเดต
     * @param array $data ข้อมูลที่ต้องการอัพเดต
     * @return MemberDividend
     */
    public function update(MemberDividend $memberDividend, array $data): MemberDividend
    {
        // อัพเดตเฉพาะข้อมูลที่ส่งมา
        $memberDividend->update($data);

        return $memberDividend;
    }

    /**
     * ลบข้อมูลเงินปันผลรายสมาชิก
     *
     * @param MemberDividend $memberDividend เงินปันผลรายสมาชิกที่ต้องการลบ
     * @return bool|null
     */
    public function delete(MemberDividend $memberDividend): ?bool
    {
        return $memberDividend->delete();
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกทั้งหมด
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15)
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])
            ->orderByDesc('md_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกตาม ID
     *
     * @param int $id รหัสเงินปันผลรายสมาชิก
     * @return MemberDividend|null
     */
    public function getById(int $id): ?MemberDividend
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])->find($id);
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกตามการจัดสรรเงินปันผล
     *
     * @param int $daId รหัสการจัดสรรเงินปันผล
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByDividendAllocationId(int $daId, int $perPage = 15)
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])
            ->where('da_id', $daId)
            ->orderByDesc('md_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกตามบัญชีสมาชิก
     *
     * @param int $accountId รหัสบัญชีสมาชิก
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByAccountId(int $accountId, int $perPage = 15)
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])
            ->where('account_id', $accountId)
            ->orderByDesc('md_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกตามปี
     *
     * @param string $year ปีที่ต้องการค้นหา
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByYear(string $year, int $perPage = 15)
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])
            ->where('md_year', $year)
            ->orderByDesc('md_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเงินปันผลรายสมาชิกตามสถานะ
     *
     * @param string $status สถานะที่ต้องการค้นหา
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 15)
    {
        return MemberDividend::with(['dividendAllocation', 'account', 'account.user', 'createdBy', 'updatedBy'])
            ->where('md_status', $status)
            ->orderByDesc('md_created_date')
            ->paginate($perPage);
    }

    /**
     * อัพเดตสถานะการจ่ายเงินปันผลรายสมาชิก
     *
     * @param MemberDividend $memberDividend เงินปันผลรายสมาชิกที่ต้องการอัพเดต
     * @param string $status สถานะใหม่
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return MemberDividend
     */
    public function updateStatus(MemberDividend $memberDividend, string $status, int $updatedBy): MemberDividend
    {
        $data = [
            'md_status' => $status,
            'md_updated_by' => $updatedBy,
            'md_updated_date' => now()
        ];

        // ถ้าเป็นการจ่ายเงินหรือโอนเงิน ให้บันทึกวันที่จ่ายด้วย
        if (in_array($status, [Enum::MEMBER_DIVIDEND_STATUS_PAID, Enum::MEMBER_DIVIDEND_STATUS_TRANSFERRED]) && !$memberDividend->md_payment_date) {
            $data['md_payment_date'] = now();
        }

        return $this->update($memberDividend, $data);
    }

    /**
     * คำนวณเงินปันผลรายสมาชิกจากการจัดสรรเงินปันผล
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผล
     * @param int $createdBy รหัสผู้สร้าง
     * @return array สรุปผลการคำนวณ
     */
    public function calculateMemberDividends(DividendAllocation $dividendAllocation, int $createdBy): array
    {
        // เริ่ม transaction
        DB::beginTransaction();

        try {
            // 1. ดึงข้อมูลสมาชิกที่มีสถานะใช้งาน
            $accounts = Account::where('account_status', Enum::ACCOUNT_STATUS_A)
                ->get(['account_id', 'account_no', 'account_unit_balance']);

            // 2. ดึงข้อมูลการฝากเงินสัจจะในรอบปีของสมาชิกแต่ละคน
            $year = $dividendAllocation->annualProfit->ap_year;
            $startDate = "{$year}-01-01";
            $endDate = "{$year}-12-31";

            $savingData = DB::table('account_transaction')
                ->select('at_account_id', DB::raw('COUNT(DISTINCT DATE_FORMAT(at_date, "%Y-%m")) as saving_months'), DB::raw('AVG(at_amount) as avg_saving_amount'))
                ->where('at_type', Enum::AT_TYPE_DP) // ฝากเงิน
                ->where('at_payment_type', Enum::TRANSACTION_PAYMENT_TYPE_S) // บวก
                ->whereBetween('at_date', [$startDate, $endDate])
                ->groupBy('at_account_id')
                ->get()
                ->keyBy('at_account_id');

            // 3. คำนวณมูลค่าต่อหุ้น
            $totalUnits = $accounts->sum('account_unit_balance');
            $valuePerUnit = $totalUnits > 0 ? $dividendAllocation->da_total_amount / $totalUnits : 0;

            // 4. สร้างข้อมูลเงินปันผลรายสมาชิก
            $created = 0;
            $totalDividend = 0;

            foreach ($accounts as $account) {
                // ข้อมูลการฝากเงินสัจจะ
                $savingMonths = $savingData->has($account->account_id) ? $savingData[$account->account_id]->saving_months : 0;
                $avgSavingAmount = $savingData->has($account->account_id) ? $savingData[$account->account_id]->avg_saving_amount : 0;

                // คำนวณเงินปันผลตามจำนวนหุ้น
                $dividendAmount = $account->account_unit_balance * $valuePerUnit;

                // ถ้ามีหุ้นและได้รับเงินปันผล
                if ($account->account_unit_balance > 0 && $dividendAmount > 0) {
                    // ตรวจสอบว่ามีข้อมูลเงินปันผลรายสมาชิกนี้แล้วหรือไม่
                    $existingDividend = MemberDividend::where('da_id', $dividendAllocation->da_id)
                        ->where('account_id', $account->account_id)
                        ->first();

                    if ($existingDividend) {
                        // อัพเดตข้อมูลที่มีอยู่แล้ว
                        $this->update($existingDividend, [
                            'md_saving_months' => $savingMonths,
                            'md_avg_saving_amount' => $avgSavingAmount,
                            'md_dividend_amount' => $dividendAmount,
                            'md_updated_by' => $createdBy,
                            'md_updated_date' => now()
                        ]);
                    } else {
                        // สร้างข้อมูลใหม่
                        $this->create([
                            'da_id' => $dividendAllocation->da_id,
                            'account_id' => $account->account_id,
                            'md_year' => $year,
                            'md_saving_months' => $savingMonths,
                            'md_avg_saving_amount' => $avgSavingAmount,
                            'md_dividend_amount' => $dividendAmount,
                            'md_status' => Enum::MEMBER_DIVIDEND_STATUS_PENDING,
                            'md_payment_method' => Enum::MEMBER_DIVIDEND_PAYMENT_METHOD_CASH,
                            'md_created_by' => $createdBy,
                            'md_updated_by' => $createdBy
                        ]);

                        $created++;
                    }

                    $totalDividend += $dividendAmount;
                }
            }

            // Commit transaction
            DB::commit();

            return [
                'success' => true,
                'created' => $created,
                'total_dividend' => $totalDividend,
                'value_per_unit' => $valuePerUnit
            ];
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * จ่ายเงินปันผลให้กับสมาชิก
     *
     * @param MemberDividend $memberDividend เงินปันผลรายสมาชิกที่ต้องการจ่าย
     * @param string $paymentMethod วิธีการจ่ายเงิน
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return bool
     */
    public function payDividend(MemberDividend $memberDividend, string $paymentMethod, int $updatedBy): bool
    {
        // ตรวจสอบว่าสามารถจ่ายเงินปันผลได้หรือไม่
        if (!$memberDividend->canPay()) {
            return false;
        }

        // เริ่ม transaction
        DB::beginTransaction();

        try {
            // 1. อัพเดตข้อมูลการจ่ายเงินปันผล
            $status = $paymentMethod === Enum::MEMBER_DIVIDEND_PAYMENT_METHOD_DEPOSIT
                ? Enum::MEMBER_DIVIDEND_STATUS_TRANSFERRED
                : Enum::MEMBER_DIVIDEND_STATUS_PAID;

            $this->update($memberDividend, [
                'md_status' => $status,
                'md_payment_date' => now(),
                'md_payment_method' => $paymentMethod,
                'md_updated_by' => $updatedBy,
                'md_updated_date' => now()
            ]);

            // 2. บันทึกรายการทางบัญชี
            if ($paymentMethod === Enum::MEMBER_DIVIDEND_PAYMENT_METHOD_DEPOSIT) {
                // กรณีฝากเข้าบัญชี ให้บันทึกรายการฝากเงิน
                $this->accountTransactionService->createTransaction([
                    'at_account_id' => $memberDividend->account_id,
                    'at_type' => Enum::AT_TYPE_DI, // รับเงินปันผล
                    'at_amount' => $memberDividend->md_dividend_amount,
                    'at_date' => now(),
                    'at_note' => "รับเงินปันผลประจำปี {$memberDividend->md_year} (ฝากเข้าบัญชี)",
                    'at_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                    'at_created_by' => $updatedBy,
                    'at_updated_by' => $updatedBy
                ]);
            } else {
                // กรณีจ่ายเงินสดหรือโอนเงิน ให้บันทึกรายการรับเงินปันผล
                $this->accountTransactionService->createTransaction([
                    'at_account_id' => $memberDividend->account_id,
                    'at_type' => Enum::AT_TYPE_DI, // รับเงินปันผล
                    'at_amount' => $memberDividend->md_dividend_amount,
                    'at_date' => now(),
                    'at_note' => "รับเงินปันผลประจำปี {$memberDividend->md_year} (" . ($paymentMethod === Enum::MEMBER_DIVIDEND_PAYMENT_METHOD_CASH ? "เงินสด" : "โอนเงิน") . ")",
                    'at_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                    'at_created_by' => $updatedBy,
                    'at_updated_by' => $updatedBy
                ]);
            }

            // Commit transaction
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return false;
        }
    }

    /**
     * ยกเลิกการจ่ายเงินปันผล
     *
     * @param MemberDividend $memberDividend เงินปันผลรายสมาชิกที่ต้องการยกเลิก
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return bool
     */
    public function cancelDividend(MemberDividend $memberDividend, int $updatedBy): bool
    {
        // ตรวจสอบว่าสามารถยกเลิกการจ่ายเงินปันผลได้หรือไม่
        if (!$memberDividend->canCancel()) {
            return false;
        }

        // อัพเดตสถานะเป็นยกเลิก
        $this->updateStatus($memberDividend, Enum::MEMBER_DIVIDEND_STATUS_CANCELLED, $updatedBy);

        return true;
    }

    /**
     * จ่ายเงินปันผลให้กับสมาชิกทั้งหมดตามการจัดสรรเงินปันผล
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผล
     * @param string $paymentMethod วิธีการจ่ายเงิน
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return array สรุปผลการจ่ายเงินปันผล
     */
    public function payAllDividends(DividendAllocation $dividendAllocation, string $paymentMethod, int $updatedBy): array
    {
        // เริ่ม transaction
        DB::beginTransaction();

        try {
            // 1. ดึงข้อมูลเงินปันผลรายสมาชิกที่รอจ่าย
            $memberDividends = MemberDividend::where('da_id', $dividendAllocation->da_id)
                ->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_PENDING)
                ->get();

            // 2. จ่ายเงินปันผลให้กับสมาชิกแต่ละคน
            $paid = 0;
            $totalPaid = 0;

            foreach ($memberDividends as $memberDividend) {
                if ($this->payDividend($memberDividend, $paymentMethod, $updatedBy)) {
                    $paid++;
                    $totalPaid += $memberDividend->md_dividend_amount;
                }
            }

            // Commit transaction
            DB::commit();

            return [
                'success' => true,
                'paid' => $paid,
                'total_paid' => $totalPaid
            ];
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
