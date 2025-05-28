<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\AccountService;
use App\Services\AddressService;
use App\Services\PositionService;
use App\Services\RuleService;
use App\Services\StatusService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class AccountController extends Controller
{

    protected $accountService;
    protected $userService;
    protected $ruleService;
    protected $positionService;
    protected $addressService;
    protected $statusService;

    public function __construct(
        AccountService  $accountService,
        UserService     $userService,
        RuleService     $ruleService,
        PositionService $positionService,
        AddressService  $addressService,
        StatusService   $statusService
    )
    {
        $this->accountService = $accountService;
        $this->userService = $userService;
        $this->ruleService = $ruleService;
        $this->positionService = $positionService;
        $this->addressService = $addressService;
        $this->statusService = $statusService;
    }

//    public function index()
//    {
//        $accounts = Account::with(['user'])
//            ->orderBy('account_id', 'desc')
//            ->paginate(10);
//        return view('superAdmin.account.index', compact('accounts'));
//    }

    public function index()
    {
        $accounts = $this->accountService->getAllAccounts();
        return view('superAdmin.account.index', compact('accounts'));
    }

//    public function create()
//    {
//        $users = User::whereIn('user_status', [
//            Enum::USER_STATUS_A,
//            Enum::USER_STATUS_P
//        ])->get();
//        $statuses = Enum::getAccountStatusDropdown();
//        $flagOpen = false;
//
//        $user = User::findOrFail(auth()->id());
//        // สร้าง account_no อัตโนมัติ
//        $year = substr(date('Y') + 543, -2); // ปี พ.ศ. 2 หลักท้าย
//        $userNumber = str_pad($user->user_number, 4, '0', STR_PAD_LEFT);
//        // นับจำนวน account ของ user + 1
//        $accountCount = Account::where('user_id', auth()->id())
//                ->whereNotIn('account_status', [
//                    Enum::ACCOUNT_STATUS_I,  // ใช้ปิดบัญชี
//                    Enum::ACCOUNT_STATUS_C,   // ไม่ผ่านการเห็นชอบ
//                ])
//                ->count() + 1;
//        $accountNumber = str_pad($accountCount, 2, '0', STR_PAD_LEFT);
//
//        $accountNo = $userNumber . $year . $accountNumber;
//
//        return view('superAdmin.account.create', compact('users', 'statuses', 'flagOpen', 'accountNo'));
//    }


    public function create()
    {
        $users = $this->userService->getUsersWithRelations();
        $statuses = Enum::getAccountStatusDropdown();
        $flagOpen = false;

        $user = $this->userService->findUserById(auth()->id());
        $accountNo = $this->accountService->generateAccountNo($user->user_id);

        return view('superAdmin.account.create', compact('users', 'statuses', 'flagOpen', 'accountNo'));
    }

    // public function openAccount($id)
    // {
    //     $users = User::findOrFail($id);
    //     $statuses = Enum::getAccountStatusDropdown();
    //     $flagOpen = true;

    //     // สร้าง account_no อัตโนมัติ
    //     $year = substr(date('Y') + 543, -2); // ปี พ.ศ. 2 หลักท้าย
    //     $userNumber = str_pad($users->user_number, 4, '0', STR_PAD_LEFT);
    //     // นับจำนวน account ของ user + 1
    //     $accountCount = Account::where('user_id', $id)
    //             ->whereNotIn('account_status', [
    //                 Enum::ACCOUNT_STATUS_I,  // ใช้ปิดบัญชี
    //                 Enum::ACCOUNT_STATUS_C,   // ไม่ผ่านการเห็นชอบ
    //             ])
    //             ->count() + 1;
    //     $accountNumber = str_pad($accountCount, 2, '0', STR_PAD_LEFT);

    //     $accountNo = $userNumber . $year . $accountNumber;

    //     return view('superAdmin.account.create', compact('users', 'statuses', 'flagOpen', 'accountNo'));
    // }

    public function openAccount($id)
    {
        $users = $this->userService->findUserById($id);
        $statuses = Enum::getAccountStatusDropdown();
        $flagOpen = true;
        $accountNo = $this->accountService->generateAccountNo($users->user_id);

        return view('superAdmin.account.create', compact('users', 'statuses', 'flagOpen', 'accountNo'));
    }

//    public function store(Request $request)
//    {
//        $request->validate([
//            'user_id' => 'required|exists:user,user_id',
//            'account_name' => 'required|max:500',
//            'account_no' => 'required|max:10|unique:account,account_no',
//            'account_book_no' => 'required|max:2',
//            'account_status' => ['required', RuleValidation::in(Enum::getAccountStatusesForValidation())],
//            'account_consider_remark' => 'nullable|max:800',
//            'account_close_remark' => 'nullable|max:800',
//            'account_start_date' => 'nullable|date',
//            'account_final_unit' => 'nullable|integer|min:0',
//            'account_balance' => 'nullable|numeric|min:0'
//        ]);
//
//        $account = new Account($request->all());
//        $account->account_created_by = auth()->id();
//        $account->account_updated_by = auth()->id();
//        $account->save();
//
//        return redirect()->route('account.index')->with('success', 'เพิ่มข้อมูลบัญชีสำเร็จ');
//    }

//    public function store(Request $request)
//    {
//        $request->validate([
//            'user_id' => 'required|exists:user,user_id',
//            'account_no' => 'required|max:10|unique:account,account_no',
//        ]);
//
//        $user = User::find($request->user_id);
//
//        $account = new Account();
//        $account->user_id = $request->user_id;
//        $account->account_no = $request->account_no;
//        $account->account_name = $user->user_fname . ' ' . $user->user_lname;
//        $account->account_book_no = 1;
//        $account->account_status = Enum::ACCOUNT_STATUS_W; // รอพิจารณา
//        $account->account_created_by = auth()->id();
//        $account->account_updated_by = auth()->id();
//        $account->save();
//
//        return redirect()->route('account.index')->with('success', 'เพิ่มข้อมูลบัญชีสำเร็จ');
//    }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'user_id' => 'required|exists:user,user_id',
//             'account_no' => 'required|max:10|unique:account,account_no',
//             'unit_num' => 'required|integer|min:1|max:' . Enum::UNIT_MAX,
//         ]);

//         $user = User::find($request->user_id);

//         DB::beginTransaction();
//         try {
//             // สร้างบัญชี
//             $account = new Account();
//             $account->user_id = $request->user_id;
//             $account->account_no = $request->account_no;
//             $account->account_name = $user->user_fname . ' ' . $user->user_lname;
//             $account->account_book_no = 1;
//             $account->account_status = Enum::ACCOUNT_STATUS_W;
//             $account->account_final_unit = $request->unit_num;
// //            $account->account_balance = $request->unit_num * Enum::UNIT_PRICE;
//             $account->account_created_by = auth()->id();
//             $account->account_updated_by = auth()->id();
//             $account->save();

//             // สร้างรายการหุ้น
//             $unitTran = new UnitTran();
//             $unitTran->account_id = $account->account_id;
//             $unitTran->unit_tran_name = Enum::UNIT_DEFAULT_NAME;
//             $unitTran->unit_tran_num = $request->unit_num;
//             $unitTran->unit_tran_amt = Enum::UNIT_PRICE;
//             $unitTran->unit_tran_created_by = auth()->id();
//             $unitTran->unit_tran_created_date = now();
//             $unitTran->save();

//             DB::commit();
//             return redirect()->route('account.index')->with('success', 'เพิ่มข้อมูลบัญชีสำเร็จ');
//         } catch (\Exception $e) {
//             DB::rollback();
//             return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
//         }
//     }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'account_no' => 'required|max:10|unique:account,account_no',
            'unit_num' => 'required|integer|min:1|max:' . Enum::UNIT_MAX,
        ]);

        try {
            $account = $this->accountService->createAccount($request->all());
            return redirect()->route('account.index')->with('success', 'เพิ่มข้อมูลบัญชีสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
        }
    }

    // public function edit($id)
    // {
    //     $account = Account::findOrFail($id);
    //     $users = User::whereIn('user_status', [
    //         Enum::USER_STATUS_A,
    //         Enum::USER_STATUS_P
    //     ])->get();
    //     $statuses = Enum::getAccountStatusDropdown();
    //     return view('superAdmin.account.edit',
    //         compact('account', 'users', 'statuses'));
    // }

    public function edit($id)
    {
        $account = $this->accountService->findAccountById($id);
        $users = $this->userService->getUsersWithRelations();
        $statuses = Enum::getAccountStatusDropdown();

        return view('superAdmin.account.edit', compact('account', 'users', 'statuses'));
    }


    // public function show($id)
    // {
    //     $account = Account::with(['user', 'unitTrans'])->findOrFail($id);
    //     return view('superAdmin.account.show', compact('account'));
    // }

    public function show($id)
    {
        $account = $this->accountService->findAccountWithDetails($id);
        return view('superAdmin.account.show', compact('account'));
    }



    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:user,user_id',
    //         'account_name' => 'required|max:500',
    //         'account_no' => 'required|max:10|unique:account,account_no,' . $id . ',account_id',
    //         'account_book_no' => 'required|max:2',
    //         'account_status' => ['required', RuleValidation::in(Enum::getAccountStatusesForValidation())],
    //         'account_consider_remark' => 'nullable|max:800',
    //         'account_close_remark' => 'nullable|max:800',
    //         'account_start_date' => 'nullable|date',
    //         'account_final_unit' => 'nullable|integer|min:0',
    //         'account_balance' => 'nullable|numeric|min:0'
    //     ]);

    //     $account = Account::findOrFail($id);

    //     // ถ้าสถานะเปลี่ยนเป็นปิดบัญชี
    //     if ($request->account_status == Enum::ACCOUNT_STATUS_I && $account->account_status != Enum::ACCOUNT_STATUS_I) {
    //         $request->merge([
    //             'account_close_date' => now(),
    //             'account_close_remark' => $request->account_close_remark
    //         ]);
    //     }

    //     // ถ้าสถานะเปลี่ยนเป็นใช้งาน
    //     if ($request->account_status == Enum::ACCOUNT_STATUS_A && $account->account_status != Enum::ACCOUNT_STATUS_A) {
    //         $request->merge([
    //             'account_consider_by' => auth()->id(),
    //             'account_consider_date' => now(),
    //             'account_start_date' => $request->account_start_date ?? now()
    //         ]);
    //     }

    //     $account->fill($request->all());
    //     $account->account_updated_by = auth()->id();
    //     $account->save();

    //     return redirect()->route('account.index')->with('success', 'อัพเดตข้อมูลบัญชีสำเร็จ');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'account_name' => 'required|max:500',
            'account_no' => 'required|max:10|unique:account,account_no,' . $id . ',account_id',
            'account_book_no' => 'required|max:2',
            'account_status' => ['required', RuleValidation::in(Enum::getAccountStatusesForValidation())],
            'account_consider_remark' => 'nullable|max:800',
            'account_close_remark' => 'nullable|max:800',
            'account_start_date' => 'nullable|date',
            'account_final_unit' => 'nullable|integer|min:0',
            'account_balance' => 'nullable|numeric|min:0'
        ]);

        try {
            $account = $this->accountService->findAccountById($id);
            $this->accountService->updateAccount($account, $request->all());
            return redirect()->route('account.index')->with('success', 'อัพเดตข้อมูลบัญชีสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update account']);
        }
    }



    // public function searchForDeposit(Request $request)
    // {

    //     $search = $request->input('query');

    //     if (!mb_check_encoding($search, 'UTF-8')) {
    //         $search = mb_convert_encoding($search, 'UTF-8', 'UTF-8');
    //     }

    //     $accounts = Account::query()
    //         ->when($search, function ($query, $search) {
    //             $query->where('account_no', 'LIKE', "%{$search}%")
    //                 ->orWhere('account_name', 'LIKE', "%{$search}%")
    //                 ->orWhereHas('user', function ($q) use ($search) {
    //                     $q->where('user_id_no', 'LIKE', "%{$search}%");
    //                 });
    //         })
    //         ->with('user')
    //         ->get();

    //     return response()->json($accounts, 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    // }

    public function searchForDeposit(Request $request)
    {
        $accounts = $this->accountService->searchAccounts($request->input('query'));
        return response()->json($accounts, 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    }


    // public function closeAccount($id)
    // {
    //     $account = Account::findOrFail($id);

    //     if ($account->account_status == Enum::ACCOUNT_STATUS_I) {
    //         return redirect()->route('account.index')->with('error', 'บัญชีนี้ถูกปิดอยู่แล้ว');
    //     }

    //     $account->update([
    //         'account_status' => Enum::ACCOUNT_STATUS_I,
    //         'account_close_date' => now(),
    //         'account_close_remark' => 'บัญชีถูกปิดโดย ' . auth()->user()->name,
    //         'account_updated_by' => auth()->id(),
    //     ]);

    //     return redirect()->route('account.index')->with('success', 'บัญชีถูกปิดสำเร็จ');
    // }

    public function preCloseAccount($id)
    {
        try {
            $account = $this->accountService->findAccountById($id);
            $this->accountService->preCloseAccount($account);
            return redirect()->route('account.index')->with('success', 'บัญชีถูกปิดสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->route('account.index')->with('error', $e->getMessage());
        }
    }

}
