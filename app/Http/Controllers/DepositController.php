<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\Deposit;
use App\Models\UnitTran;
use App\Services\AccountService;
use App\Services\DepositService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    protected $depositService;
    protected $accountService;
    protected $userService;

    public function __construct(
        DepositService $depositService,
        AccountService $accountService,
        UserService    $userService
    )
    {
        $this->depositService = $depositService;
        $this->accountService = $accountService;
        $this->userService = $userService;
    }

    // public function index()
    // {
    //     $deposits = Deposit::with(['account', 'user'])
    //         ->orderBy('deposit_id', 'desc')
    //         ->paginate(10);
    //     return view('superAdmin.deposit.index', compact('deposits'));
    // }

    public function index()
    {
        $deposits = Deposit::whereNotNull('deposit_date')->paginate(15);
        return view('superAdmin.deposit.index', compact('deposits'));
    }

    // public function create()
    // {
    //     $accounts = Account::all();
    //     $users = User::all();
    //     $months = Enum::getMonthsDropdown();
    //     $unitTrans = UnitTran::all(); // เพิ่มข้อมูล UnitTran
    //     return view('superAdmin.deposit.create', compact('accounts', 'users', 'months', 'unitTrans'));
    // }

    public function create()
    {
        $accounts = $this->accountService->getAllAccounts();
        $users = $this->userService->getActiveUsers();
        $months = Enum::getMonthsDropdown();
        $unitTrans = UnitTran::all();
        return view('superAdmin.deposit.create', compact('accounts', 'users', 'months', 'unitTrans'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'account_id' => 'required|exists:account,account_id',
    //         'user_id' => 'required|exists:user,user_id',
    //         'deposit_year' => 'required|size:4',
    //         'deposit_month' => 'required',
    //         'deposit_date' => 'required|date',
    //         'deposit_unit' => 'required|integer|min:1',
    //         'deposit_amount' => 'required|numeric|min:0',
    //         'deposit_flag' => 'required|in:' . implode(',', Enum::getDepositFlagForValidation())
    //     ]);

    //     $deposit = new Deposit($request->all());
    //     $deposit->deposit_flag = $request->deposit_flag ?? Enum::DEPOSIT_FLAG_P;
    //     $deposit->deposit_created_by = auth()->id();
    //     $deposit->deposit_updated_by = auth()->id();
    //     $deposit->save();

    //     return redirect()->route('deposit.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    // }


    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:account,account_id',
            'user_id' => 'required|exists:user,user_id',
            'deposit_year' => 'required|size:4',
            'deposit_month' => 'required',
            'deposit_date' => 'required|date',
            'deposit_unit' => 'required|integer|min:1',
            'deposit_amount' => 'required|numeric|min:0',
            'deposit_flag' => 'required|in:' . implode(',', Enum::getDepositFlagForValidation())
        ]);

        try {
            $this->depositService->createDeposit($request->all());
            return redirect()->route('deposit.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create deposit']);
        }
    }


    // public function createFromUnitTran(Request $request, UnitTran $unitTran)
    // {
    //     $request->validate([
    //         'months' => ['required', 'integer', 'min:' . Enum::MIN_DEPOSIT_MONTHS, 'max:' . Enum::MAX_DEPOSIT_MONTHS],
    //     ]);

    //     $deposits = $this->depositService->createDepositPlan($unitTran, $request->months);

    //     return redirect()->route('deposit.index')
    //         ->with('success', 'สร้างแผนการฝากสำเร็จ จำนวน ' . count($deposits) . ' เดือน');
    // }


    public function createFromUnitTran(Request $request, UnitTran $unitTran)
    {
        $request->validate([
            'months' => ['required', 'integer', 'min:' . Enum::MIN_DEPOSIT_MONTHS, 'max:' . Enum::MAX_DEPOSIT_MONTHS],
        ]);

        try {
            $deposits = $this->depositService->createDepositPlan($unitTran, 0);
            return redirect()->route('deposit.index')
                ->with('success', 'สร้างแผนการฝากสำเร็จ จำนวน ' . $deposits->count() . ' เดือน');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create deposit plan']);
        }
    }

    public function processPayDepositCash($depositId)
    {
        try {
            // อัพเดทสถานะการชำระเงินเป็น "เสร็จสิ้น"
            // $deposit = Deposit::findOrFail($depositId);
            // $deposit->deposit_flag = 'Y'; // จ่ายแล้ว
            // $deposit->save();
            $deposit = $this->depositService->findDepositById($depositId);
            $payStatus = Enum::DEPOSIT_PAY_TYPE_CASH;

            $this->depositService->processPayment($deposit, $payStatus);

            return redirect()->back()->with('success', 'ยืนยันการชำระเงินด้วยเงินสดสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการยืนยันการชำระ']);
        }
    }


    // public function edit($id)
    // {
    //     $deposit = Deposit::findOrFail($id);
    //     $accounts = Account::all();
    //     $users = User::all();
    //     $months = Enum::getMonthsDropdown();
    //     return view('superAdmin.deposit.edit', compact('deposit', 'accounts', 'users', 'months'));
    // }

    public function edit($id)
    {
        $deposit = $this->depositService->findDepositById($id);
        $accounts = $this->accountService->getAllAccounts();
        $users = $this->userService->getActiveUsers();
        $months = Enum::getMonthsDropdown();
        return view('superAdmin.deposit.edit', compact('deposit', 'accounts', 'users', 'months'));
    }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'account_id' => 'required|exists:account,account_id',
    //         'user_id' => 'required|exists:user,user_id',
    //         'deposit_year' => 'required|size:4',
    //         'deposit_month' => 'required',
    //         'deposit_date' => 'required|date',
    //         'deposit_unit' => 'required|integer|min:1',
    //         'deposit_amount' => 'required|numeric|min:0'
    //     ]);

    //     $deposit = Deposit::findOrFail($id);
    //     $deposit->fill($request->all());
    //     $deposit->deposit_updated_by = auth()->id();
    //     $deposit->deposit_updated_date = now();
    //     $deposit->save();

    //     return redirect()->route('deposit.index')->with('success', 'อัพเดตข้อมูลสำเร็จ');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_id' => 'required|exists:account,account_id',
            'user_id' => 'required|exists:user,user_id',
            'deposit_year' => 'required|size:4',
            'deposit_month' => 'required',
            'deposit_date' => 'required|date',
            'deposit_unit' => 'required|integer|min:1',
            'deposit_amount' => 'required|numeric|min:0'
        ]);

        try {
            $deposit = $this->depositService->findDepositById($id);
            $this->depositService->updateDeposit($deposit, $request->all());
            return redirect()->route('deposit.index')->with('success', 'อัพเดตข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update deposit']);
        }
    }

