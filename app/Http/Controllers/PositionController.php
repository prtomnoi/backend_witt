<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\PositionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    /**
     * แสดงรายการ Position ทั้งหมด
     */
    public function index()
    {
        $positions = $this->positionService->getAllPositions();
        return view('superAdmin.position.index', compact('positions'));
    }

    /**
     * แสดงฟอร์มสร้าง Position
     */
    public function create()
    {
//        $statuses = Enum::getDefaultStatusesDropdown();
        return view('superAdmin.position.create');
    }

    /**
     * บันทึก Position ใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|max:100|unique:position',
            'position_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $this->positionService->createPosition($request->all());
            return redirect()->route('position.index')
                ->with('success', 'Position created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create position'])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไข Position
     */
    public function edit($id)
    {
        $position = $this->positionService->findPositionById($id);
//        $statuses = Enum::getDefaultStatusesDropdown();
        return view('superAdmin.position.edit', compact('position'));
    }

    /**
     * อัพเดท Position
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'position_name' => 'required|max:100|unique:position,position_name,' . $id . ',position_id',
            'position_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $position = $this->positionService->findPositionById($id);
            $this->positionService->updatePosition($position, $request->all());
            return redirect()->route('position.index')
                ->with('success', 'Position updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update position'])
                ->withInput();
        }
    }

    /**
     * เปลี่ยนสถานะ Position
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $position = $this->positionService->findPositionById($id);
            $this->positionService->changeStatus($position, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
