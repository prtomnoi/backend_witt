<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\MemberDividend;
use App\Services\MemberDividendService;
use App\Services\DividendAllocationService;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class MemberDividendController
 * @package App\Http\Controllers
 */
class MemberDividendController extends Controller
{
    /**
     * @var MemberDividendService
     */
    protected $memberDividendService;

    /**
     * @var DividendAllocationService
     */
    protected $dividendAllocationService;

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * MemberDividendController constructor.
     *
     * @param MemberDividendService $memberDividendService
     * @param DividendAllocationService $dividendAllocationService
     * @param AccountService $accountService
     */
    public function __construct(
        MemberDividendService     $memberDividendService,
        DividendAllocationService $dividendAllocationService,
        AccountService            $accountService
    )
    {
        $this->memberDividendService = $memberDividendService;
        $this->dividendAllocationService = $dividendAllocationService;
        $this->accountService = $accountService;
    }

    /**
     * แสดงรายการเงินปันผลรายสมาชิกทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $memberDividends = $this->memberDividendService->getAll(10);
        return view('admin.member_dividend.index', compact('memberDividends'));
    }

    /**
     * แสดงรายการเงินปันผลรายสมาชิกตามการจัดสรรเงินปันผล
     *
     * @param int $daId
     * @return \Illuminate\View\View
     */
    public function indexByDividendAllocation($daId)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($daId);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        $memberDividends = $this->memberDividendService->getByDividendAllocationId($daId, 10);

