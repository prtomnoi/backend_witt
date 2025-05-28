<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\DividendAllocation;
use App\Models\AnnualProfit;
use Illuminate\Support\Facades\DB;

/**
 * Class DividendAllocationService
 * @package App\Services
 */
class DividendAllocationService
{
    /**
     * @var AnnualProfitService
     */
    protected $annualProfitService;

    /**
     * DividendAllocationService constructor.
     *
     * @param AnnualProfitService $annualProfitService
     */
    public function __construct(AnnualProfitService $annualProfitService)
    {
        $this->annualProfitService = $annualProfitService;
    }

    /**
     * สร้างข้อมูลการจัดสรรเงินปันผล
     *
     * @param array $data ข้อมูลการจัดสรรเงินปันผล
     * @return DividendAllocation
     */
    public function create(array $data): DividendAllocation
    {
        // กำหนดค่าเริ่มต้นสำหรับวันที่สร้างและอัพเดต
        $now = now();

        return DividendAllocation::create([
            'ap_id' => $data['ap_id'],
            'da_total_amount' => $data['da_total_amount'],
            'da_contribution_amount' => $data['da_contribution_amount'],
            'da_welfare_amount' => $data['da_welfare_amount'],
            'da_distribution_date' => $data['da_distribution_date'] ?? null,
            'da_status' => $data['da_status'] ?? Enum::DIVIDEND_ALLOCATION_STATUS_PENDING,
            'da_created_by' => $data['da_created_by'],
            'da_created_date' => $data['da_created_date'] ?? $now,
            'da_updated_by' => $data['da_updated_by'],
            'da_updated_date' => $data['da_updated_date'] ?? $now,
        ]);
    }

    /**
     * อัพเดตข้อมูลการจัดสรรเงินปันผล
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผลที่ต้องการอัพเดต
     * @param array $data ข้อมูลที่ต้องการอัพเดต
     * @return DividendAllocation
     */
    public function update(DividendAllocation $dividendAllocation, array $data): DividendAllocation
    {
        // อัพเดตเฉพาะข้อมูลที่ส่งมา
        $dividendAllocation->update($data);

        return $dividendAllocation;
    }

    /**
     * ลบข้อมูลการจัดสรรเงินปันผล
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผลที่ต้องการลบ
     * @return bool|null
     */
    public function delete(DividendAllocation $dividendAllocation): ?bool
    {
        return $dividendAllocation->delete();
    }

    /**
     * ดึงข้อมูลการจัดสรรเงินปันผลทั้งหมด
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15)
    {
        return DividendAllocation::with(['annualProfit', 'createdBy', 'updatedBy'])
            ->orderByDesc('da_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลการจัดสรรเงินปันผลตาม ID
     *
     * @param int $id รหัสการจัดสรรเงินปันผล
     * @return DividendAllocation|null
     */
    public function getById(int $id): ?DividendAllocation
    {
        return DividendAllocation::with(['annualProfit', 'createdBy', 'updatedBy'])->find($id);
    }

    /**
     * ดึงข้อมูลการจัดสรรเงินปันผลตามผลประกอบการประจำปี
     *
     * @param int $apId รหัสผลประกอบการประจำปี
     * @return DividendAllocation|null
     */
    public function getByAnnualProfitId(int $apId): ?DividendAllocation
    {
        return DividendAllocation::with(['annualProfit', 'createdBy', 'updatedBy'])
            ->where('ap_id', $apId)
            ->first();
    }

    /**
     * ดึงข้อมูลการจัดสรรเงินปันผลตามสถานะ
     *
     * @param string $status สถานะที่ต้องการค้นหา
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 15)
    {
        return DividendAllocation::with(['annualProfit', 'createdBy', 'updatedBy'])
            ->where('da_status', $status)
            ->orderByDesc('da_created_date')
            ->paginate($perPage);
    }

    /**
     * อัพเดตสถานะการจัดสรรเงินปันผล
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผลที่ต้องการอัพเดต
     * @param string $status สถานะใหม่
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return DividendAllocation
     */
    public function updateStatus(DividendAllocation $dividendAllocation, string $status, int $updatedBy): DividendAllocation
    {
        $data = [
            'da_status' => $status,
            'da_updated_by' => $updatedBy,
            'da_updated_date' => now()
        ];

        // ถ้าเป็นการอนุมัติ ให้บันทึกวันที่จัดสรรด้วย
        if ($status === Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED && !$dividendAllocation->da_distribution_date) {
            $data['da_distribution_date'] = now();
        }

        return $this->update($dividendAllocation, $data);
    }

