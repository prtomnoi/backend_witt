<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\Account;
use App\Models\Announce;
use App\Services\AccountService;
use App\Services\DepositService;
use App\Services\MeetingDocService;
use Illuminate\Http\Request;

class AccountApprovalController extends Controller
{
    protected $depositService;
    protected $accountService;
    protected $meetingDocService;

    public function __construct(
        DepositService $depositService,
        AccountService $accountService,
        MeetingDocService $meetingDocService
    )
    {
        $this->depositService = $depositService;
        $this->accountService = $accountService;
        $this->meetingDocService = $meetingDocService;
    }

     public function index()
     {

         $pendingAccounts = $this->accountService->getPendingAccounts();
//         dd($pendingAccounts);

         $meetingDocs = $this->meetingDocService->getActiveMeetingDocs();

         return view('superAdmin.approval.index', compact('pendingAccounts', 'meetingDocs'));
     }

    /**
     * แสดงหน้ารายการบัญชีที่รออนุมัติ
     */
//    public function index()
//    {
//        dd(555);
//        $pendingAccounts = $this->accountService->getPendingAccounts();
//        $announces = $this->accountService->getActiveAnnounces();
//
//        return view('superAdmin.approval.index',
//            compact('pendingAccounts', 'announces'));
//    }

    public function create()
    {


    }

    public function store(Request $request)
    {


    }

    public function edit($id)
    {

    }

    public function show($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    // public function process(Request $request)
    // {
    //     $request->validate([
    //         'account_ids' => 'required|array',
    //         'account_ids.*' => 'exists:account,account_id',
    //         'action' => 'required|in:' . Enum::APPROVE_ACTION . ',' . Enum::REJECT_ACTION,
    //         'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
    //         'account_consider_remark' => 'nullable|string|max:800'
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $accounts = Account::whereIn('account_id', $request->account_ids)->get();

    //         foreach ($accounts as $account) {
    //             if ($request->action === Enum::APPROVE_ACTION) {
    //                 $this->approveAccount($account);
    //             } else {
    //                 $this->rejectAccount($account, $request->account_consider_remark);
    //             }

    //             // Create user announce mapping
    //             $this->createUserMeetingDocMapping($account->user_id, $request->meeting_doc_id);
    //         }

    //         DB::commit();
    //         return redirect()->route('approval.index')
    //             ->with('success', 'ดำเนินการอนุมัติเรียบร้อยแล้ว');

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
    //     }
    // }

    /**
     * ประมวลผลการอนุมัติบัญชี
     */
    public function process(Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:account,account_id',
            'action' => 'required|in:' . Enum::APPROVE_ACTION . ',' . Enum::REJECT_ACTION,
            'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
            'account_consider_remark' => 'nullable|string|max:800'
        ]);

        try {
            $this->accountService->processApproval($request->all());
            return redirect()->route('approval.index')
                ->with('success', 'ดำเนินการอนุมัติเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
        }
    }