        return view('admin.member_dividend.index_by_allocation', compact('memberDividends', 'dividendAllocation'));
    }

    /**
     * แสดงฟอร์มสร้างเงินปันผลรายสมาชิกใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // ดึงข้อมูลการจัดสรรเงินปันผลที่อนุมัติแล้ว
        $dividendAllocations = $this->dividendAllocationService->getByStatus(Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED, 100);

        // ดึงข้อมูลบัญชีสมาชิกที่มีสถานะใช้งาน
        $accounts = $this->accountService->getActiveAccounts(100);

        $statuses = Enum::getMemberDividendStatusDropdown();
        $paymentMethods = Enum::getMemberDividendPaymentMethodDropdown();

        return view('admin.member_dividend.create', compact('dividendAllocations', 'accounts', 'statuses', 'paymentMethods'));
    }

    /**
     * บันทึกข้อมูลเงินปันผลรายสมาชิกใหม่
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'da_id' => 'required|exists:dividend_allocation,da_id',
            'account_id' => 'required|exists:account,account_id',
            'md_year' => 'required|string|size:4',
            'md_saving_months' => 'required|integer|min:0|max:12',
            'md_avg_saving_amount' => 'required|numeric|min:0',
            'md_dividend_amount' => 'required|numeric|min:0',
            'md_status' => 'required|in:' . implode(',', Enum::getMemberDividendStatusForValidation()),
            'md_payment_date' => 'nullable|date',
            'md_payment_method' => 'required|in:' . implode(',', Enum::getMemberDividendPaymentMethodForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่ามีเงินปันผลรายสมาชิกสำหรับบัญชีนี้และการจัดสรรนี้แล้วหรือไม่
        $existingDividend = MemberDividend::where('da_id', $request->da_id)
            ->where('account_id', $request->account_id)
            ->first();

        if ($existingDividend) {
            return redirect()->back()
                ->with('error', 'มีข้อมูลเงินปันผลสำหรับสมาชิกและการจัดสรรนี้แล้ว')
                ->withInput();
        }

        // สร้างข้อมูลเงินปันผลรายสมาชิก
        $userId = Auth::id() ?? 1; // ถ้าไม่มี Auth ให้ใช้ ID 1 แทน

        $this->memberDividendService->create([
            'da_id' => $request->da_id,
            'account_id' => $request->account_id,
            'md_year' => $request->md_year,
            'md_saving_months' => $request->md_saving_months,
            'md_avg_saving_amount' => $request->md_avg_saving_amount,
            'md_dividend_amount' => $request->md_dividend_amount,
            'md_status' => $request->md_status,
            'md_payment_date' => $request->md_payment_date,
            'md_payment_method' => $request->md_payment_method,
            'md_created_by' => $userId,
            'md_updated_by' => $userId,
        ]);

        return redirect()->route('member-dividend.index')
            ->with('success', 'เพิ่มข้อมูลเงินปันผลรายสมาชิกเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดเงินปันผลรายสมาชิก
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        return view('admin.member_dividend.show', compact('memberDividend'));
    }

    /**
     * แสดงฟอร์มแก้ไขเงินปันผลรายสมาชิก
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        // ตรวจสอบว่าสามารถแก้ไขได้หรือไม่
        if (!in_array($memberDividend->md_status, [Enum::MEMBER_DIVIDEND_STATUS_PENDING, Enum::MEMBER_DIVIDEND_STATUS_CANCELLED])) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลเงินปันผลรายสมาชิกที่จ่ายแล้วได้');
        }

        $dividendAllocations = $this->dividendAllocationService->getAll(100);
        $accounts = $this->accountService->getActiveAccounts(100);
        $statuses = Enum::getMemberDividendStatusDropdown();
        $paymentMethods = Enum::getMemberDividendPaymentMethodDropdown();

        return view('admin.member_dividend.edit', compact('memberDividend', 'dividendAllocations', 'accounts', 'statuses', 'paymentMethods'));
    }

    /**
     * อัพเดตข้อมูลเงินปันผลรายสมาชิก
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        // ตรวจสอบว่าสามารถแก้ไขได้หรือไม่
        if (!in_array($memberDividend->md_status, [Enum::MEMBER_DIVIDEND_STATUS_PENDING, Enum::MEMBER_DIVIDEND_STATUS_CANCELLED])) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลเงินปันผลรายสมาชิกที่จ่ายแล้วได้');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'da_id' => 'required|exists:dividend_allocation,da_id',
            'account_id' => 'required|exists:account,account_id',
            'md_year' => 'required|string|size:4',
            'md_saving_months' => 'required|integer|min:0|max:12',
            'md_avg_saving_amount' => 'required|numeric|min:0',
            'md_dividend_amount' => 'required|numeric|min:0',
            'md_status' => 'required|in:' . implode(',', Enum::getMemberDividendStatusForValidation()),
            'md_payment_date' => 'nullable|date',
            'md_payment_method' => 'required|in:' . implode(',', Enum::getMemberDividendPaymentMethodForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่ามีเงินปันผลรายสมาชิกสำหรับบัญชีนี้และการจัดสรรนี้แล้วหรือไม่ (กรณีเปลี่ยน da_id หรือ account_id)
        if ($request->da_id != $memberDividend->da_id || $request->account_id != $memberDividend->account_id) {
            $existingDividend = MemberDividend::where('da_id', $request->da_id)
                ->where('account_id', $request->account_id)
                ->where('md_id', '!=', $id)
                ->first();

            if ($existingDividend) {
                return redirect()->back()
                    ->with('error', 'มีข้อมูลเงินปันผลสำหรับสมาชิกและการจัดสรรนี้แล้ว')
                    ->withInput();
            }
        }

        // ข้อมูลที่จะอัพเดต
        $updateData = [
            'da_id' => $request->da_id,
            'account_id' => $request->account_id,
            'md_year' => $request->md_year,
            'md_saving_months' => $request->md_saving_months,
            'md_avg_saving_amount' => $request->md_avg_saving_amount,
            'md_dividend_amount' => $request->md_dividend_amount,
            'md_status' => $request->md_status,
            'md_payment_date' => $request->md_payment_date,
            'md_payment_method' => $request->md_payment_method,
            'md_updated_by' => Auth::id() ?? 1,
            'md_updated_date' => now(),
        ];

        // อัพเดตข้อมูล
        $this->memberDividendService->update($memberDividend, $updateData);

        return redirect()->route('member-dividend.index')
            ->with('success', 'อัพเดตข้อมูลเงินปันผลรายสมาชิกเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลเงินปันผลรายสมาชิก
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        // ตรวจสอบว่าสามารถลบได้หรือไม่
        if (!in_array($memberDividend->md_status, [Enum::MEMBER_DIVIDEND_STATUS_PENDING, Enum::MEMBER_DIVIDEND_STATUS_CANCELLED])) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่สามารถลบข้อมูลเงินปันผลรายสมาชิกที่จ่ายแล้วได้');
        }

        $this->memberDividendService->delete($memberDividend);

        return redirect()->route('member-dividend.index')
            ->with('success', 'ลบข้อมูลเงินปันผลรายสมาชิกเรียบร้อยแล้ว');
    }

    /**
     * คำนวณเงินปันผลรายสมาชิก
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculate(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'da_id' => 'required|exists:dividend_allocation,da_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ดึงข้อมูลการจัดสรรเงินปันผล
        $dividendAllocation = $this->dividendAllocationService->getById($request->da_id);

        if (!$dividendAllocation) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล')
                ->withInput();
        }

        // ตรวจสอบว่าอนุมัติแล้วหรือยัง
        if ($dividendAllocation->da_status !== Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถคำนวณเงินปันผลรายสมาชิกสำหรับการจัดสรรเงินปันผลที่ยังไม่ได้อนุมัติได้')
                ->withInput();
        }

        // คำนวณเงินปันผลรายสมาชิก
        $userId = Auth::id() ?? 1;
        $result = $this->memberDividendService->calculateMemberDividends($dividendAllocation, $userId);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถคำนวณเงินปันผลรายสมาชิกได้: ' . ($result['message'] ?? 'เกิดข้อผิดพลาด'))
                ->withInput();
        }

        return redirect()->route('member-dividend.index-by-allocation', ['daId' => $dividendAllocation->da_id])
            ->with('success', "คำนวณเงินปันผลรายสมาชิกเรียบร้อยแล้ว สร้างรายการใหม่ {$result['created']} รายการ รวมเงินปันผล " . number_format($result['total_dividend'], 2) . " บาท (มูลค่าต่อหุ้น " . number_format($result['value_per_unit'], 2) . " บาท)");
    }

    /**
     * จ่ายเงินปันผลให้กับสมาชิก
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay(Request $request, $id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        // ตรวจสอบว่าสามารถจ่ายเงินปันผลได้หรือไม่
        if (!$memberDividend->canPay()) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่สามารถจ่ายเงินปันผลที่ไม่ได้อยู่ในสถานะรอจ่ายได้');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:' . implode(',', Enum::getMemberDividendPaymentMethodForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // จ่ายเงินปันผล
        $userId = Auth::id() ?? 1;
        $result = $this->memberDividendService->payDividend($memberDividend, $request->payment_method, $userId);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถจ่ายเงินปันผลได้')
                ->withInput();
        }

        return redirect()->route('member-dividend.index')
            ->with('success', 'จ่ายเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * จ่ายเงินปันผลให้กับสมาชิกทั้งหมดตามการจัดสรรเงินปันผล
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payAll(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'da_id' => 'required|exists:dividend_allocation,da_id',
            'payment_method' => 'required|in:' . implode(',', Enum::getMemberDividendPaymentMethodForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ดึงข้อมูลการจัดสรรเงินปันผล
        $dividendAllocation = $this->dividendAllocationService->getById($request->da_id);

        if (!$dividendAllocation) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล')
                ->withInput();
        }

        // ตรวจสอบว่าอนุมัติแล้วหรือยัง
        if ($dividendAllocation->da_status !== Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถจ่ายเงินปันผลสำหรับการจัดสรรเงินปันผลที่ยังไม่ได้อนุมัติได้')
                ->withInput();
        }

        // จ่ายเงินปันผลให้กับสมาชิกทั้งหมด
        $userId = Auth::id() ?? 1;
        $result = $this->memberDividendService->payAllDividends($dividendAllocation, $request->payment_method, $userId);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถจ่ายเงินปันผลได้: ' . ($result['message'] ?? 'เกิดข้อผิดพลาด'))
                ->withInput();
        }

        return redirect()->route('member-dividend.index-by-allocation', ['daId' => $dividendAllocation->da_id])
            ->with('success', "จ่ายเงินปันผลเรียบร้อยแล้ว จำนวน {$result['paid']} รายการ รวมเงินปันผล " . number_format($result['total_paid'], 2) . " บาท");
    }

    /**
     * ยกเลิกการจ่ายเงินปันผล
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $memberDividend = $this->memberDividendService->getById($id);

        if (!$memberDividend) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลเงินปันผลรายสมาชิก');
        }

        // ตรวจสอบว่าสามารถยกเลิกการจ่ายเงินปันผลได้หรือไม่
        if (!$memberDividend->canCancel()) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่สามารถยกเลิกการจ่ายเงินปันผลที่ไม่ได้อยู่ในสถานะที่เหมาะสมได้');
        }

        // ยกเลิกการจ่ายเงินปันผล
        $userId = Auth::id() ?? 1;
        $result = $this->memberDividendService->cancelDividend($memberDividend, $userId);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถยกเลิกการจ่ายเงินปันผลได้')
                ->withInput();
        }

        return redirect()->route('member-dividend.index')
            ->with('success', 'ยกเลิกการจ่ายเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายงานเงินปันผลรายสมาชิกตามปี
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function reportByYear(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $memberDividends = $this->memberDividendService->getByYear($year, 100);

        // คำนวณสรุปข้อมูล
        $summary = [
            'total_members' => $memberDividends->total(),
            'total_dividend' => $memberDividends->sum('md_dividend_amount'),
            'paid_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_PAID)->count(),
            'transferred_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_TRANSFERRED)->count(),
            'pending_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_PENDING)->count(),
            'cancelled_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_CANCELLED)->count(),
        ];

        // สร้างตัวเลือกปี (ย้อนหลัง 10 ปี)
        $years = [];
        $currentYear = (int)date('Y');
        for ($i = 0; $i < 10; $i++) {
            $yearValue = $currentYear - $i;
            $years[$yearValue] = $yearValue;
        }

        return view('admin.member_dividend.report_by_year', compact('memberDividends', 'year', 'years', 'summary'));
    }

    /**
     * แสดงรายงานเงินปันผลรายสมาชิกตามสถานะ
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function reportByStatus(Request $request)
    {
        $status = $request->input('status', Enum::MEMBER_DIVIDEND_STATUS_PENDING);
        $memberDividends = $this->memberDividendService->getByStatus($status, 100);

        // คำนวณสรุปข้อมูล
        $summary = [
            'total_members' => $memberDividends->total(),
            'total_dividend' => $memberDividends->sum('md_dividend_amount'),
        ];

        $statuses = Enum::getMemberDividendStatusDropdown();

        return view('admin.member_dividend.report_by_status', compact('memberDividends', 'status', 'statuses', 'summary'));
    }

    /**
     * แสดงรายงานเงินปันผลรายสมาชิกตามบัญชี
     *
     * @param int $accountId
     * @return \Illuminate\View\View
     */
    public function reportByAccount($accountId)
    {
        $account = $this->accountService->getById($accountId);

        if (!$account) {
            return redirect()->route('member-dividend.index')
                ->with('error', 'ไม่พบข้อมูลบัญชีสมาชิก');
        }

        $memberDividends = $this->memberDividendService->getByAccountId($accountId, 100);

        // คำนวณสรุปข้อมูล
        $summary = [
            'total_years' => $memberDividends->count(),
            'total_dividend' => $memberDividends->sum('md_dividend_amount'),
            'paid_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_PAID)->count(),
            'transferred_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_TRANSFERRED)->count(),
            'pending_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_PENDING)->count(),
            'cancelled_count' => $memberDividends->where('md_status', Enum::MEMBER_DIVIDEND_STATUS_CANCELLED)->count(),
        ];

        return view('admin.member_dividend.report_by_account', compact('memberDividends', 'account', 'summary'));
    }

    /**
     * ส่งออกข้อมูลเงินปันผลรายสมาชิกเป็นไฟล์ Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $daId = $request->input('da_id');
        $year = $request->input('year');

        // ถ้ามีการระบุ da_id ให้ดึงข้อมูลตามการจัดสรรเงินปันผล
        if ($daId) {
            $memberDividends = $this->memberDividendService->getByDividendAllocationId($daId, 1000);
            $fileName = "member_dividend_allocation_{$daId}.xlsx";
        } // ถ้ามีการระบุปี ให้ดึงข้อมูลตามปี
        else if ($year) {
            $memberDividends = $this->memberDividendService->getByYear($year, 1000);
            $fileName = "member_dividend_year_{$year}.xlsx";
        } // ถ้าไม่มีการระบุเงื่อนไข ให้ดึงข้อมูลทั้งหมด
        else {
            $memberDividends = $this->memberDividendService->getAll(1000);
            $fileName = "member_dividend_all.xlsx";
        }

        // สร้างไฟล์ Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // กำหนดหัวตาราง
        $sheet->setCellValue('A1', 'ลำดับ');
        $sheet->setCellValue('B1', 'รหัสสมาชิก');
        $sheet->setCellValue('C1', 'ชื่อ-นามสกุล');
        $sheet->setCellValue('D1', 'ปี');
        $sheet->setCellValue('E1', 'จำนวนเดือนที่ส่งเงินสัจจะ');
        $sheet->setCellValue('F1', 'เงินสัจจะเฉลี่ย (บาท)');
        $sheet->setCellValue('G1', 'จำนวนเงินปันผล (บาท)');
        $sheet->setCellValue('H1', 'สถานะ');
        $sheet->setCellValue('I1', 'วิธีการจ่าย');
        $sheet->setCellValue('J1', 'วันที่จ่าย');

        // เพิ่มข้อมูลในตาราง
        $row = 2;
        foreach ($memberDividends as $index => $memberDividend) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $memberDividend->account->account_no);
            $sheet->setCellValue('C' . $row, $memberDividend->account->user->user_firstname . ' ' . $memberDividend->account->user->user_lastname);
            $sheet->setCellValue('D' . $row, $memberDividend->md_year);
            $sheet->setCellValue('E' . $row, $memberDividend->md_saving_months);
            $sheet->setCellValue('F' . $row, $memberDividend->md_avg_saving_amount);
            $sheet->setCellValue('G' . $row, $memberDividend->md_dividend_amount);
            $sheet->setCellValue('H' . $row, $memberDividend->status_text);
            $sheet->setCellValue('I' . $row, $memberDividend->payment_method_text);
            $sheet->setCellValue('J' . $row, $memberDividend->md_payment_date);
            $row++;
        }

        // จัดรูปแบบตาราง
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        $sheet->getStyle('A1:J' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('F2:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // ปรับความกว้างคอลัมน์อัตโนมัติ
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // สร้างไฟล์ Excel และดาวน์โหลด
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'member_dividend_');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