    /**
     * คำนวณการจัดสรรเงินปันผลจากผลประกอบการประจำปี
     *
     * @param AnnualProfit $annualProfit ผลประกอบการประจำปี
     * @param int $createdBy รหัสผู้สร้าง
     * @return DividendAllocation
     */
    public function calculateDividendAllocation(AnnualProfit $annualProfit, int $createdBy): DividendAllocation
    {
        // ค้นหาการจัดสรรเงินปันผลที่มีอยู่แล้ว
        $dividendAllocation = $this->getByAnnualProfitId($annualProfit->ap_id);

        // คำนวณจำนวนเงินที่จัดสรรตามสัดส่วนที่กำหนด
        $totalAmount = ($annualProfit->ap_net_profit * Enum::DIVIDEND_ALLOCATION_PERCENT) / 100;
        $contributionAmount = ($annualProfit->ap_net_profit * Enum::CONTRIBUTION_ALLOCATION_PERCENT) / 100;
        $welfareAmount = ($annualProfit->ap_net_profit * Enum::WELFARE_ALLOCATION_PERCENT) / 100;

        // ถ้ามีข้อมูลอยู่แล้ว ให้อัพเดต
        if ($dividendAllocation) {
            return $this->update($dividendAllocation, [
                'da_total_amount' => $totalAmount,
                'da_contribution_amount' => $contributionAmount,
                'da_welfare_amount' => $welfareAmount,
                'da_updated_by' => $createdBy,
                'da_updated_date' => now()
            ]);
        }

        // ถ้ายังไม่มีข้อมูล ให้สร้างใหม่
        return $this->create([
            'ap_id' => $annualProfit->ap_id,
            'da_total_amount' => $totalAmount,
            'da_contribution_amount' => $contributionAmount,
            'da_welfare_amount' => $welfareAmount,
            'da_status' => Enum::DIVIDEND_ALLOCATION_STATUS_PENDING,
            'da_created_by' => $createdBy,
            'da_updated_by' => $createdBy
        ]);
    }

