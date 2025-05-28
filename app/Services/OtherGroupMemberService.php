<?php

namespace App\Services;

use App\Models\OtherGroupMember;
use App\Enums\Enum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OtherGroupMemberService
{
    /**
     * ดึงข้อมูลสมาชิกกลุ่มอื่นทั้งหมดพร้อม pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllMembers(int $perPage = 10): LengthAwarePaginator
    {
        return OtherGroupMember::with(['user'])
            ->orderBy('ogm_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหาสมาชิกกลุ่มอื่นตาม ID
     *
     * @param int $id
     * @return OtherGroupMember
     */
    public function findMemberById(int $id): OtherGroupMember
    {
        return OtherGroupMember::with(['user'])->findOrFail($id);
    }

    /**
     * สร้างสมาชิกกลุ่มอื่นใหม่
     *
     * @param array $data
     * @return OtherGroupMember
     */
    public function createMember(array $data): OtherGroupMember
    {
        DB::beginTransaction();
        try {
            $member = new OtherGroupMember();
            $member->user_id = $data['user_id'];
            $member->ogm_name = $data['ogm_name'];
            $member->ogm_status = $data['ogm_status'];
            $member->ogm_created_by = Auth::id();
            $member->ogm_updated_by = Auth::id();
            $member->save();

            DB::commit();
            return $member;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * อัพเดทสมาชิกกลุ่มอื่น
     *
     * @param OtherGroupMember $member
     * @param array $data
     * @return OtherGroupMember
     */
    public function updateMember(OtherGroupMember $member, array $data): OtherGroupMember
    {
        DB::beginTransaction();
        try {
            $member->user_id = $data['user_id'];
            $member->ogm_name = $data['ogm_name'];
            $member->ogm_status = $data['ogm_status'];
            $member->ogm_updated_by = Auth::id();
            $member->save();

            DB::commit();
            return $member;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูลสมาชิกกลุ่มอื่นตามผู้ใช้
     *
     * @param int $userId
     * @return Collection
     */
    public function getMembersByUser(int $userId): Collection
    {
        return OtherGroupMember::where('user_id', $userId)
            ->orderBy('ogm_id', 'desc')
            ->get();
    }

    /**
     * ดึงข้อมูลสมาชิกกลุ่มอื่นที่มีสถานะใช้งาน
     *
     * @return Collection
     */
    public function getActiveMembers(): Collection
    {
        return OtherGroupMember::where('ogm_status', Enum::DEFAULT_STATUS_A)
            ->with(['user'])
            ->orderBy('ogm_id', 'desc')
            ->get();
    }

    /**
     * เปลี่ยนสถานะสมาชิกกลุ่มอื่น
     *
     * @param OtherGroupMember $member
     * @param string $status
     * @return OtherGroupMember
     */
    public function changeMemberStatus(OtherGroupMember $member, string $status): OtherGroupMember
    {
        DB::beginTransaction();
        try {
            $member->ogm_status = $status;
            $member->ogm_updated_by = Auth::id();
            $member->save();

            DB::commit();
            return $member;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