//     public function payDeposits()
//     {
//         $currentMonth = date('m');

//         $currentYear = (int)date('Y') + 543;

// //        $deposits = Deposit::all();

// //        $deposits = Deposit::where('deposit_month_no', '<=', $currentMonth)->get();
//         $deposits = Deposit::where('deposit_year', '=', $currentYear)->get();
// //        dd($currentMonth, $deposits);

//         return view('superAdmin.save_money.index', compact('deposits'));
//     }

    public function payDeposits()
    {
        $currentYear = (int)date('Y') + 543;
        $deposits = $this->depositService->getDepositsByYearMonth($currentYear);
        return view('superAdmin.save_money.index', compact('deposits'));
    }


//     public function payDepositsId($id)
//     {
//
//         $currentMonth = date('m');
//         $currentYear = (int)date('Y') + 543;
//
//         $deposits = Deposit::where('deposit_year', '=', $currentYear)->orWhere("account_id", $id)->get();
//
//         return view('superAdmin.save_money.show', compact('deposits'));
//     }

    public function payDepositsId($id)
    {
        $currentYear = (int)date('Y');
        $deposits = $this->depositService->getDepositsByAccountAndYear($id, $currentYear);
        return view('superAdmin.save_money.show', compact('deposits'));
    }


//     public function processPayDeposits($id)
//     {
//         $deposit = Deposit::findOrFail($id);
//         $deposit->deposit_flag = Enum::DEPOSIT_FLAG_Y;
//         $deposit->deposit_updated_by = auth()->id();
//         $deposit->deposit_updated_date = now();
//         $deposit->save();


//         $accountTransaction = new AccountTransaction([
//             'account_no' => $deposit->account->account_no,
//             'user_id' => $deposit->user_id,
//             'at_date' => now(),
//             'at_unit' => $deposit->deposit_unit,
//             'at_amount' => $deposit->deposit_amount,
//             'at_type' => Enum::AT_TYPE_DP,
//             'at_remark' => 'จ่ายตรง',
//             'at_created_by' => auth()->id()
//         ]);
//         $accountTransaction->save();

