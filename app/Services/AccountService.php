<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\Account;
use App\Models\Announce;
use App\Models\UnitTran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountService
{

    /**
     * ดึงข้อมูลบัญชีทั้งหมด ยกเว้นบัญชีพิเศษ
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllAccounts($perPage = 10)
    {
        return Account::with(['user'])
            ->whereNotIn('account_no', Enum::MAIN_ACCOUNT)
            ->orderBy('account_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลบัญชีพิเศษ
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllMainAccounts($perPage = 10)
    {
        return Account::with(['user'])
            ->whereIn('account_no', Enum::MAIN_ACCOUNT)
            ->orderBy('account_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลบัญชีผู้ค้ำทั้งหมด ยกเว้นบัญชีพิเศษ และรวมบัญชีที่มีสถานะ 'G' พร้อมข้อมูลเงินกู้ที่มีสถานะ 'A'
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAccountsGuarantorWithActiveLoans($perPage = 10)
    {
        return Account::with(['user', 'loans' => function($query) {
            $query->where('loan_status', Enum::LOAN_STATUS_A);
        }])
            ->whereNotIn('account_no', Enum::MAIN_ACCOUNT)
            ->where('account_status', Enum::ACCOUNT_STATUS_G)
            ->orderBy('account_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลบัญชีผู้กู้ทั้งหมด ยกเว้นบัญชีพิเศษ และรวมบัญชีที่มีสถานะ 'L' พร้อมข้อมูลเงินกู้ที่มีสถานะ 'A'
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAccountsWithActiveLoans($perPage = 10)
    {
        return Account::with(['user', 'loans' => function($query) {
            $query->where('loan_status', Enum::LOAN_STATUS_A);
        }])
            ->whereNotIn('account_no', Enum::MAIN_ACCOUNT)
            ->where('account_status', Enum::ACCOUNT_STATUS_L)
            ->orderBy('account_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหาบัญชีตาม ID
     *
     * @param int $id
     * @return Account
     */
    public function findAccountById(int $id): Account
    {
        return Account::findOrFail($id);
    }
    /**
     * ค้นหาบัญชี
     *
     * @param string $account_no
     * @return Account
     */
    public function findAccountByAccountNo(string $account_no): Account
    {
        return Account::where('account_no', $account_no)->first();
    }

    /**
     * ค้นหาบัญชีพร้อมข้อมูลที่เกี่ยวข้องตาม ID
     *
     * @param int $id
     * @return Account
     */
    public function findAccountWithDetails(int $id): Account
    {
        return Account::with([
            'user',
            'unitTrans',
            'deposits',
            'withdrawals'
        ])->findOrFail($id);
    }

    /**
     * ดึงข้อมูลบัญชีที่มีสถานะ 'A' ยกเว้นบัญชีพิเศษ
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAccounts()
    {

        return Account::where('account_status', Enum::ACCOUNT_STATUS_A)
            ->whereNotIn('account_no', Enum::MAIN_ACCOUNT)
            ->get();
    }

    /**
     * ค้นหาบัญชีตามคำค้น
     */
    public function searchAccounts(?string $search): Collection
    {
        if (!empty($search) && !mb_check_encoding($search, 'UTF-8')) {
            $search = mb_convert_encoding($search, 'UTF-8', 'UTF-8');
        }

        return Account::query()
            ->when($search, function ($query, $search) {
                $query->where('account_no', 'LIKE', "%{$search}%")
                    ->orWhere('account_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('user_id_no', 'LIKE', "%{$search}%");
                    });
            })
            ->with('user')
            ->get();
    }

    /**
     * ค้นหาบัญชีที่มีสถานะใช้งานได้
     *
     * @param string|null $search
     * @return Collection
     */
    public function searchInActiveAccounts(?string $search): Collection
    {
        return Account::query()
            ->whereNotIn('account_status', [
                Enum::ACCOUNT_STATUS_I,  // Inactive
                Enum::ACCOUNT_STATUS_C   // Closed
            ])
            ->when($search, function ($query, $search) {
                $query->where('account_no', 'LIKE', "%{$search}%")
                    ->orWhere('account_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('user_id_no', 'LIKE', "%{$search}%");
                    });
            })
            ->with('user')
            ->get();
    }

    /**
     * ค้นหาบัญชีพร้อมข้อมูลการทำธุรกรรมล่าสุด
     *
     * @param int $id
     * @return Account
     */
    public function findAccountWithLatestTransactions(int $id): Account
    {
        return Account::with([
            'user',
            'unitTrans' => function ($query) {
                $query->latest('unit_tran_created_date')->take(5);
            },
            'deposits' => function ($query) {
                $query->latest('deposit_created_date')->take(5);
            },
            'withdrawals' => function ($query) {
                $query->latest('withdrawal_created_date')->take(5);
            }
        ])->findOrFail($id);
    }

    // สร้างเลขบัญชีอัตโนมัติ
    // public function generateAccountNo($userId)
    // {
    //     $user = $this->userService->findUserById($userId);
    //     $year = substr(date('Y') + 543, -2);
    //     $userNumber = str_pad($user->user_number, 4, '0', STR_PAD_LEFT);

    //     $accountCount = Account::where('user_id', $userId)
    //             ->whereNotIn('account_status', [
    //                 Enum::ACCOUNT_STATUS_I,
    //                 Enum::ACCOUNT_STATUS_C,
    //             ])
    //             ->count() + 1;

    //     $accountNumber = str_pad($accountCount, 2, '0', STR_PAD_LEFT);

    //     return $userNumber . $year . $accountNumber;
    // }

    /**
     * สร้างเลขบัญชีอัตโนมัติ
     */
    public function generateAccountNo(int $userId): string
    {
        $userService = app(UserService::class);
        $user = $userService->findUserById($userId);

        $year = substr(date('Y') + 543, -2);
        $userNumber = str_pad($user->user_number, 4, '0', STR_PAD_LEFT);

        $accountCount = Account::where('user_id', $userId)
                ->whereNotIn('account_status', [
                    Enum::ACCOUNT_STATUS_I,
                    Enum::ACCOUNT_STATUS_C,
                ])
                ->count() + 1;

        $accountNumber = str_pad($accountCount, 2, '0', STR_PAD_LEFT);

        return $userNumber . $year . $accountNumber;
    }

    // สร้างบัญชีใหม่
    // public function createAccount($data)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $user = $this->userService->findUserById($data['user_id']);

    //         $account = new Account();
    //         $account->user_id = $data['user_id'];
    //         $account->account_no = $data['account_no'];
    //         $account->account_name = $user->user_fname . ' ' . $user->user_lname;
    //         $account->account_book_no = 1;
    //         $account->account_status = Enum::ACCOUNT_STATUS_W;
    //         $account->account_final_unit = $data['unit_num'];
    //         $account->account_created_by = auth()->id();
    //         $account->account_updated_by = auth()->id();
    //         $account->save();

    //         $this->createUnitTransaction($account, $data['unit_num']);

    //         DB::commit();
    //         return $account;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }

    /**
     * สร้างบัญชีใหม่
     */
    public function createAccount(array $data): Account
    {
        DB::beginTransaction();
        try {
            $userService = app(UserService::class);
            $user = $userService->findUserById($data['user_id']);

            $account = new Account();
            $account->user_id = $data['user_id'];
            $account->account_no = $data['account_no'];
            $account->account_name = $user->user_fname . ' ' . $user->user_lname;
            $account->account_book_no = 1;
            $account->account_status = Enum::ACCOUNT_STATUS_W;
            $account->account_final_unit = $data['unit_num'];
            $account->account_created_by = Auth::id();
            $account->account_updated_by = Auth::id();
            $account->save();

            $this->createUnitTransaction($account, $data['unit_num']);

            DB::commit();
            return $account;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // สร้างรายการ Unit Transaction
    private function createUnitTransaction(Account $account, int $unitNum): UnitTran
    {
        return UnitTran::create([
            'account_id' => $account->account_id,
            'unit_tran_name' => Enum::UNIT_DEFAULT_NAME,
            'unit_tran_num' => $unitNum,
            'unit_tran_amt' => Enum::UNIT_PRICE,
            'unit_tran_created_by' => Auth::id(),
            'unit_tran_created_date' => now()
        ]);
    }

    // อัพเดตข้อมูลบัญชี
    // public function updateAccount($account, $data)
    // {
    //     if ($data['account_status'] == Enum::ACCOUNT_STATUS_I &&
    //         $account->account_status != Enum::ACCOUNT_STATUS_I) {
    //         $data['account_close_date'] = now();
    //     }

    //     if ($data['account_status'] == Enum::ACCOUNT_STATUS_A &&
    //         $account->account_status != Enum::ACCOUNT_STATUS_A) {
    //         $data['account_consider_by'] = auth()->id();
    //         $data['account_consider_date'] = now();
    //         $data['account_start_date'] = $data['account_start_date'] ?? now();
    //     }

    //     $account->fill($data);
    //     $account->account_updated_by = auth()->id();
    //     $account->save();

    //     return $account;
    // }

    /**
     * อัพเดตข้อมูลบัญชี
     */
    public function updateAccount(Account $account, array $data): Account
    {
        DB::beginTransaction();
        try {
            if ($data['account_status'] == Enum::ACCOUNT_STATUS_I &&
                $account->account_status != Enum::ACCOUNT_STATUS_I) {
                $data['account_close_date'] = now();
            }

            if ($data['account_status'] == Enum::ACCOUNT_STATUS_A &&
                $account->account_status != Enum::ACCOUNT_STATUS_A) {
                $data['account_consider_by'] = Auth::id();
                $data['account_consider_date'] = now();
                $data['account_start_date'] = $data['account_start_date'] ?? now();

                // สร้างแผนการฝากเงิน
                // $depositService = app(DepositService::class);
                // foreach ($account->unitTrans as $unitTran) {
                //     $depositService->createDepositPlan($unitTran);
                // }
            }

            $account->fill($data);
            $account->account_updated_by = Auth::id();
            $account->save();

            DB::commit();
            return $account;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // ปิดบัญชี
    // public function closeAccount($account)
    // {
    //     if ($account->account_status == Enum::ACCOUNT_STATUS_I) {
    //         throw new \Exception('บัญชีนี้ถูกปิดอยู่แล้ว');
    //     }

    //     $account->update([
    //         'account_status' => Enum::ACCOUNT_STATUS_I,
    //         'account_close_date' => now(),
    //         'account_close_remark' => 'บัญชีถูกปิดโดย ' . auth()->user()->name,
    //         'account_updated_by' => auth()->id(),
    //     ]);

    //     return $account;
    // }

    /**
     * ขอปิดบัญชี
     */
    public function preCloseAccount(Account $account): Account
    {
        DB::beginTransaction();
        try {
            if ($account->account_status == Enum::ACCOUNT_STATUS_I) {
                throw new \Exception('บัญชีนี้ถูกปิดอยู่แล้ว');
            }

            if ($account->account_status == Enum::ACCOUNT_STATUS_WI) {
                throw new \Exception('บัญชีนี้ถูกขอยื่นปิดอยู่แล้ว');
            }

            $account->update([
                'account_status' => Enum::ACCOUNT_STATUS_WI,
                'account_close_date' => now(),
                'account_close_remark' => 'บัญชีถูกยื่นเรื่องขอปิดโดย ' . Auth::user()->name,
                'account_updated_by' => Auth::id(),
            ]);

            // ส่งการแจ้งเตือน
//            app(NotificationService::class)->sendAccountClosureNotification($account);

            DB::commit();
            return $account;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // ค้นหาบัญชีสำหรับการฝากเงิน
    public function searchForDeposit($search)
    {
        if (!mb_check_encoding($search, 'UTF-8')) {
            $search = mb_convert_encoding($search, 'UTF-8', 'UTF-8');
        }

        return Account::query()
            ->when($search, function ($query, $search) {
                $query->where('account_no', 'LIKE', "%{$search}%")
                    ->orWhere('account_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('user_id_no', 'LIKE', "%{$search}%");
                    });
            })
            ->with('user')
            ->get();
    }


    /**
     * ดึงข้อมูลบัญชีที่รออนุมัติ
     */
    public function getPendingAccounts(): Collection
    {
        return Account::with(['user', 'unitTrans'])
            ->where('account_status', Enum::ACCOUNT_STATUS_W)
            ->orderBy('account_id', 'desc')
            ->get();
    }

    /**
     * ดึงข้อมูลบัญชีที่รอปิด
     */
    public function getInActiveAnnounces(): Collection
    {
        return Account::with(['user', 'unitTrans'])
            ->where('account_status', Enum::ACCOUNT_STATUS_I)
            ->orderBy('account_id', 'desc')
            ->get();
    }

    public function getWaitClosingAccounts(): Collection
    {
        return Account::with(['user', 'unitTrans'])
            ->where('account_status', Enum::ACCOUNT_STATUS_WI)
            ->orderBy('account_id', 'desc')
            ->get();
    }

    /**
     * ดึงข้อมูลประกาศที่ใช้งานได้
     */
    public function getActiveAnnounces(): Collection
    {
        return Announce::with('meetingDoc')
            ->where('announce_status', Enum::DEFAULT_STATUS_A)
            ->get();
    }

    /**
     * ประมวลผลการอนุมัติบัญชี
     */
    public function processApproval(array $data): void
    {
        DB::beginTransaction();
        try {
            $accounts = Account::whereIn('account_id', $data['account_ids'])->get();

            foreach ($accounts as $account) {
                if ($data['action'] === Enum::APPROVE_ACTION) {
                    $this->approveAccount($account);
                } else {
                    $this->rejectAccount($account, $data['account_consider_remark']);
                }

                $this->createUserMeetingDocMapping($account->user_id, $data['meeting_doc_id'], Enum::UMDM_TYPE_OACC);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * อนุมัติบัญชี
     */
    public function approveAccount(Account $account): void
    {
        DB::beginTransaction();
        try {
            // อัพเดทสถานะบัญชี
            $account->account_status = Enum::ACCOUNT_STATUS_A;
            $account->account_consider_by = Auth::id();
            $account->account_consider_date = now();
            $account->account_start_date = now();
            $account->save();

            // อัพเดทสถานะผู้ใช้ถ้าอยู่ในสถานะ pending
            if ($account->user->user_status === Enum::USER_STATUS_P) {
                $account->user->user_status = Enum::USER_STATUS_A;
                $account->user->save();
            }

            // สร้างแผนการฝากเงินสำหรับแต่ละรายการ unit
            $depositService = app(DepositService::class);
//            dd($account, $account->unitTrans);
            foreach ($account->unitTrans as $unitTran) {
                $depositService->createDepositPlan($unitTran, 0);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ปฏิเสธบัญชี
     */
    public function rejectAccount(Account $account, string $remark): void
    {
        $account->account_status = Enum::ACCOUNT_STATUS_C;
        $account->account_consider_by = Auth::id();
        $account->account_consider_date = now();
        $account->account_consider_remark = $remark;
        $account->save();
    }

    /**
     * สร้าง userMeetingDocMapping
     */
    public function createUserMeetingDocMapping(int $userId, int $meetingDocId, string $status): void
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

    public function processCloseApproval(array $data): void
    {
        DB::beginTransaction();
        try {
            $accounts = Account::whereIn('account_id', $data['account_ids'])->get();
            $accountTransactionService = app(AccountTransactionService::class);

            foreach ($accounts as $account) {
                if ($data['action'] === Enum::APPROVE_ACTION) {
                    $accountTransactionService->createTransactionForCloseAccount($account);
                    $this->approveAccountClose($account);
                } else {
                    $this->rejectAccount($account, $data['account_consider_remark']);
                }

                $this->createUserMeetingDocMapping($account->user_id, $data['meeting_doc_id'], Enum::UMDM_TYPE_CACC);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function approveAccountClose(Account $account): void
    {
            // อัพเดทสถานะบัญชี
            $account->account_balance = 0;
            $account->account_status = Enum::ACCOUNT_STATUS_I;
            $account->account_consider_by = Auth::id();
            $account->account_consider_date = now();
            $account->account_start_date = now();
            $account->save();

    }

    /**
     * ดึงข้อมูลบัญชีที่มีสถานะ 'A' สำหรับสร้าง dropdown
     *
     * @return array
     */
    public function getGuarantorAccountsForDropdown()
    {

        $accounts = $this->getActiveAccounts();

        $dropdown = [];
        foreach ($accounts as $account) {
            $key = $account->account_id;
            $value = [
                'name' => $account->account_name . ' (วงเงินสะสม: ' . number_format($account->account_balance, 2) . ')',
                'balance' => $account->account_balance
            ];
            $dropdown[$key] = $value;
        }

        return $dropdown;
    }



    ///////////////////////////////////////////////////////////////////////////// วางโครงไว้ก่อน
    /**
     * ตรวจสอบความพร้อมในการกู้เงิน
     *
     * @param int $accountId รหัสบัญชีที่ต้องการตรวจสอบ
     * @param float $loanAmount จำนวนเงินที่ต้องการกู้
     * @return array ผลการตรวจสอบแต่ละรายการ
     * @throws \Exception เมื่อไม่พบบัญชีหรือเกิดข้อผิดพลาดอื่นๆ
     */
    public function checkLoanEligibility(int $accountId, float $loanAmount): array
    {
        try {
            $account = Account::findOrFail($accountId);
            $result = [];

            // 1. เช็ควงเงินกู้
            $maxLoanAmount = $this->calculateMaxLoanAmount($account);
            $result['loanAmountCheck'] = $loanAmount <= $maxLoanAmount;

            // 2. เช็คคนค้ำ
            $guarantors = $this->getGuarantors($accountId);
            $result['guarantorCheck'] = count($guarantors) >= $this->getRequiredGuarantorsCount($loanAmount);

            // 3. เช็คสัญญา
            $activeLoans = $this->getActiveLoans($accountId);
            $result['contractCheck'] = count($activeLoans) == 0;

            // 4. เช็คเครดิต balance
            $minRequiredBalance = $this->getMinRequiredBalance();
            $result['creditBalanceCheck'] = $account->account_balance >= $minRequiredBalance;

            return $result;
        } catch (\Exception $e) {
            throw new \Exception("เกิดข้อผิดพลาดในการตรวจสอบความพร้อมในการกู้เงิน: " . $e->getMessage());
        }
    }

    /**
     * คำนวณวงเงินกู้สูงสุดที่สามารถกู้ได้
     *
     * @param Account $account บัญชีที่ต้องการคำนวณ
     * @return float วงเงินกู้สูงสุด
     */
    private function calculateMaxLoanAmount(Account $account): float
    {
        // ตัวอย่างการคำนวณ (ต้องปรับตามกฎของระบบจริง)
        return $account->account_balance * 3;
    }

    /**
     * ดึงข้อมูลผู้ค้ำประกัน
     *
     * @param int $accountId รหัสบัญชี
     * @return array รายการผู้ค้ำประกัน
     */
    private function getGuarantors(int $accountId): array
    {
        // ดึงข้อมูลผู้ค้ำประกันจากฐานข้อมูล
        return []; // ต้องแทนที่ด้วยการดึงข้อมูลจริง
    }

    /**
     * คำนวณจำนวนผู้ค้ำประกันที่ต้องการตามจำนวนเงินกู้
     *
     * @param float $loanAmount จำนวนเงินกู้
     * @return int จำนวนผู้ค้ำประกันที่ต้องการ
     */
    private function getRequiredGuarantorsCount(float $loanAmount): int
    {
        // ตัวอย่างการคำนวณ (ต้องปรับตามกฎของระบบจริง)
        return ceil($loanAmount / 100000);
    }

    /**
     * ดึงข้อมูลสัญญาเงินกู้ที่ยังใช้งานอยู่
     *
     * @param int $accountId รหัสบัญชี
     * @return array รายการสัญญาเงินกู้ที่ยังใช้งานอยู่
     */
    private function getActiveLoans(int $accountId): array
    {
        // ดึงข้อมูลสัญญาเงินกู้ที่ยังใช้งานอยู่จากฐานข้อมูล
        return []; // ต้องแทนที่ด้วยการดึงข้อมูลจริง
    }

    /**
     * ดึงข้อมูลยอดเงินขั้นต่ำที่ต้องมีในบัญชี
     *
     * @return float ยอดเงินขั้นต่ำที่ต้องมีในบัญชี
     */
    private function getMinRequiredBalance(): float
    {
        // ดึงข้อมูลยอดเงินขั้นต่ำจากการตั้งค่าระบบ
        return 1000; // ต้องแทนที่ด้วยการดึงข้อมูลจริง
    }


}