    /**
     * แจกจ่ายเงินปันผลให้กับสมาชิก
     *
     * @param DividendAllocation $dividendAllocation การจัดสรรเงินปันผลที่ต้องการแจกจ่าย
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return bool
     */
    public function distributeDividend(DividendAllocation $dividendAllocation, int $updatedBy): bool
    {
        // ตรวจสอบว่าอนุมัติแล้วหรือยัง
        if ($dividendAllocation->da_status !== Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED) {
            return false;
        }

        // เริ่ม transaction
        DB::beginTransaction();

        try {
            // 1. จัดสรรเงินปันผลเข้าบัญชีหลัก

            // 1.1 จัดสรรเงินปันผล (60%)
            DB::table('account_main_transaction')->insert([
                'amt_account_no' => Enum::ACCOUNT_DIVIDEND,
                'amt_type' => Enum::AMT_TYPE_DI, // จัดสรรเงินปันผล
                'amt_amount' => $dividendAllocation->da_total_amount,
                'amt_date' => now(),
                'amt_note' => "จัดสรรเงินปันผลประจำปี {$dividendAllocation->annualProfit->ap_year}",
                'amt_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                'amt_created_by' => $updatedBy,
                'amt_created_date' => now(),
                'amt_updated_by' => $updatedBy,
                'amt_updated_date' => now()
            ]);

            // 1.2 จัดสรรเงินสมทบ (10%)
            DB::table('account_main_transaction')->insert([
                'amt_account_no' => Enum::ACCOUNT_CONTRIBUTION,
                'amt_type' => Enum::AMT_TYPE_AL, // จัดสรรผลกำไร
                'amt_amount' => $dividendAllocation->da_contribution_amount,
                'amt_date' => now(),
                'amt_note' => "จัดสรรเงินสมทบประจำปี {$dividendAllocation->annualProfit->ap_year}",
                'amt_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                'amt_created_by' => $updatedBy,
                'amt_created_date' => now(),
                'amt_updated_by' => $updatedBy,
                'amt_updated_date' => now()
            ]);

            // 1.3 จัดสรรเงินสวัสดิการ (30%)
            DB::table('account_main_transaction')->insert([
                'amt_account_no' => Enum::ACCOUNT_WELFARE,
                'amt_type' => Enum::AMT_TYPE_AL, // จัดสรรผลกำไร
                'amt_amount' => $dividendAllocation->da_welfare_amount,
                'amt_date' => now(),
                'amt_note' => "จัดสรรเงินสวัสดิการประจำปี {$dividendAllocation->annualProfit->ap_year}",
                'amt_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                'amt_created_by' => $updatedBy,
                'amt_created_date' => now(),
                'amt_updated_by' => $updatedBy,
                'amt_updated_date' => now()
            ]);

            // 2. แจกจ่ายเงินปันผลให้กับสมาชิก (ตามสัดส่วนหุ้น)

            // 2.1 ดึงข้อมูลสมาชิกและจำนวนหุ้น
            $members = DB::table('account')
                ->where('account_status', Enum::ACCOUNT_STATUS_A) // สถานะใช้งาน
                ->get(['account_id', 'account_no', 'account_unit_balance']);

            // 2.2 คำนวณมูลค่าต่อหุ้น
            $totalUnits = $members->sum('account_unit_balance');
            $valuePerUnit = $totalUnits > 0 ? $dividendAllocation->da_total_amount / $totalUnits : 0;

            // 2.3 แจกจ่ายเงินปันผลให้กับสมาชิกแต่ละคน
            foreach ($members as $member) {
                if ($member->account_unit_balance > 0) {
                    $dividendAmount = $member->account_unit_balance * $valuePerUnit;

                    // บันทึกรายการรับเงินปันผลในตาราง account_transaction
                    DB::table('account_transaction')->insert([
                        'at_account_id' => $member->account_id,
                        'at_type' => Enum::AT_TYPE_DI, // รับเงินปันผล
                        'at_amount' => $dividendAmount,
                        'at_date' => now(),
                        'at_note' => "รับเงินปันผลประจำปี {$dividendAllocation->annualProfit->ap_year} (จำนวน {$member->account_unit_balance} หุ้น)",
                        'at_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                        'at_created_by' => $updatedBy,
                        'at_created_date' => now(),
                        'at_updated_by' => $updatedBy,
                        'at_updated_date' => now()
                    ]);

                    // บันทึกรายการจ่ายเงินปันผลในตาราง account_main_transaction
                    DB::table('account_main_transaction')->insert([
                        'amt_account_no' => Enum::ACCOUNT_DIVIDEND,
                        'amt_type' => Enum::AMT_TYPE_DO, // จ่ายเงินปันผล
                        'amt_amount' => $dividendAmount,
                        'amt_date' => now(),
                        'amt_note' => "จ่ายเงินปันผลประจำปี {$dividendAllocation->annualProfit->ap_year} ให้กับบัญชี {$member->account_no}",
                        'amt_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_D, // ลบ
                        'amt_created_by' => $updatedBy,
                        'amt_created_date' => now(),
                        'amt_updated_by' => $updatedBy,
                        'amt_updated_date' => now()
                    ]);
                }
            }

            // 3. อัพเดตสถานะเป็นแจกจ่ายเงินปันผลแล้ว
            $this->updateStatus($dividendAllocation, Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED, $updatedBy);

            // 4. อัพเดตสถานะผลประกอบการประจำปีเป็นจัดสรรผลกำไรแล้ว
            $this->annualProfitService->updateStatus(
                $dividendAllocation->annualProfit,
                Enum::ANNUAL_PROFIT_STATUS_DISTRIBUTED,
                $updatedBy
            );

            // Commit transaction
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            return false;
        }
    }
}
