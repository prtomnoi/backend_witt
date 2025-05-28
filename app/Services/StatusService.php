<?php

namespace App\Services;

use App\Models\Status;
use App\Enums\Enum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StatusService
{
    /**
     * ดึงข้อมูล Status ทั้งหมดพร้อม pagination
     */
    public function getAllStatuses(int $perPage = 10): LengthAwarePaginator
    {
        return Status::paginate($perPage);
    }

    /**
     * ดึงข้อมูล Status ที่ active
     */
    public function getActiveStatuses(): Collection
    {
        return Status::where('status_status', Enum::DEFAULT_STATUS_A)->get();
    }

    /**
     * ค้นหา Status ตาม ID
     */
    public function findStatusById(int $id): Status
    {
        return Status::findOrFail($id);
    }

    /**
     * สร้าง Status ใหม่
     */
    public function createStatus(array $data): Status
    {
        $status = new Status();
        $status->status_name = $data['status_name'];
        $status->status_type = $data['status_type'];
        $status->status_status = $data['status_status'];
        $status->status_created_by = Auth::id();
        $status->status_updated_by = Auth::id();
        $status->save();

        return $status;
    }

    /**
     * อัพเดท Status
     */
    public function updateStatus(Status $status, array $data): Status
    {
        $status->status_name = $data['status_name'];
        $status->status_type = $data['status_type'];
        $status->status_status = $data['status_status'];
        $status->status_updated_by = Auth::id();
        $status->save();

        return $status;
    }

    /**
     * ลบ Status
     */
    public function deleteStatus(Status $status): bool
    {
        return $status->delete();
    }

    /**
     * เปลี่ยนสถานะ Status
     */
    public function changeStatus(Status $status, string $newStatus): Status
    {
        $status->status_status = $newStatus;
        $status->status_updated_by = Auth::id();
        $status->save();

        return $status;
    }

    /**
     * ดึงข้อมูล Status ตามประเภท
     */
    public function getStatusesByType(string $type): Collection
    {
        return Status::where('status_type', $type)
            ->where('status_status', Enum::DEFAULT_STATUS_A)
            ->get();
    }
}
