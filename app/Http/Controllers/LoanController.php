<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Services\LoanInstallmentPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;

class LoanController extends Controller
{
    protected $loanService;
    protected $accountService;

    public function __construct(LoanInstallmentPaymentService $loanService, AccountService $accountService)
    {
        $this->loanService = $loanService;
        $this->accountService = $accountService;
    }

    /**
     * แสดงแบบฟอร์มสร้างเงินกู้
     */
    public function create(Request $request)
    {
        $users = $this->accountService->getActiveAccounts(); // ดึงบัญชีที่สามารถกู้ได้
        $guarantorOptions = $this->accountService->getGuarantorAccountsForDropdown(); // ดึงบัญชีผู้ค้ำประกัน
        $loanType = is_array($request->type) ? $request->type[0] : $request->type;
        return view('superAdmin.loan.create', compact('users', 'guarantorOptions', 'loanType'));
    }

    /**
     * สร้างรายการกู้ใหม่
     *
     * @param Request $request คำขอจาก HTTP
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|integer|exists:account,account_id',
            'loan_amount' => 'required|numeric|min:1',
            'periods' => 'required|integer|min:1',
            'loanType' => 'required|string|in:N,E',
            'guarantors' => 'array',
            'guarantors.*' => 'nullable|integer|exists:account,account_id',
            'guarantor_account_balance' => 'array',
            'guarantor_spouse_name' => 'array',
            'guarantor_spouse_number' => 'array',
        ]);
    
        // เตรียมข้อมูลผู้ค้ำประกัน
        $guarantorIds = array_filter($validated['guarantors'] ?? []);
        $guarantorData = [];
    
        foreach ($guarantorIds as $index => $guarantorId) {
            $guarantorData[$guarantorId] = [
                'account_balance' => $request->guarantor_account_balance[$index] ?? 0,
                'spouse_name' => $request->guarantor_spouse_name[$index] ?? null,
                'spouse_number' => $request->guarantor_spouse_number[$index] ?? null,
            ];
        }
    
        try {
            $loan = $this->loanService->createNewLoan(
                $validated['account_id'],
                $validated['periods'],
                $validated['loan_amount'],
                $validated['loanType'],
                $guarantorIds,
                $guarantorData,
                $request->loan_reason ?? null 
            );
    
            return redirect()->route('loan.index', ['type' => $validated['loanType']])
            ->with('success', 'สร้างการกู้เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * แสดงรายการเงินกู้
     */
    public function index(Request $request)
    {
        $loanType = $request->query('type');
        $loans = $this->loanService->getLoansByType($loanType);
        return view('superAdmin.loan.index', compact('loans', 'loanType'));
    }

    public function exportPdf($loanId)
    {
        $loan = Loan::with(['account.user', 'guarantors.user'])->findOrFail($loanId);
        // dd($loan);
        return PDF::loadView('superAdmin.loan.pdf', compact('loan'))
                  ->stream('loan_request.pdf');
    }

    public function exportPDF2()
    {
        // $loan = $this->loanService->findLoanById($id);

        // if (!$loan) {
        //     abort(404, "Loan not found");
        // }

        $pdf = Pdf::loadView('superAdmin.loan.pdf3');
        return $pdf->stream("loan.pdf");
    }

    public function exportPDF3()
    {

        $pdf = Pdf::loadView('superAdmin.loan.recreipt');
        return $pdf->stream("loan.pdf");
    }

}
