<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\Account;
use App\Models\AccountMainTransaction;
use App\Models\AccountTransaction;
use App\Models\GuarantorLoan;
use App\Models\InstallmentPayment;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanInstallmentPaymentService
{
    /**
     * ดึงข้อมูลการผ่อนชำระทั้งหมดพร้อม pagination
     */
    public function getAllInstallments(int $perPage = 10): LengthAwarePaginator
    {
        return InstallmentPayment::with(['createdBy', 'updatedBy'])
            ->orderBy('ip_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลสัญญาทั้งหมดพร้อม pagination
     */
    public function getAllLoans(int $perPage = 10): LengthAwarePaginator
    {
        return Loan::with(['account' , 'guarantors'])
            ->orderBy('loan_id', 'desc')
            ->paginate($perPage);
    }

        public function getLoansByType(?string $loanType)
    {
        $query = Loan::with(['account', 'installmentPayments', 'guarantors']);

        if (!empty($loanType)) {
            $query->where('loan_type', $loanType);
        }

        return $query->orderBy('loan_id', 'desc')->paginate(10);
    }


    /**
     * ค้นหาการผ่อนชำระตาม ID
     */
    public function findInstallmentById(int $id): InstallmentPayment
    {
        return InstallmentPayment::with(['account', 'user', 'createdBy', 'updatedBy'])
            ->findOrFail($id);
    }

    /**
     * นับจำนวน InstallmentPayment ตาม loan_id
     *
     * @param int $loanId รหัสเงินกู้
     * @return int จำนวน InstallmentPayment ที่พบ
     */
    public function countInstallmentsByLoanId(int $loanId): int
    {
        return InstallmentPayment::where('loan_id', $loanId)->count();
    }

    /**
     * ค้นหาสัญญาตาม ID
     */
    public function findLoanById(int $id): Loan
    {
        return Loan::with(['account' , 'guarantors'])
            ->findOrFail($id);
    }

    /**
     * นับจำนวนเงินกู้ตาม account_id
     *
     * @param int $accountId
     * @return int
     */
    public function countLoansByAccountId(int $accountId): int
    {
        $count = Loan::where('account_id', $accountId)->count();
        return $count > 0 ? $count : 1;
    }

    /**
     * ค้นหารายการเงินกู้ที่มีสถานะรอพิจารณา
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findWaitLoans()
    {
        return Loan::where('loan_status', Enum::LOAN_STATUS_W)->get();
    }

    /**
     * สร้างข้อมูลสัญญาการผ่อนชำระใหม่
     *
     * @param array $data ข้อมูลสำหรับการสร้างเงินกู้
     * @return Loan เงินกู้ที่สร้างขึ้นใหม่
     * @throws \Exception ถ้าเกิดข้อผิดพลาดในการสร้างเงินกู้
     */
    private function saveLoan(array $data): Loan
    {
        try {
            $loan = new Loan();
            $loan->account_id = $data['account_id'];
            $loan->loan_no = $data['loan_no'];
            $loan->loan_date = now();
            $loan->loan_name = $data['loan_name'];
            $loan->loan_total = $data['loan_total'];
            $loan->loan_balance = $data['loan_balance'];
            $loan->loan_rate = $data['loan_rate'];
            $loan->loan_rate_pay = $data['loan_rate_pay'] ?? 0; // เพิ่มฟิลด์ใหม่
            $loan->loan_type = $data['loan_type'];
            $loan->loan_period = $data['loan_period'];
            $loan->loan_status = $data['loan_status'];
            $loan->loan_created_by = Auth::id() ?? 0;
            $loan->loan_created_date = now();
            $loan->loan_updated_by = Auth::id() ?? 0;
            $loan->loan_updated_date = now();

            $loan->save();

            return $loan;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * สร้างสัญญาผ่อนชำระ
     *
     * @param int $accountId รหัสบัญชีที่จะสร้างแผนการผ่อน
     * @param int $periods จำนวนงวดที่ต้องการผ่อน
     * @param float $amount จำนวนเงินที่กู้
     * @param string $loanType ประเภทการขอกู้
     * @param array $guarantorIds รหัสบัญชีของผู้ค้ำประกัน
     * @param array $guarantorData ข้อมูลเพิ่มเติมของผู้ค้ำประกัน
     * @return Loan สัญญาเงินกู้ที่สร้างขึ้นใหม่
     * @throws \Exception ถ้าเกิดข้อผิดพลาดในการสร้างสัญญา
     */
    public function createNewLoan(int $accountId, int $periods, float $amount, string $loanType, array $guarantorIds, array $guarantorData = []): Loan
    {
        // เริ่ม transaction เพื่อความสมบูรณ์ของข้อมูล
        DB::beginTransaction();
        try {
            $accountService = app(AccountService::class);
            // ดึงข้อมูลบัญชีจาก accountId
            $account = $accountService->findAccountById($accountId);

            // ดึงค่า config สำหรับอัตราดอกเบี้ย
            $systemConfigService = app(SystemConfigService::class);
            $configLoanRate = $systemConfigService->getConfigKeyByTypeAndName(
                Enum::SYSTEM_CONFIG_TYPE_RATE,
                Enum::SYSTEM_CONFIG_NAME_LOAN_RATE
            );

            // สร้างชื่อสัญญาเงินกู้
            $loanName = $account->account_name . " ได้ทำการกู้" . Enum::getLoanTypeDescription($loanType);

            // สร้างเลขที่สัญญาเงินกู้
            $loanNo = $accountId . "/" . $this->countLoansByAccountId($accountId);

            // สร้างสัญญาเงินกู้ใหม่
            $loan = $this->saveLoan([
                'account_id' => $accountId,
                'loan_no' => $loanNo,
                'loan_name' => $loanName,
                'loan_total' => $amount,
                'loan_balance' => $amount,
                'loan_rate' => $configLoanRate !== null ? floatval($configLoanRate) : 0.0,
                'loan_rate_pay' => 0, // กำหนดค่าเริ่มต้นเป็น 0
                'loan_type' => $loanType,
                'loan_status' => Enum::LOAN_STATUS_W,
                'loan_period' => $periods,
                'loan_created_by' => Auth::id() ?? 0,
                'loan_created_date' => now(),
                'loan_updated_by' => Auth::id() ?? 0,
                'loan_updated_date' => now()
            ]);

            // บันทึกข้อมูลผู้ค้ำประกันและอัพเดทสถานะบัญชี
            foreach ($guarantorIds as $guarantorId) {
                if (!empty($guarantorId)) {
                    // อัพเดทสถานะบัญชีของผู้ค้ำประกัน
                    $this->updateAccountStatus($guarantorId, Enum::ACCOUNT_STATUS_G);

                    // ดึงข้อมูลบัญชีของผู้ค้ำประกันเพื่อหา user_id
                    $guarantorAccount = $accountService->findAccountById($guarantorId);

                    if (!$guarantorAccount) {
                        throw new \Exception("ไม่พบข้อมูลบัญชีของผู้ค้ำประกันรหัส {$guarantorId}");
                    }

                    // บันทึกข้อมูลในตาราง GuarantorLoan
                    $guarantorLoan = new GuarantorLoan();
                    $guarantorLoan->loan_id = $loan->loan_id;
                    $guarantorLoan->user_id = $guarantorAccount->user_id; // ดึง user_id จากบัญชีผู้ค้ำประกัน
                    $guarantorLoan->account_id = $guarantorId;
                    $guarantorLoan->guarantor_account_balance = $guarantorData[$guarantorId]['account_balance'] ?? 0;
                    $guarantorLoan->guarantor_loan_spouse_name = $guarantorData[$guarantorId]['spouse_name'] ?? null;
                    $guarantorLoan->guarantor_loan_spouse_number = $guarantorData[$guarantorId]['spouse_number'] ?? null;
                    $guarantorLoan->guarantor_loan_created_by = Auth::id() ?? 0;
                    $guarantorLoan->guarantor_loan_created_date = now();
                    $guarantorLoan->save();
                }
            }

            // อัพเดทสถานะบัญชีของผู้กู้
            $this->updateAccountStatus($accountId, Enum::ACCOUNT_STATUS_L);

            // ยืนยัน transaction
            DB::commit();
            return $loan;
        } catch (\Exception $e) {
            // ยกเลิก transaction ในกรณีที่เกิดข้อผิดพลาด
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * บันทึกข้อมูลผู้ค้ำประกัน
     *
     * @param array $data ข้อมูลผู้ค้ำประกัน
     * @return GuarantorLoan
     * @throws \Exception
     */
    private function saveGuarantor(array $data): GuarantorLoan
    {
        try {
            $guarantorLoan = new GuarantorLoan();

            $guarantorLoan->loan_id = $data['loan_id'];
            $guarantorLoan->user_id = $data['user_id'];
            $guarantorLoan->account_id = $data['account_id'];
            $guarantorLoan->guarantor_account_balance = $data['guarantor_account_balance'] ?? 0;
            $guarantorLoan->guarantor_loan_spouse_name = $data['guarantor_loan_spouse_name'] ?? null;
            $guarantorLoan->guarantor_loan_spouse_number = $data['guarantor_loan_spouse_number'] ?? null;
            $guarantorLoan->guarantor_loan_created_by = Auth::id() ?? 0;
            $guarantorLoan->guarantor_loan_created_date = now();

            $guarantorLoan->save();

            return $guarantorLoan;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * อัพเดทสถานะบัญชี
     *
     * @param int $accountId รหัสบัญชี
     * @param string $status สถานะใหม่
     */
    private function updateAccountStatus(int $accountId, string $status): void
    {

        if (empty($accountId)) {
            throw new \Exception("Cannot update account status. Account ID is null.");
        }
        $accountService = app(AccountService::class);
        // ดึงข้อมูลบัญชีจาก accountId
        $account = $accountService->findAccountById($accountId);
        $account->account_status = $status;
        $account->account_updated_by = Auth::id() ?? 0;
        $account->account_updated_date = now();
        $account->save();
    }

    /**
     * สร้างข้อมูลการจ่ายผ่อนชำระใหม่
     *
     * @param array $data ข้อมูลการผ่อนชำระ
     * @param int $accountId รหัสบัญชี
     * @return InstallmentPayment
     * @throws \Exception
     */
    public function savePayInstallment(array $data, int $accountId): InstallmentPayment
    {
        DB::beginTransaction();
        try {
            // สร้างรายการผ่อนชำระใหม่
            $installment = $this->createInstallmentPayment($data);

            $accountService = app(AccountService::class);
            $account = $accountService->findAccountById($accountId);
            $mainAccount = $accountService->findAccountById(Enum::ACCOUNT_SAVINGS);

            $loan = $this->findLoanById($data['loan_id']);
            $loan->loan_balance -= $data['ip_amount'];
            $loan->loan_updated_by = Auth::id() ?? 0;
            $loan->loan_updated_date = now();
            $loan->save();

            if ($loan->loan_balance <= 0) {
                // ปรับสถานะบัญชีของผู้กู้เป็น 'A'
                $account->account_status = Enum::ACCOUNT_STATUS_A;
                $account->account_updated_by = Auth::id() ?? 0;
                $account->account_updated_date = now();
                $account->save();

                // ปรับสถานะบัญชีของผู้ค้ำประกันเป็น 'A'
                $guarantorLoans = $this->findGuarantorsByLoanId($loan->loan_id);
                foreach ($guarantorLoans as $guarantorLoan) {
                    $guarantorAccount = $accountService->findAccountById($guarantorLoan->account_id);
                    $guarantorAccount->account_status = Enum::ACCOUNT_STATUS_A;
                    $guarantorAccount->account_updated_by = Auth::id() ?? 0;
                    $guarantorAccount->account_updated_date = now();
                    $guarantorAccount->save();
                }
            }

            // บันทึกธุรกรรมสำหรับเงินต้น
            $at = null;
            if ($data['ip_amount'] > 0) {
                $at = $this->createAccountTransaction($accountId, $account->user_id, $data, Enum::AT_TYPE_LD);
            }

            // บันทึกธุรกรรมสำหรับดอกเบี้ย
            if ($data['ip_interest'] > 0) {
                $this->createAccountTransaction($accountId, $account->user_id, $data, Enum::AT_TYPE_LD, 'ip_interest');
            }

            // อัพเดทยอดเงินในบัญชีหลัก
            $this->updateMainAccountBalance($mainAccount, $data);

            // บันทึกธุรกรรมหลักสำหรับเงินต้นและดอกเบี้ย
            $this->createMainAccountTransactions($accountId, $account->user_id, $data, $at ? $at->at_id : null);

            DB::commit();
            return $installment;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function createInstallmentPayment(array $data): InstallmentPayment
    {
        $installment = new InstallmentPayment();
        $installment->loan_id = $data['loan_id'];
        $installment->ip_year = $data['ip_year'];
        $installment->ip_month = $data['ip_month'];
        $installment->ip_date = $data['ip_date'];
        $installment->ip_unit = $data['ip_unit'];
        $installment->ip_amount = $data['ip_amount'];
        $installment->ip_rate = $data['ip_rate'];
        $installment->ip_interest = $data['ip_interest'];
        $installment->ip_period_no = $data['ip_period_no'];
        $installment->ip_pay_type = $data['ip_pay_type'];
        $installment->ip_created_by = Auth::id();
        $installment->ip_created_date = now();
        $installment->ip_updated_by = Auth::id();
        $installment->ip_updated_date = now();
        $installment->save();
        return $installment;
    }

    private function createAccountTransaction(int $accountId, int $userId, array $data, string $type, string $amountField = 'ip_amount')
    {
        $at = new AccountTransaction();
        $at->account_no = $accountId;
        $at->user_id = $userId;
        $at->at_unit = $data['ip_unit'];
        $at->at_amount = $data[$amountField];
        $at->at_type = $type;
        $at->save();
        return $at;
    }

    private function updateMainAccountBalance(Account $mainAccount, array $data)
    {
        $mainAccount->account_balance += ($data['ip_amount'] + $data['ip_interest']);
        $mainAccount->account_updated_by = Auth::id();
        $mainAccount->account_updated_date = now();
        $mainAccount->save();
    }

    private function createMainAccountTransactions(int $accountId, int $userId, array $data, ?int $referenceId)
    {
        if ($data['ip_amount'] > 0) {
            $this->createMainAccountTransaction($accountId, $userId, $data, 'ip_amount', $referenceId);
        }
        if ($data['ip_interest'] > 0) {
            $this->createMainAccountTransaction($accountId, $userId, $data, 'ip_interest', $referenceId);
        }
    }

    private function createMainAccountTransaction(int $accountId, int $userId, array $data, string $amountField, ?int $referenceId)
    {
        $atMain = new AccountMainTransaction();
        $atMain->account_no = $accountId;
        $atMain->user_id = $userId;
        $atMain->amt_unit = $data['ip_unit'];
        $atMain->amt_amount = $data[$amountField];
        $atMain->amt_type = Enum::AT_TYPE_LP;
        $atMain->reference_id = $referenceId;
        $atMain->save();
    }

    /**
     * ดึงค่าการผ่อนชำระใหม่
     *
     * @param array $data ข้อมูลสำหรับการผ่อนชำระ
     * @param string $payType ประเภทการชำระเงิน
     * @return object ข้อมูลการผ่อนชำระ
     * @throws \Exception
     */
    public function getPayInstallmentByPayType(array $data, string $payType): object
    {
        try {
            $loan = $this->findLoanById($data['loan_id'])->load(['installmentPayments' => function ($query) {
                $query->where('ip_pay_type', Enum::IP_PAY_TYPE_A)
                    ->latest('ip_id')
                    ->take(1);
            }]);

            $countInstallments = $this->countInstallmentsByLoanId($data['loan_id']);

            $lastInstallmentPayment = $loan->installmentPayments->first();

            $currentDate = now();
            $currentYear = $currentDate->year + 543; // Convert to Buddhist year
            $currentMonth = $currentDate->format('m');

            $dataReturn = new \stdClass();

            $dataReturn->ip_period_no = $countInstallments + 1;
            $dataReturn->ip_pay_type = $payType;
            $dataReturn->ip_year = $currentYear;
            $dataReturn->ip_date = $currentDate;
            $dataReturn->ip_month = $currentMonth;

            if ($lastInstallmentPayment !== null) {
                $dataReturn->ip_unit = $lastInstallmentPayment->ip_unit;
                $dataReturn->ip_rate = $lastInstallmentPayment->ip_rate;
                $dataReturn->ip_interest = $lastInstallmentPayment->ip_interest;
                $dataReturn->ip_amount = $payType == Enum::IP_PAY_TYPE_A ? $lastInstallmentPayment->ip_amount : 0;
            } else {
                $dataReturn->ip_rate = $loan->loan_rate ?? 0;
                $dataReturn->ip_interest = $this->calculateInterestPerPeriod($loan->loan_total, $loan->loan_period, $loan->loan_rate);
                $dataReturn->ip_amount = $payType == Enum::IP_PAY_TYPE_A ? ($loan->loan_total / $loan->loan_period) : 0;
            }

            return $dataReturn;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * คำนวณดอกเบี้ย
     */
    private function calculateInterest(float $amount, float $rate): float
    {
        return ($amount * $rate) / 100;
    }

    /**
     * คำนวณดอกเบี้ยต่องวด
     *
     * @param float $totalAmount จำนวนเงินกู้ทั้งหมด
     * @param int $periods จำนวนงวด
     * @param float $ratePerMonth อัตราดอกเบี้ยต่อเดือน (ยังไม่หารด้วย 100)
     * @return float ดอกเบี้ยต่องวด
     */
    private function calculateInterestPerPeriod(float $totalAmount, int $periods, float $ratePerMonth): float
    {
        $amountPerPeriod = $totalAmount / $periods;
        $ratePerMonth = $ratePerMonth / 100; // แปลงเป็นเปอร์เซ็นต์
        return $amountPerPeriod * $ratePerMonth;
    }

//    /**
//     * สร้างแผนการผ่อนชำระ
//     *
//     * @param Account $account บัญชีที่จะสร้างแผนการผ่อน
//     * @param int $periods จำนวนงวดที่ต้องการผ่อน
//     * @param float $amount จำนวนเงินทั้งหมด
//     * @param float $rate อัตราดอกเบี้ย
//     * @return Collection
//     * @throws \Exception
//     */
//    public function createInstallmentPlan(Account $account, int $periods, float $amount, float $rate): Collection
//    {
//        DB::beginTransaction();
//        $monthNames = Enum::getMonthsDropdown(); // ดึงชื่อเดือนภาษาไทยจาก Enum
//        try {
//            $installments = collect();
//            $startDate = now()->addMonth(); // เริ่มเดือนถัดไป
//            $amountPerPeriod = $amount / $periods;
//
//            // คำนวณดอกเบี้ยต่องวด
//            $interestPerPeriod = ($amountPerPeriod * $rate) / 100; // ดอกเบี้ยต่องวดจากเรทที่รับมา
//
//            for ($i = 1; $i <= $periods; $i++) {
//                $paymentDate = $startDate->copy()->addMonths($i - 1);
//                $monthNumber = $paymentDate->format('m'); // ดึงตัวเลขเดือน
//
//                $installment = $this->createInstallment([
////                    'account_id' => $account->account_id,
////                    'user_id' => $account->user_id,
//                    'ip_year' => $paymentDate->year + 543,
//                    'ip_month' => $monthNames[$monthNumber],
//                    'ip_date' => $paymentDate,
//                    'ip_unit' => 1, // default unit
//                    'ip_amount' => $amountPerPeriod,
//                    'ip_rate' => $rate,
//                    'ip_interest' => $interestPerPeriod, // เพิ่มดอกเบี้ยต่องวด
//                    'ip_period_no' => $i
//                ]);
//
//                $installments->push($installment);
//            }
//
//            DB::commit();
//            return $installments;
//        } catch (\Exception $e) {
//            DB::rollback();
//            throw $e;
//        }
//    }


    /**
     * อัพเดทการผ่อนชำระ
     */
    public function updateInstallment(InstallmentPayment $installment, array $data): InstallmentPayment
    {
        DB::beginTransaction();
        try {
            $installment->fill($data);
            $installment->ip_interest = $this->calculateInterest($data['ip_amount'], $data['ip_rate']);
            $installment->ip_updated_by = Auth::id();
            $installment->ip_updated_date = now();
            $installment->save();

            DB::commit();
            return $installment;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูลการผ่อนชำระตามบัญชี
     */
    public function getInstallmentsByLoan(int $loanId): Collection
    {
        return InstallmentPayment::where('loan_id', $loanId)
            ->orderBy('ip_year', 'desc')
            ->orderBy('ip_month', 'desc')
            ->get();
    }

    /**
     * ดึงข้อมูลการผ่อนชำระตามปีและเดือน
     */
    public function getInstallmentsByYearMonth(int $year, ?int $month = null): Collection
    {
        $query = InstallmentPayment::where('ip_year', $year);

        if ($month) {
            $query->where('ip_month', $month);
        }

        return $query->orderBy('ip_date', 'asc')->get();
    }


    /**
     * อนุมัติเงินกู้
     *
     * @param int $loanId รหัสเงินกู้ที่ต้องการอนุมัติ
     * @param int $meetingDocId รหัสเอกสารการประชุม
     * @return Loan เงินกู้ที่ได้รับการอนุมัติ
     * @throws \Exception ถ้าเกิดข้อผิดพลาดในการอนุมัติเงินกู้
     */
    public function approveLoan(int $loanId, int $meetingDocId): Loan
    {
        DB::beginTransaction();
        try {
            // ดึงข้อมูลเงินกู้
            $loan = $this->findLoanById($loanId);

            // ตรวจสอบสถานะปัจจุบันของเงินกู้
            if ($loan->loan_status !== Enum::LOAN_STATUS_W) {
                throw new \Exception('เงินกู้นี้ไม่อยู่ในสถานะรอการอนุมัติ');
            }

            // อัปเดตสถานะเงินกู้เป็น 'A' (อนุมัติ)
            $loan->loan_status = Enum::LOAN_STATUS_A;
            $loan->loan_approved_by = Auth::id() ?? 0;
            $loan->loan_approved_date = now();
            $loan->save();

            // กำหนดประเภทเอกสารการประชุม
            $umdmType = ($loan->loan_type == Enum::LOAN_TYPE_N) ? Enum::UMDM_TYPE_LNACC : Enum::UMDM_TYPE_LNEACC;

            // อัปเดตสถานะบัญชีเป็น 'L' (กู้เงิน)
            $accountService = app(AccountService::class);
            $account = $accountService->findAccountById($loan->account_id);

            if (!$account) {
                throw new \Exception('ไม่พบข้อมูลบัญชีที่เกี่ยวข้องกับเงินกู้นี้');
            }

            $account->account_status = Enum::ACCOUNT_STATUS_L;
            $account->account_updated_by = Auth::id() ?? 0;
            $account->account_updated_date = now();
            $account->save();

            // สร้างการเชื่อมโยงระหว่างผู้ใช้และเอกสารการประชุม
            $this->createUserMeetingDocMapping($account->user_id, $meetingDocId, $umdmType);

            $guarantors = $this->findGuarantorsByLoanId($loanId);

            foreach ($guarantors as $guarantor) {
                $this->createUserMeetingDocMapping($guarantor->user_id, $meetingDocId, Enum::UMDM_TYPE_GUACC);
            }

            DB::commit();
            return $loan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('เกิดข้อผิดพลาดในการอนุมัติเงินกู้: ' . $e->getMessage());
        }
    }


    /**
     * สร้าง userMeetingDocMapping
     */
    private function createUserMeetingDocMapping(int $userId, int $meetingDocId, string $status): void
    {
        try {
            $userMeetingDocMappingService = app(UserMeetingDocMappingService::class);
            $userMeetingDocMappingService->createMapping([
                'user_id' => $userId,
                'meeting_doc_id' => $meetingDocId,
                'umdm_created_by' => Auth::id(),
                'umdm_created_date' => now()
            ], $status);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create user MeetingDoc mapping: ' . $e->getMessage());
        }
    }

    /**
     * ค้นหาผู้ค้ำประกันตาม loan_id
     *
     * @param int $loanId รหัสเงินกู้
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findGuarantorsByLoanId(int $loanId)
    {
        return GuarantorLoan::with(['user', 'account'])
            ->where('loan_id', $loanId)
            ->get();
    }

    /**
     * อนุมัติเงินกู้หลายรายการ
     *
     * @param array $loanIds รหัสเงินกู้ที่ต้องการอนุมัติ
     * @param int $meetingDocId รหัสเอกสารการประชุม
     * @return array เงินกู้ที่ได้รับการอนุมัติ
     * @throws \Exception ถ้าเกิดข้อผิดพลาดในการอนุมัติเงินกู้
     */
    public function approveLoanSet(array $loanIds, int $meetingDocId): array
    {
        DB::beginTransaction();
        try {
            $approvedLoans = [];

            foreach ($loanIds as $loanId) {
                $loan = $this->findLoanById($loanId);

                if ($loan->loan_status !== Enum::LOAN_STATUS_W) {
                    throw new \Exception("เงินกู้รหัส {$loanId} ไม่อยู่ในสถานะรอการอนุมัติ");
                }

                $loan->loan_status = Enum::LOAN_STATUS_A;
                $loan->loan_approved_by = Auth::id() ?? 0;
                $loan->loan_approved_date = now();
                $loan->save();

                $umdmType = ($loan->loan_type == Enum::LOAN_TYPE_N) ? Enum::UMDM_TYPE_LNACC : Enum::UMDM_TYPE_LNEACC;

                $accountService = app(AccountService::class);
                $account = $accountService->findAccountById($loan->account_id);

                if (!$account) {
                    throw new \Exception("ไม่พบข้อมูลบัญชีที่เกี่ยวข้องกับเงินกู้รหัส {$loanId}");
                }

                $account->account_status = Enum::ACCOUNT_STATUS_L;
                $account->account_updated_by = Auth::id() ?? 0;
                $account->account_updated_date = now();
                $account->save();

                $this->createUserMeetingDocMapping($account->user_id, $meetingDocId, $umdmType);

                $guarantors = $this->findGuarantorsByLoanId($loanId);

                foreach ($guarantors as $guarantor) {
                    $this->createUserMeetingDocMapping($guarantor->user_id, $meetingDocId, Enum::UMDM_TYPE_GUACC);
                }

                $approvedLoans[] = $loan;
            }

            DB::commit();
            return $approvedLoans;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('เกิดข้อผิดพลาดในการอนุมัติเงินกู้: ' . $e->getMessage());
        }
    }

    /**
     * สร้างใบเสร็จรับเงินจาก installment_payment
     *
     * @param int $ipId รหัสการชำระเงินงวด
     * @return array ข้อมูลใบเสร็จรับเงิน
     * @throws \Exception ถ้าไม่พบข้อมูลการชำระเงินงวด
     */
    public function generateReceipt(int $ipId): array
    {
        $installmentPayment = $this->findInstallmentById($ipId);

        if (!$installmentPayment) {
            throw new \Exception('ไม่พบข้อมูลการชำระเงินงวด');
        }

        $loan = $installmentPayment->loan;
        $account = $loan->account;
        $user = $account->user;

        $receipt = [
//            'receipt_number' => 'REC-' . str_pad($ipId, 6, '0', STR_PAD_LEFT),
            'date' => $installmentPayment->ip_date,
            'customer_name' => $user->user_name,
            'account_number' => $account->account_no,
            'loan_number' => $loan->loan_no,
            'payment_amount' => $installmentPayment->ip_amount,
            'interest_amount' => $installmentPayment->ip_interest,
            'total_amount' => $installmentPayment->ip_amount + $installmentPayment->ip_interest,
            'payment_period' => $installmentPayment->ip_period_no . '/' . $loan->loan_period,
            'payment_method' => $installmentPayment->ip_pay_type,
            'created_by' => $installmentPayment->createdBy->user_name,
        ];

        return $receipt;
    }

    /**
     * บันทึกข้อมูลการอนุมัติเงินกู้จาก dropdown 3 ระดับ
     *
     * @param int $loanId รหัสเงินกู้
     * @param array $approverData ข้อมูลผู้อนุมัติทั้ง 3 ระดับ
     * @return Loan
     * @throws \Exception
     */
    public function approveLoanMultiLevel(int $loanId, array $approverData): Loan
    {
        DB::beginTransaction();
        try {
            $loan = Loan::findOrFail($loanId);

            if (isset($approverData['approve1'])) {
                $loan->loan_approve1 = $approverData['approve1'];
            }
            if (isset($approverData['approve2'])) {
                $loan->loan_approve2 = $approverData['approve2'];
            }
            if (isset($approverData['approve3'])) {
                $loan->loan_approve3 = $approverData['approve3'];
            }

            $loan->loan_updated_by = auth()->id();
            $loan->loan_updated_date = now();
            $loan->save();

            DB::commit();
            return $loan;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


}
