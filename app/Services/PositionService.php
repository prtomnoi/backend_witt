<?php

namespace App\Services;

use App\Models\Position;
use App\Enums\Enum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PositionService
{
    /**
     * ดึงข้อมูล Position ทั้งหมดพร้อม pagination
     */
    public function getAllPositions(int $perPage = 10): LengthAwarePaginator
    {
        return Position::paginate($perPage);
    }

    /**
     * ดึงข้อมูล Position ที่ active
     */
    public function getActivePositions(): Collection
    {
        return Position::where('position_status', Enum::DEFAULT_STATUS_A)->get();
    }

    /**
     * ค้นหา Position ตาม ID
     */
    public function findPositionById(int $id): Position
    {
        return Position::findOrFail($id);
    }

    /**
     * สร้าง Position ใหม่
     */
    public function createPosition(array $data): Position
    {
        $position = new Position();
        $position->position_name = $data['position_name'];
        $position->position_status = $data['position_status'];
        $position->position_created_by = Auth::id();
        $position->position_updated_by = Auth::id();
        $position->save();

        return $position;
    }

    /**
     * อัพเดท Position
     */
    public function updatePosition(Position $position, array $data): Position
    {
        $position->position_name = $data['position_name'];
        $position->position_status = $data['position_status'];
        $position->position_updated_by = Auth::id();
        $position->save();

        return $position;
    }

    /**
     * ลบ Position
     */
    public function deletePosition(Position $position): bool
    {
        return $position->delete();
    }

    /**
     * เปลี่ยนสถานะ Position
     */
    public function changeStatus(Position $position, string $status): Position
    {
        $position->position_status = $status;
        $position->position_updated_by = Auth::id();
        $position->save();

        return $position;
    }
}
