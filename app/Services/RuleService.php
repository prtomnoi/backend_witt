<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RuleService
{
    /**
     * ดึงข้อมูล Rule ทั้งหมดพร้อม pagination
     */
    public function getAllRules(int $perPage = 10): LengthAwarePaginator
    {
        return Rule::paginate($perPage);
    }

    /**
     * ดึงข้อมูล Rule ที่ active
     */
    public function getActiveRules(): Collection
    {
        return Rule::where('rule_status', Enum::DEFAULT_STATUS_A)->get();
    }

    /**
     * ค้นหา Rule ตาม ID
     */
    public function findRuleById(int $id): Rule
    {
        return Rule::findOrFail($id);
    }

    /**
     * สร้าง Rule ใหม่
     */
    public function createRule(array $data): Rule
    {
        $rule = new Rule();
        $rule->rule_name = $data['rule_name'];
        $rule->rule_type = $data['rule_type'];
        $rule->rule_status = $data['rule_status'];
        $rule->rule_created_by = Auth::id();
        $rule->rule_updated_by = Auth::id();
        $rule->save();

        return $rule;
    }

    /**
     * อัพเดท Rule
     */
    public function updateRule(Rule $rule, array $data): Rule
    {
        $rule->rule_name = $data['rule_name'];
        $rule->rule_type = $data['rule_type'];
        $rule->rule_status = $data['rule_status'];
        $rule->rule_updated_by = Auth::id();
        $rule->save();

        return $rule;
    }

    /**
     * ลบ Rule
     */
    public function deleteRule(Rule $rule): bool
    {
        return $rule->delete();
    }

    /**
     * เปลี่ยนสถานะ Rule
     */
    public function changeStatus(Rule $rule, string $status): Rule
    {
        $rule->rule_status = $status;
        $rule->rule_updated_by = Auth::id();
        $rule->save();

        return $rule;
    }
}
