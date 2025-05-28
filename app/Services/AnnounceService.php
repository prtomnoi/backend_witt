<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\Announce;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnounceService
{
    /**
     * ดึงข้อมูลประกาศทั้งหมดพร้อม pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllAnnounces(int $perPage = 10): LengthAwarePaginator
    {
        return Announce::orderBy('announce_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลประกาศตาม meeting_doc_id พร้อม pagination
     *
     * @param int $meetingDocId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAnnouncesByMeetingDoc(int $meetingDocId, int $perPage = 10): LengthAwarePaginator
    {
        return Announce::where('meeting_doc_id', $meetingDocId)
            ->orderBy('announce_id', 'desc')
            ->paginate($perPage);
    }


    /**
     * ดึงข้อมูลประกาศที่มีสถานะใช้งาน
     *
     * @return Collection
     */
    public function getActiveAnnounces(): Collection
    {
        return Announce::where('announce_status', Enum::DEFAULT_STATUS_A)
            ->orderBy('announce_id', 'desc')
            ->get();
    }

    /**
     * ค้นหาประกาศตาม ID
     *
     * @param int $id
     * @return Announce
     */
    public function findAnnounceById(int $id): Announce
    {
        return Announce::findOrFail($id);
    }

    /**
     * สร้างประกาศใหม่
     *
     * @param array $data
     * @return Announce
     */
    public function createAnnounce(array $data): Announce
    {
        DB::beginTransaction();
        try {
            $announce = new Announce();
            // $announce->meeting_doc_id = $data['meeting_doc_id'];
            $announce->announce_no = $data['announce_no'];
            $announce->announce_year = $data['announce_year'];
            $announce->announce_start_date = $data['announce_start_date'];
            $announce->announce_end_date = $data['announce_end_date'];
            $announce->announce_title = $data['announce_title'];
            $announce->announce_type = $data['announce_type'];

            if (isset($data['announce_pic']) && $data['announce_pic']) {
                // ตรวจสอบไฟล์
                if (!$data['announce_pic']->isValid()) {
                    throw new \Exception('Invalid file upload');
                }

                // ตรวจสอบประเภทไฟล์
                $allowedTypes = Enum::ALLOWED_FILE_TYPES;
                $mimeType = $data['announce_pic']->getMimeType();
                if (!in_array($mimeType, $allowedTypes)) {
                    throw new \Exception('Invalid file type. Only JPG, PNG and PDF are allowed');
                }

                // เก็บข้อมูลไฟล์
                $announce->announce_pic = file_get_contents($data['announce_pic']->getRealPath());
                $announce->announce_pic_type = $mimeType; // เก็บ mime type
            }

            $announce->announce_status = $data['announce_status'];
            $announce->announce_created_by = Auth::id();
            $announce->announce_updated_by = Auth::id();
            $announce->save();

            DB::commit();
            return $announce;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
}

    /**
     * อัพเดทประกาศ
     *
     * @param Announce $announce
     * @param array $data
     * @return Announce
     */
    public function updateAnnounce(Announce $announce, array $data): Announce
    {
        DB::beginTransaction();
        try {
            $announce->announce_title = $data['announce_title'];
            $announce->meeting_doc_id = $data['meeting_doc_id'];
            // $announce->announce_detail = $data['announce_detail'];
            $announce->announce_status = $data['announce_status'];
            $announce->announce_updated_by = Auth::id();
            $announce->save();

            DB::commit();
            return $announce;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * เปลี่ยนสถานะประกาศ
     *
     * @param Announce $announce
     * @param string $status
     * @return Announce
     */
    public function changeStatus(Announce $announce, string $status): Announce
    {
        DB::beginTransaction();
        try {
            $announce->announce_status = $status;
            $announce->announce_updated_by = Auth::id();
            $announce->save();

            DB::commit();
            return $announce;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูลประกาศตามสถานะ
     *
     * @param string $status
     * @return Collection
     */
    public function getAnnouncesByStatus(string $status): Collection
    {
        return Announce::where('announce_status', $status)
            ->orderBy('announce_id', 'desc')
            ->get();
    }

    /**
     * ค้นหาประกาศตามคำค้น
     *
     * @param string|null $search
     * @return Collection
     */
    public function searchAnnounces(?string $search): Collection
    {
        if (!empty($search) && !mb_check_encoding($search, 'UTF-8')) {
            $search = mb_convert_encoding($search, 'UTF-8', 'UTF-8');
        }

        return Announce::where('announce_status', Enum::DEFAULT_STATUS_A)
            ->where(function ($query) use ($search) {
                $query->where('announce_title', 'LIKE', "%{$search}%")
                    ->orWhere('announce_detail', 'LIKE', "%{$search}%");
            })
            ->orderBy('announce_id', 'desc')
            ->get();
    }
}
