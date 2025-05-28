<?php

namespace App\Services;

use App\Models\UserMeetingDocMapping;
use App\Enums\Enum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserMeetingDocMappingService
{
    /**
     * ดึงข้อมูลการประกาศทั้งหมดพร้อม pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllMappings(int $perPage = 10): LengthAwarePaginator
    {
        return UserMeetingDocMapping::with(['user', 'meeting_doc'])
            ->orderBy('umdm_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหาการประกาศตาม ID
     *
     * @param int $id
     * @return UserMeetingDocMapping
     */
    public function findMappingById(int $id): UserMeetingDocMapping
    {
        return UserMeetingDocMapping::with(['user', 'meeting_doc'])
            ->findOrFail($id);
    }

    /**
     * สร้างการประกาศใหม่
     *
     * @param array $data
     * @param string $status
     * @return UserMeetingDocMapping
     */
    public function createMapping(array $data, string $status): UserMeetingDocMapping
    {
        DB::beginTransaction();
        try {
            $mapping = new UserMeetingDocMapping();
            $mapping->user_id = $data['user_id'];
            $mapping->meeting_doc_id = $data['meeting_doc_id'];
            $mapping->umdm_type = $status;
            $mapping->umdm_created_by = Auth::id();
            $mapping->umdm_created_date = now();
            $mapping->save();

            DB::commit();
            return $mapping;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * อัพเดทการประกาศ
     *
     * @param UserMeetingDocMapping $mapping
     * @param array $data
     * @return UserMeetingDocMapping
     */
    public function updateMapping(UserMeetingDocMapping $mapping, array $data): UserMeetingDocMapping
    {
        DB::beginTransaction();
        try {
            $mapping->user_id = $data['user_id'];
            $mapping->meeting_doc_id = $data['meeting_doc_id'];
            $mapping->save();

            DB::commit();
            return $mapping;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ลบการประกาศ
     *
     * @param UserMeetingDocMapping $mapping
     * @return bool
     */
    public function deleteMapping(UserMeetingDocMapping $mapping): bool
    {
        DB::beginTransaction();
        try {
            $result = $mapping->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูลการประกาศตามผู้ใช้
     *
     * @param int $userId
     * @return Collection
     */
    public function getMappingsByUser(int $userId): Collection
    {
        return UserMeetingDocMapping::where('user_id', $userId)
            ->with(['meeting_doc'])
            ->orderBy('umdm_created_date', 'desc')
            ->get();
    }

    /**
     * ดึงข้อมูลการประกาศตามประกาศ
     *
     * @param int $d
     * @return Collection
     */
    public function getMappingsByAnnounce(int $meetingDocId): Collection
    {
        return UserMeetingDocMapping::where('meeting_doc_id', $meetingDocId)
            ->with(['user'])
            ->orderBy('umdm_created_date', 'desc')
            ->get();
    }
}
