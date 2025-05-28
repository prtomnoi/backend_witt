<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\AnnualProfit;
use Illuminate\Support\Facades\DB;

/**
 * Class AnnualProfitService
 * @package App\Services
 */
class AnnualProfitService
{
    /**
     * สร้างข้อมูลผลประกอบการประจำปี
     *
     * @param array $data ข้อมูลผลประกอบการประจำปี
     * @return AnnualProfit
     */
    public function create(array $data): AnnualProfit
    {
        // กำหนดค่าเริ่มต้นสำหรับวันที่สร้างและอัพเดต
        $now = now();

        // คำนวณกำไรสุทธิ
        $netProfit = $data['ap_total_income'] - $data['ap_total_expense'];

        return AnnualProfit::create([
            'ap_year' => $data['ap_year'],
            'ap_total_income' => $data['ap_total_income'],
            'ap_total_expense' => $data['ap_total_expense'],
            'ap_net_profit' => $netProfit,
            'ap_close_date' => $data['ap_close_date'] ?? null,
            'ap_status' => $data['ap_status'] ?? Enum::ANNUAL_PROFIT_STATUS_OPEN,
            'ap_created_by' => $data['ap_created_by'],
            'ap_created_date' => $data['ap_created_date'] ?? $now,
            'ap_updated_by' => $data['ap_updated_by'],
            'ap_updated_date' => $data['ap_updated_date'] ?? $now,
        ]);
    }

    /**
     * อัพเดตข้อมูลผลประกอบการประจำปี
     *
     * @param AnnualProfit $annualProfit ผลประกอบการประจำปีที่ต้องการอัพเดต
     * @param array $data ข้อมูลที่ต้องการอัพเดต
     * @return AnnualProfit
     */
    public function update(AnnualProfit $annualProfit, array $data): AnnualProfit
    {
        // ถ้ามีการอัพเดตรายได้หรือค่าใช้จ่าย ให้คำนวณกำไรสุทธิใหม่
        if (isset($data['ap_total_income']) || isset($data['ap_total_expense'])) {
            $totalIncome = $data['ap_total_income'] ?? $annualProfit->ap_total_income;
            $totalExpense = $data['ap_total_expense'] ?? $annualProfit->ap_total_expense;
            $data['ap_net_profit'] = $totalIncome - $totalExpense;
        }

        // อัพเดตเฉพาะข้อมูลที่ส่งมา
        $annualProfit->update($data);

        return $annualProfit;
    }

    /**
     * ลบข้อมูลผลประกอบการประจำปี
     *
     * @param AnnualProfit $annualProfit ผลประกอบการประจำปีที่ต้องการลบ
     * @return bool|null
     */
    public function delete(AnnualProfit $annualProfit): ?bool
    {
        return $annualProfit->delete();
    }

    /**
     * ดึงข้อมูลผลประกอบการประจำปีทั้งหมด
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15)
    {
        return AnnualProfit::with(['createdBy', 'updatedBy'])
            ->orderByDesc('ap_year')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลผลประกอบการประจำปีตามปี
     *
     * @param string $year ปีที่ต้องการค้นหา
     * @return AnnualProfit|null
     */
    public function getByYear(string $year): ?AnnualProfit
    {
        return AnnualProfit::where('ap_year', $year)->first();
    }

    /**
     * ดึงข้อมูลผลประกอบการประจำปีตาม ID
     *
     * @param int $id รหัสผลประกอบการประจำปี
     * @return AnnualProfit|null
     */
    public function getById(int $id): ?AnnualProfit
    {
        return AnnualProfit::with(['createdBy', 'updatedBy'])->find($id);
    }

    /**
     * ดึงข้อมูลผลประกอบการประจำปีตามสถานะ
     *
     * @param string $status สถานะที่ต้องการค้นหา
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 15)
    {
        return AnnualProfit::with(['createdBy', 'updatedBy'])
            ->where('ap_status', $status)
            ->orderByDesc('ap_year')
            ->paginate($perPage);
    }

    /**
     * อัพเดตสถานะผลประกอบการประจำปี
     *
     * @param AnnualProfit $annualProfit ผลประกอบการประจำปีที่ต้องการอัพเดต
     * @param string $status สถานะใหม่
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return AnnualProfit
     */
    public function updateStatus(AnnualProfit $annualProfit, string $status, int $updatedBy): AnnualProfit
    {
        $data = [
            'ap_status' => $status,
            'ap_updated_by' => $updatedBy,
            'ap_updated_date' => now()
        ];

        // ถ้าเป็นการปิดงบ ให้บันทึกวันที่ปิดงบด้วย
        if ($status === Enum::ANNUAL_PROFIT_STATUS_CLOSED && !$annualProfit->ap_close_date) {
            $data['ap_close_date'] = now();
        }

        return $this->update($annualProfit, $data);
    }

