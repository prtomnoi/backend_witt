<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\DividendAllocation;
use App\Services\DividendAllocationService;
use App\Services\AnnualProfitService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class DividendAllocationController
 * @package App\Http\Controllers
 */
class DividendAllocationController extends Controller
{
    /**
     * @var DividendAllocationService
     */
    protected $dividendAllocationService;

    /**
     * @var AnnualProfitService
     */
    protected $annualProfitService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * DividendAllocationController constructor.
     *
     * @param DividendAllocationService $dividendAllocationService
     * @param AnnualProfitService $annualProfitService
     * @param UserService $userService
     */
    public function __construct(
        DividendAllocationService $dividendAllocationService,
        AnnualProfitService       $annualProfitService,
        UserService               $userService
    )
    {
        $this->dividendAllocationService = $dividendAllocationService;
        $this->annualProfitService = $annualProfitService;
        $this->userService = $userService;
    }

    /**
     * แสดงรายการการจัดสรรเงินปันผลทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dividendAllocations = $this->dividendAllocationService->getAll(10);
        return view('admin.dividend_allocation.index', compact('dividendAllocations'));
    }

    /**
     * แสดงฟอร์มสร้างการจัดสรรเงินปันผลใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // ดึงข้อมูลผลประกอบการประจำปีที่ปิดงบแล้วแต่ยังไม่ได้จัดสรรผลกำไร
        $annualProfits = $this->annualProfitService->getByStatus(Enum::ANNUAL_PROFIT_STATUS_CLOSED, 100);
        $statuses = Enum::getDividendAllocationStatusDropdown();

        return view('admin.dividend_allocation.create', compact('annualProfits', 'statuses'));
    }

    /**
     * บันทึกข้อมูลการจัดสรรเงินปันผลใหม่
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'ap_id' => 'required|exists:annual_profit,ap_id',
            'da_total_amount' => 'required|numeric|min:0',
            'da_contribution_amount' => 'required|numeric|min:0',
            'da_welfare_amount' => 'required|numeric|min:0',
            'da_distribution_date' => 'nullable|date',
            'da_status' => 'required|in:' . implode(',', Enum::getDividendAllocationStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่ามีการจัดสรรเงินปันผลสำหรับผลประกอบการประจำปีนี้แล้วหรือไม่
        $existingAllocation = $this->dividendAllocationService->getByAnnualProfitId($request->ap_id);
        if ($existingAllocation) {
            return redirect()->back()
                ->with('error', 'มีการจัดสรรเงินปันผลสำหรับผลประกอบการประจำปีนี้แล้ว')
                ->withInput();
        }

        // สร้างข้อมูลการจัดสรรเงินปันผล
        $userId = Auth::id() ?? 1; // ถ้าไม่มี Auth ให้ใช้ ID 1 แทน

        $this->dividendAllocationService->create([
            'ap_id' => $request->ap_id,
            'da_total_amount' => $request->da_total_amount,
            'da_contribution_amount' => $request->da_contribution_amount,
            'da_welfare_amount' => $request->da_welfare_amount,
            'da_distribution_date' => $request->da_distribution_date,
            'da_status' => $request->da_status,
            'da_created_by' => $userId,
            'da_updated_by' => $userId,
        ]);

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'เพิ่มข้อมูลการจัดสรรเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดการจัดสรรเงินปันผล
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        return view('admin.dividend_allocation.show', compact('dividendAllocation'));
    }

    /**
     * แสดงฟอร์มแก้ไขการจัดสรรเงินปันผล
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        // ตรวจสอบว่าสามารถแก้ไขได้หรือไม่
        if ($dividendAllocation->da_status === Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลการจัดสรรเงินปันผลที่แจกจ่ายแล้วได้');
        }

        $annualProfits = $this->annualProfitService->getAll(100);
        $statuses = Enum::getDividendAllocationStatusDropdown();

        return view('admin.dividend_allocation.edit', compact('dividendAllocation', 'annualProfits', 'statuses'));
    }

    /**
     * อัพเดตข้อมูลการจัดสรรเงินปันผล
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        // ตรวจสอบว่าสามารถแก้ไขได้หรือไม่
        if ($dividendAllocation->da_status === Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลการจัดสรรเงินปันผลที่แจกจ่ายแล้วได้');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'ap_id' => 'required|exists:annual_profit,ap_id',
            'da_total_amount' => 'required|numeric|min:0',
            'da_contribution_amount' => 'required|numeric|min:0',
            'da_welfare_amount' => 'required|numeric|min:0',
            'da_distribution_date' => 'nullable|date',
            'da_status' => 'required|in:' . implode(',', Enum::getDividendAllocationStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่ามีการจัดสรรเงินปันผลสำหรับผลประกอบการประจำปีนี้แล้วหรือไม่ (กรณีเปลี่ยน ap_id)
        if ($request->ap_id != $dividendAllocation->ap_id) {
            $existingAllocation = $this->dividendAllocationService->getByAnnualProfitId($request->ap_id);
            if ($existingAllocation) {
                return redirect()->back()
                    ->with('error', 'มีการจัดสรรเงินปันผลสำหรับผลประกอบการประจำปีนี้แล้ว')
                    ->withInput();
            }
        }

        // ข้อมูลที่จะอัพเดต
        $updateData = [
            'ap_id' => $request->ap_id,
            'da_total_amount' => $request->da_total_amount,
            'da_contribution_amount' => $request->da_contribution_amount,
            'da_welfare_amount' => $request->da_welfare_amount,
            'da_distribution_date' => $request->da_distribution_date,
            'da_status' => $request->da_status,
            'da_updated_by' => Auth::id() ?? 1,
            'da_updated_date' => now(),
        ];

        // อัพเดตข้อมูล
        $this->dividendAllocationService->update($dividendAllocation, $updateData);

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'อัพเดตข้อมูลการจัดสรรเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลการจัดสรรเงินปันผล
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        // ตรวจสอบว่าสามารถลบได้หรือไม่
        if ($dividendAllocation->da_status === Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถลบข้อมูลการจัดสรรเงินปันผลที่แจกจ่ายแล้วได้');
        }

        $this->dividendAllocationService->delete($dividendAllocation);

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'ลบข้อมูลการจัดสรรเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * คำนวณการจัดสรรเงินปันผล
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculate(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'ap_id' => 'required|exists:annual_profit,ap_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ดึงข้อมูลผลประกอบการประจำปี
        $annualProfit = $this->annualProfitService->getById($request->ap_id);

        if (!$annualProfit) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี')
                ->withInput();
        }

        // ตรวจสอบว่าปิดงบแล้วหรือยัง
        if ($annualProfit->ap_status !== Enum::ANNUAL_PROFIT_STATUS_CLOSED) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถคำนวณการจัดสรรเงินปันผลสำหรับผลประกอบการประจำปีที่ยังไม่ปิดงบได้')
                ->withInput();
        }

        // คำนวณการจัดสรรเงินปันผล
        $userId = Auth::id() ?? 1;
        $this->dividendAllocationService->calculateDividendAllocation($annualProfit, $userId);

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'คำนวณข้อมูลการจัดสรรเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * อัพเดตสถานะการจัดสรรเงินปันผล
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', Enum::getDividendAllocationStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่าสามารถอัพเดตสถานะได้หรือไม่
        if ($dividendAllocation->da_status === Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED && $request->status !== Enum::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถเปลี่ยนสถานะของการจัดสรรเงินปันผลที่แจกจ่ายแล้วได้');
        }

        // อัพเดตสถานะ
        $userId = Auth::id() ?? 1;
        $this->dividendAllocationService->updateStatus($dividendAllocation, $request->status, $userId);

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'อัพเดตสถานะการจัดสรรเงินปันผลเรียบร้อยแล้ว');
    }

    /**
     * แจกจ่ายเงินปันผล
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function distribute($id)
    {
        $dividendAllocation = $this->dividendAllocationService->getById($id);

        if (!$dividendAllocation) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่พบข้อมูลการจัดสรรเงินปันผล');
        }

        // ตรวจสอบว่าสามารถแจกจ่ายเงินปันผลได้หรือไม่
        if ($dividendAllocation->da_status !== Enum::DIVIDEND_ALLOCATION_STATUS_APPROVED) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถแจกจ่ายเงินปันผลที่ยังไม่ได้อนุมัติได้');
        }

        // แจกจ่ายเงินปันผล
        $userId = Auth::id() ?? 1;
        $result = $this->dividendAllocationService->distributeDividend($dividendAllocation, $userId);

        if (!$result) {
            return redirect()->route('dividend-allocation.index')
                ->with('error', 'ไม่สามารถแจกจ่ายเงินปันผลได้');
        }

        return redirect()->route('dividend-allocation.index')
            ->with('success', 'แจกจ่ายเงินปันผลเรียบร้อยแล้ว');
    }
}