    public function processClose(Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:account,account_id',
            'action' => 'required|in:' . Enum::APPROVE_ACTION . ',' . Enum::REJECT_ACTION,
            'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
            'account_consider_remark' => 'nullable|string|max:800'
        ]);

        try {
            $this->accountService->processCloseApproval($request->all());
            return redirect()->route('approval.index')
                ->with('success', 'ดำเนินการอนุมัติเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
        }
    }


    public function processLoan(Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:account,account_id',
            'action' => 'required|in:' . Enum::APPROVE_ACTION . ',' . Enum::REJECT_ACTION,
            'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
            'account_consider_remark' => 'nullable|string|max:800'
        ]);

        try {
            $this->accountService->processCloseApproval($request->all());
            return redirect()->route('approval.index')
                ->with('success', 'ดำเนินการอนุมัติเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage()]);
        }
    }

    // private function approveAccount(Account $account)
    // {
    //     // Update account status
    //     $account->account_status = Enum::ACCOUNT_STATUS_A;
    //     $account->account_consider_by = auth()->id();
    //     $account->account_consider_date = now();
    //     $account->account_start_date = now();
    //     $account->save();

    //     // Update user status if pending
    //     if ($account->user->user_status === Enum::USER_STATUS_P) {
    //         $account->user->user_status = Enum::USER_STATUS_A;
    //         $account->user->save();
    //     }

    //     // Create deposit plan for each unit transaction
    //     foreach ($account->unitTrans as $unitTran) {
    //         $this->depositService->createDepositPlan($unitTran);
    //     }
    // }

    /**
     * อนุมัติบัญชี
     */
    public function approveAccount(Account $account)
    {
        try {
            $this->accountService->approveAccount($account);

            // สร้างแผนการฝากเงินสำหรับแต่ละรายการ unit
            foreach ($account->unitTrans as $unitTran) {
                $this->depositService->createDepositPlan($unitTran);
            }
        } catch (\Exception $e) {
            throw new \Exception('เกิดข้อผิดพลาดในการอนุมัติบัญชี: ' . $e->getMessage());
        }
    }

    // private function rejectAccount(Account $account, $remark)
    // {
    //     $account->account_status = Enum::ACCOUNT_STATUS_C;
    //     $account->account_consider_by = auth()->id();
    //     $account->account_consider_date = now();
    //     $account->account_consider_remark = $remark;
    //     $account->save();
    // }

    /**
     * ปฏิเสธบัญชี
     */
    public function rejectAccount(Account $account, string $remark)
    {
        try {
            $this->accountService->rejectAccount($account, $remark);
        } catch (\Exception $e) {
            throw new \Exception('เกิดข้อผิดพลาดในการปฏิเสธบัญชี: ' . $e->getMessage());
        }
    }

    // private function createUserMeetingDocMapping($userId, $meetingDocId)
    // {
    //     userMeetingDocMapping::create([
    //         'user_id' => $userId,
    //         'meeting_doc_id' => $meetingDocId,
    //         'umdm_created_by' => auth()->id(),
    //         'umdm_created_date' => now()
    //     ]);
    // }

    /**
     * สร้าง userMeetingDocMapping
     */
//    public function createUserMeetingDocMapping(int $userId, int $meetingDocId)
//    {
//        try {
//            $this->userAnnounceMappingService->createMapping([
//                'user_id' => $userId,
//                'meeting_doc_id' => $meetingDocId,
//                'umdm_created_by' => auth()->id(),
//                'umdm_created_date' => now()
//            ]);
//        } catch (\Exception $e) {
//            throw new \Exception('เกิดข้อผิดพลาดในการสร้างการประกาศ: ' . $e->getMessage());
//        }
//    }


    // public function closeAccount()
    // {
    //     $pendingAccounts = Account::with(['user', 'unitTrans'])
    //         ->where('account_status', Enum::ACCOUNT_STATUS_I)
    //         ->orderBy('account_id', 'desc')
    //         ->get();

    //     $announces = Announce::with('meetingDoc')
    //         ->where('announce_status', Enum::DEFAULT_STATUS_A)
    //         ->get();

    //     return view('superAdmin.approval.close', compact('pendingAccounts', 'announces'));
    // }

    /**
     * แสดงหน้ารายการบัญชีที่รอปิด
     */
    public function closeAccount()
    {
//        dd(6666);
        $pendingAccounts = $this->accountService->getWaitClosingAccounts();
        $meetingDocs = $this->meetingDocService->getActiveMeetingDocs();

        return view('superAdmin.approval.close',
            compact('pendingAccounts', 'meetingDocs'));
    }

    public function approvalLoan()
    {
        $pendingAccounts = $this->accountService->getWaitClosingAccounts();
        $meetingDocs = $this->meetingDocService->getActiveMeetingDocs();

        return view('superAdmin.approval.loan',
            compact('pendingAccounts', 'meetingDocs'));
    }

}
