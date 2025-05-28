<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\RuleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class RuleController extends Controller
{
    protected $ruleService;

    public function __construct(RuleService $ruleService)
    {
        $this->ruleService = $ruleService;
    }

    /**
     * แสดงรายการ Rule ทั้งหมด
     */
    public function index()
    {
        $rules = $this->ruleService->getAllRules();
        return view('superAdmin.rule.index', compact('rules'));
    }

    /**
     * แสดงฟอร์มสร้าง Rule
     */
    public function create()
    {
        return view('superAdmin.rule.create');
    }

    /**
     * บันทึก Rule ใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|max:30|unique:rule',
            'rule_type' => 'required|in:A,O',
            'rule_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $this->ruleService->createRule($request->all());
            return redirect()->route('rule.index')
                ->with('success', 'Rule created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create rule'])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไข Rule
     */
    public function edit($id)
    {
        $rule = $this->ruleService->findRuleById($id);
        return view('superAdmin.rule.edit', compact('rule'));
    }

    /**
     * อัพเดท Rule
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rule_name' => 'required|max:30|unique:rule,rule_name,' . $id . ',rule_id',
            'rule_type' => 'required|in:A,O',
            'rule_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())],
        ]);

        try {
            $rule = $this->ruleService->findRuleById($id);
            $this->ruleService->updateRule($rule, $request->all());
            return redirect()->route('rule.index')
                ->with('success', 'Rule updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update rule'])
                ->withInput();
        }
    }

    /**
     * เปลี่ยนสถานะ Rule
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $rule = $this->ruleService->findRuleById($id);
            $this->ruleService->changeStatus($rule, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