// // การรับเงินเข้าบัญชี
//         // $savingsAccount = Account::where('account_no', Enum::ACCOUNT_SAVINGS)->first();
//         $savingsAccount = Account::where('account_no', $deposit->account->account_no)->first();
//         if ($savingsAccount) {
//             $savingsAccount->account_balance += $deposit->deposit_amount;
//             $savingsAccount->save();
//         }

//         $accountTransactionDS = new AccountTransaction([
//             'account_no' => $savingsAccount->account_no,
//             'user_id' => $savingsAccount->user_id,
//             'at_date' => now(),
//             'at_unit' => $deposit->deposit_unit,
//             'at_amount' => $deposit->deposit_amount,
//             'at_type' => Enum::AT_TYPE_DS,
//             'reference_id' => $accountTransaction->at_id,
//             'at_remark' => 'ได้รับเงินจาก ' . $deposit->user->name,
//             'at_created_by' => auth()->id()
//         ]);
//         $accountTransactionDS->save();

//         return redirect()->route('payDepositsId', ['id' => $deposit->account->account_id])->with('success', 'ชำระเงินสำเร็จ');
//         // return redirect()->route('deposits.index')->with('success', 'รอการตรวจสอบ');
//     }

    public function processPayDeposits($id)
    {
        try {
            $deposit = $this->depositService->findDepositById($id);

            if ($deposit->deposit_flag === Enum::DEPOSIT_FLAG_W) {
                // โอนเงิน (Transfer)
                $payStatus = Enum::DEPOSIT_PAY_TYPE_TRAN;

            } elseif ($deposit->deposit_flag === Enum::DEPOSIT_FLAG_P) {
                // เงินสด (Cash)
                $payStatus = Enum::DEPOSIT_PAY_TYPE_CASH;
            }

            $this->depositService->processPayment($deposit, $payStatus);
            return redirect()->route('payDepositsId', ['id' => $deposit->account->account_id])
                ->with('success', 'ชำระเงินสำเร็จ');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Failed to process payment']);
        }
    }

//    public function processPayDeposits($depositId)
//    {
//        try {
//            $deposit = Deposit::findOrFail($depositId);
//
//            if ($deposit->deposit_flag === 'W') {
//                // โอนเงิน (Transfer)
//                $deposit->deposit_flag = 'Y'; // จ่ายแล้ว
//            } elseif ($deposit->deposit_flag === 'P') {
//            // เงินสด (Cash)
//            $deposit->deposit_flag = 'Y'; // จ่ายแล้ว
//        }
//
//                $deposit->save();
//
//            return redirect()->back()->with('success', 'ยืนยันการชำระสำเร็จ');
//        } catch (\Exception $e) {
//            return redirect()->back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการยืนยันการชำระ']);
//        }
//    }

//     public function upload(Request $request)
//     {

//         $request->validate([
//             'document' => 'required|file|mimes:pdf,doc,docx,jpg,png'
//         ]);

//         $file = $request->file('document');
//         $fileContent = file_get_contents($file->getRealPath());

//         $deposit = Deposit::findOrFail($request->deposit_id);
//         $deposit->deposit_flag = Enum::DEPOSIT_FLAG_W;
//         $deposit->deposit_pic = $fileContent;
//         $deposit->deposit_updated_by = auth()->id();
//         $deposit->deposit_updated_date = now();
//         $deposit->save();

// //        $accountTransaction = new AccountTransaction([
// //            'account_no' => $deposit->account->account_no,
// //            'user_id' => $deposit->user_id,
// //            'at_date' => now(),
// //            'at_unit' => $deposit->deposit_unit,
// //            'at_amount' => $deposit->deposit_amount,
// //            'at_type' => Enum::AT_TYPE_DP,
// //            'at_remark' => 'จ่ายตรง',
// //            'at_created_by' => auth()->id()
// //        ]);
// //        $accountTransaction->save();
// //
// //// การรับเงินเข้าบัญชี
// //        $savingsAccount = Account::where('account_no', Enum::ACCOUNT_SAVINGS)->first();
// //        $accountTransactionDS = new AccountTransaction([
// //            'account_no' => $savingsAccount->account_no,
// //            'user_id' => $savingsAccount->user_id,
// //            'at_date' => now(),
// //            'at_unit' => $deposit->deposit_unit,
// //            'at_amount' => $deposit->deposit_amount,
// //            'at_type' => Enum::AT_TYPE_DS,
// //            'reference_id' => $accountTransaction->at_id,
// //            'at_remark' => 'ได้รับเงินจาก ' . $deposit->user->name,
// //            'at_created_by' => auth()->id()
// //        ]);
// //        $accountTransactionDS->save();


