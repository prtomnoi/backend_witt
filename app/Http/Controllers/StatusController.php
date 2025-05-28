<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class StatusController extends Controller
{
    protected $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * แสดงรายการ Status ทั้งหมด
     */
    public function index()
    {
        $data = $this->statusService->getAllStatuses();
        return view('superAdmin.status.index', compact('data'));
    }

    /**
     * แสดงฟอร์มสร้าง Status
     */
    public function create()
    {
//        $statusTypes = Enum::getStatusTypesDropdown();
//        $defaultStatuses = Enum::getDefaultStatusesDropdown();
        return view('superAdmin.status.create');
    }

    /**
     * บันทึก Status ใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|max:100|unique:status',
            'status_type' => 'required|in:D,W,P',
            'status_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $this->statusService->createStatus($request->all());
            return redirect()->route('status.index')
                ->with('success', 'Status created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create status'])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไข Status
     */
    public function edit($id)
    {
        $status = $this->statusService->findStatusById($id);
//        $statusTypes = Enum::getStatusTypesDropdown();
//        $defaultStatuses = Enum::getDefaultStatusesDropdown();
        return view('superAdmin.status.edit', compact('status'));
    }

    /**
     * อัพเดท Status
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status_name' => 'required|max:100|unique:status,status_name,' . $id . ',status_id',
            'status_type' => 'required|in:D,W,P',
            'status_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $status = $this->statusService->findStatusById($id);
            $this->statusService->updateStatus($status, $request->all());
            return redirect()->route('status.index')
                ->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update status'])
                ->withInput();
        }
    }

    /**
     * เปลี่ยนสถานะ Status
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $status = $this->statusService->findStatusById($id);
            $this->statusService->changeStatus($status, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