    /**
     * คำนวณผลประกอบการประจำปีจากข้อมูลรายการธุรกรรม
     *
     * @param string $year ปีที่ต้องการคำนวณ
     * @param int $createdBy รหัสผู้สร้าง
     * @return AnnualProfit
     */
    public function calculateAnnualProfit(string $year, int $createdBy): AnnualProfit
    {
        // ค้นหาผลประกอบการประจำปีที่มีอยู่แล้ว
        $annualProfit = $this->getByYear($year);

        // คำนวณรายได้ทั้งหมดจากตาราง account_main_transaction
        $totalIncome = DB::table('account_main_transaction')
            ->whereYear('amt_date', $year)
            ->whereIn('amt_type', [
                Enum::AMT_TYPE_DP, // ฝากเงินเข้ากองทุน
                Enum::AMT_TYPE_LP, // รับชำระเงินกู้
                Enum::AMT_TYPE_FE, // รับค่าธรรมเนียม
                Enum::AMT_TYPE_PN  // รับค่าปรับ
            ])
            ->sum('amt_amount');

        // คำนวณค่าใช้จ่ายทั้งหมดจากตาราง account_main_transaction
        $totalExpense = DB::table('account_main_transaction')
            ->whereYear('amt_date', $year)
            ->whereIn('amt_type', [
                Enum::AMT_TYPE_WD, // ถอนเงินจากกองทุน
                Enum::AMT_TYPE_DO, // จ่ายเงินปันผล
                Enum::AMT_TYPE_WF  // จ่ายสวัสดิการ
            ])
            ->sum('amt_amount');

        // คำนวณกำไรสุทธิ
        $netProfit = $totalIncome - $totalExpense;

        // ถ้ามีข้อมูลอยู่แล้ว ให้อัพเดต
        if ($annualProfit) {
            return $this->update($annualProfit, [
                'ap_total_income' => $totalIncome,
                'ap_total_expense' => $totalExpense,
                'ap_net_profit' => $netProfit,
                'ap_updated_by' => $createdBy,
                'ap_updated_date' => now()
            ]);
        }

        // ถ้ายังไม่มีข้อมูล ให้สร้างใหม่
        return $this->create([
            'ap_year' => $year,
            'ap_total_income' => $totalIncome,
            'ap_total_expense' => $totalExpense,
            'ap_net_profit' => $netProfit,
            'ap_status' => Enum::ANNUAL_PROFIT_STATUS_OPEN,
            'ap_created_by' => $createdBy,
            'ap_updated_by' => $createdBy
        ]);
    }

    /**
     * จัดสรรผลกำไรประจำปี
     *
     * @param AnnualProfit $annualProfit ผลประกอบการประจำปีที่ต้องการจัดสรร
     * @param array $distribution ข้อมูลการจัดสรร [account_no => percentage]
     * @param int $updatedBy รหัสผู้อัพเดต
     * @return bool
     */
    public function distributeProfit(AnnualProfit $annualProfit, array $distribution, int $updatedBy): bool
    {
        // ตรวจสอบว่าปิดงบแล้วหรือยัง
        if ($annualProfit->ap_status !== Enum::ANNUAL_PROFIT_STATUS_CLOSED) {
            return false;
        }

        // เริ่ม transaction
        DB::beginTransaction();

        try {
            // วนลูปจัดสรรผลกำไรตามสัดส่วนที่กำหนด
            foreach ($distribution as $accountNo => $percentage) {
                $amount = ($annualProfit->ap_net_profit * $percentage) / 100;

                // บันทึกรายการจัดสรรผลกำไรในตาราง account_main_transaction
                DB::table('account_main_transaction')->insert([
                    'amt_account_no' => $accountNo,
                    'amt_type' => Enum::AMT_TYPE_AL, // จัดสรรผลกำไร
                    'amt_amount' => $amount,
                    'amt_date' => now(),
                    'amt_note' => "จัดสรรผลกำไรประจำปี {$annualProfit->ap_year} ({$percentage}%)",
                    'amt_payment_type' => Enum::TRANSACTION_PAYMENT_TYPE_S, // บวก
                    'amt_created_by' => $updatedBy,
                    'amt_created_date' => now(),
                    'amt_updated_by' => $updatedBy,
                    'amt_updated_date' => now()
                ]);
            }

            // อัพเดตสถานะเป็นจัดสรรผลกำไรแล้ว
            $this->updateStatus($annualProfit, Enum::ANNUAL_PROFIT_STATUS_DISTRIBUTED, $updatedBy);

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
