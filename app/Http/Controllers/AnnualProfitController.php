<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\AnnualProfit;
use App\Services\AnnualProfitService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class AnnualProfitController
 * @package App\Http\Controllers
 */
class AnnualProfitController extends Controller
{
    /**
     * @var AnnualProfitService
     */
    protected $annualProfitService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * AnnualProfitController constructor.
     *
     * @param AnnualProfitService $annualProfitService
     * @param UserService $userService
     */
    public function __construct(
        AnnualProfitService $annualProfitService,
        UserService         $userService
    )
    {
        $this->annualProfitService = $annualProfitService;
        $this->userService = $userService;
    }

    /**
     * แสดงรายการผลประกอบการประจำปีทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $annualProfits = $this->annualProfitService->getAll(10);
        return view('admin.annual_profit.index', compact('annualProfits'));
    }

    /**
     * แสดงฟอร์มสร้างผลประกอบการประจำปีใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $statuses = Enum::getAnnualProfitStatusDropdown();
        return view('admin.annual_profit.create', compact('statuses'));
    }

    /**
     * บันทึกข้อมูลผลประกอบการประจำปีใหม่
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'ap_year' => 'required|string|size:4|unique:annual_profit,ap_year',
            'ap_total_income' => 'required|numeric|min:0',
            'ap_total_expense' => 'required|numeric|min:0',
            'ap_close_date' => 'nullable|date',
            'ap_status' => 'required|in:' . implode(',', Enum::getAnnualProfitStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // สร้างข้อมูลผลประกอบการประจำปี
        $userId = Auth::id() ?? 1; // ถ้าไม่มี Auth ให้ใช้ ID 1 แทน

        $this->annualProfitService->create([
            'ap_year' => $request->ap_year,
            'ap_total_income' => $request->ap_total_income,
            'ap_total_expense' => $request->ap_total_expense,
            'ap_close_date' => $request->ap_close_date,
            'ap_status' => $request->ap_status,
            'ap_created_by' => $userId,
            'ap_updated_by' => $userId,
        ]);

        return redirect()->route('annual-profit.index')
            ->with('success', 'เพิ่มข้อมูลผลประกอบการประจำปีเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดผลประกอบการประจำปี
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        return view('admin.annual_profit.show', compact('annualProfit'));
    }

    /**
     * แสดงฟอร์มแก้ไขผลประกอบการประจำปี
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        $statuses = Enum::getAnnualProfitStatusDropdown();

        return view('admin.annual_profit.edit', compact('annualProfit', 'statuses'));
    }

    /**
     * อัพเดตข้อมูลผลประกอบการประจำปี
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'ap_year' => 'required|string|size:4|unique:annual_profit,ap_year,' . $id . ',ap_id',
            'ap_total_income' => 'required|numeric|min:0',
            'ap_total_expense' => 'required|numeric|min:0',
            'ap_close_date' => 'nullable|date',
            'ap_status' => 'required|in:' . implode(',', Enum::getAnnualProfitStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ข้อมูลที่จะอัพเดต
        $updateData = [
            'ap_year' => $request->ap_year,
            'ap_total_income' => $request->ap_total_income,
            'ap_total_expense' => $request->ap_total_expense,
            'ap_close_date' => $request->ap_close_date,
            'ap_status' => $request->ap_status,
            'ap_updated_by' => Auth::id() ?? 1,
            'ap_updated_date' => now(),
        ];

        // อัพเดตข้อมูล
        $this->annualProfitService->update($annualProfit, $updateData);

        return redirect()->route('annual-profit.index')
            ->with('success', 'อัพเดตข้อมูลผลประกอบการประจำปีเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลผลประกอบการประจำปี
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        // ตรวจสอบว่าสามารถลบได้หรือไม่
        if ($annualProfit->ap_status === Enum::ANNUAL_PROFIT_STATUS_DISTRIBUTED) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่สามารถลบข้อมูลผลประกอบการประจำปีที่จัดสรรผลกำไรแล้วได้');
        }

        $this->annualProfitService->delete($annualProfit);

        return redirect()->route('annual-profit.index')
            ->with('success', 'ลบข้อมูลผลประกอบการประจำปีเรียบร้อยแล้ว');
    }

    /**
     * คำนวณผลประกอบการประจำปี
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculate(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // คำนวณผลประกอบการประจำปี
        $userId = Auth::id() ?? 1;
        $this->annualProfitService->calculateAnnualProfit($request->year, $userId);

        return redirect()->route('annual-profit.index')
            ->with('success', 'คำนวณข้อมูลผลประกอบการประจำปีเรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มจัดสรรผลกำไรประจำปี
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function distributeForm($id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        // ตรวจสอบว่าสามารถจัดสรรผลกำไรได้หรือไม่
        if ($annualProfit->ap_status !== Enum::ANNUAL_PROFIT_STATUS_CLOSED) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่สามารถจัดสรรผลกำไรประจำปีที่ยังไม่ปิดงบได้');
        }

        // ดึงข้อมูลบัญชีหลักสำหรับจัดสรรผลกำไร
        $mainAccounts = [
            Enum::ACCOUNT_SAVINGS => 'บัญชีเงินฝาก',
            Enum::ACCOUNT_DIVIDEND => 'บัญชีปันผล',
            Enum::ACCOUNT_CONTRIBUTION => 'บัญชีเงินสมทบ',
            Enum::ACCOUNT_WELFARE => 'บัญชีสวัสดิการ'
        ];

        return view('admin.annual_profit.distribute', compact('annualProfit', 'mainAccounts'));
    }

    /**
     * จัดสรรผลกำไรประจำปี
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function distribute(Request $request, $id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'distribution' => 'required|array',
            'distribution.*' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่าผลรวมของเปอร์เซ็นต์เท่ากับ 100% หรือไม่
        $totalPercentage = array_sum($request->distribution);
        if ($totalPercentage != 100) {
            return redirect()->back()
                ->with('error', 'ผลรวมของเปอร์เซ็นต์ต้องเท่ากับ 100%')
                ->withInput();
        }

        // จัดสรรผลกำไรประจำปี
        $userId = Auth::id() ?? 1;
        $result = $this->annualProfitService->distributeProfit($annualProfit, $request->distribution, $userId);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถจัดสรรผลกำไรประจำปีได้')
                ->withInput();
        }

        return redirect()->route('annual-profit.index')
            ->with('success', 'จัดสรรผลกำไรประจำปีเรียบร้อยแล้ว');
    }

    /**
     * อัพเดตสถานะผลประกอบการประจำปี
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $annualProfit = $this->annualProfitService->getById($id);

        if (!$annualProfit) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่พบข้อมูลผลประกอบการประจำปี');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', Enum::getAnnualProfitStatusForValidation()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ตรวจสอบว่าสามารถอัพเดตสถานะได้หรือไม่
        if ($annualProfit->ap_status === Enum::ANNUAL_PROFIT_STATUS_DISTRIBUTED && $request->status !== Enum::ANNUAL_PROFIT_STATUS_DISTRIBUTED) {
            return redirect()->route('annual-profit.index')
                ->with('error', 'ไม่สามารถเปลี่ยนสถานะของผลประกอบการประจำปีที่จัดสรรผลกำไรแล้วได้');
        }

        // อัพเดตสถานะ
        $userId = Auth::id() ?? 1;
        $this->annualProfitService->updateStatus($annualProfit, $request->status, $userId);

        return redirect()->route('annual-profit.index')
            ->with('success', 'อัพเดตสถานะผลประกอบการประจำปีเรียบร้อยแล้ว');
    }
}