//         // return redirect()->route('deposits.index')->with('success', 'การจ่ายเงินสำเร็จ');
//         return redirect()->route('payDepositsId', ['id' => $deposit->account->account_id])->with('success', 'รอการตรวจสอบ');
//     }

    public function processUploadDeposits(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,png'
        ]);

        try {
            $deposit = $this->depositService->findDepositById($request->deposit_id);
            $this->depositService->uploadSlipDocument($deposit, $request->file('document'));
            return redirect()->route('payDepositsId', ['id' => $deposit->account->account_id])
                ->with('success', 'รอการตรวจสอบ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to upload document']);
        }
    }

    // public function showDocument($depositId)
    // {
    //     $deposit = Deposit::findOrFail($depositId);
    //     $content = $deposit->deposit_pic;
    //     $mimeType = 'application/pdf'; // ตั้งค่า MIME type ตามประเภทไฟล์ที่คุณจัดเก็บ

    //     return response($content)->header('Content-Type', $mimeType);
    // }

    public function showDocument($depositId)
    {
        try {
            $deposit = $this->depositService->findDepositById($depositId);
            $content = $deposit->deposit_pic;
            $mimeType = 'application/pdf';
            return response($content)->header('Content-Type', $mimeType);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to show document']);
        }
    }

    public function showFile($depositId)
    {
        try {
            $deposit = $this->depositService->findDepositById($depositId);
            $content = $deposit->deposit_pic;

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($content);
    
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mimeType, $allowedTypes)) {
                return back()->withErrors(['error' => 'Unsupported file type.']);
            }
    
            return response($content)->header('Content-Type', $mimeType);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to show document']);
        }
    }
    


    public function depositTotalAmount(Request $request, UserService $userService)
    {
        $filters = $request->only(['search', 'status', 'position']);
        
        $users = User::with([
            'position',
            'rule',
            'accounts',
        ])->get();
        
        $accounts = $this->accountService->getAllAccounts();
    
        return view('superAdmin.deposit.total', compact('users', 'accounts'));
    }
    public function depositStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'account_id' => 'required|exists:account,account_id',
            'deposit_amount' => 'required|numeric|min:0.01',
            'deposit_date' => 'required|date',
            'deposit_pay_type' => 'required|in:CASH,TRAN,WAIT',
            'deposit_pic' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:16384' // 16MB
        ]);

        $fileContent = null;
        $fileType = null;

        if ($request->hasFile('deposit_pic')) {
            $file = $request->file('deposit_pic');

            if ($file->getSize() > 16 * 1024 * 1024) {
                return back()->withErrors(['deposit_pic' => 'ไฟล์มีขนาดเกิน 16MB'])->withInput();
            }

            $fileContent = file_get_contents($file->getRealPath());
            $fileType = $file->getClientMimeType();
        }
        $account = \App\Models\Account::findOrFail($request->account_id);

        $deposit = new Deposit();
        $deposit->user_id = $request->user_id;
        $deposit->deposit_year = "2024";
        $deposit->deposit_month_no = "12";
        $deposit->deposit_month = "ธันวาคม";
        $deposit->deposit_unit = $account->account_final_unit;
        $deposit->account_id = $request->account_id;
        $deposit->deposit_amount = $request->deposit_amount;
        $deposit->deposit_date = $request->deposit_date;
        $deposit->deposit_pay_type = $request->deposit_pay_type;
        $deposit->deposit_flag = Enum::DEPOSIT_FLAG_P;

        if ($fileContent) {
            $deposit->deposit_pic = $fileContent;
            $deposit->deposit_pic_type = $fileType;
        }

        $deposit->deposit_created_by = Auth::id();
        $deposit->deposit_updated_by = Auth::id();
        $deposit->save();

        return redirect()->route('deposit.index')->with('success', 'บันทึกข้อมูลการฝากเรียบร้อยแล้ว');
    }
    public function getAccountsByUser($userId)
    {
        $accounts = Account::where('user_id', $userId)->get(['account_id', 'account_no']);
        return response()->json($accounts);
    }
}
