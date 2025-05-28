<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\MeetingDoc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingDocService
{
    /**
     * ดึงข้อมูลเอกสารการประชุมทั้งหมดพร้อม pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllMeetingDocs(int $perPage = 10): LengthAwarePaginator
    {
        return MeetingDoc::orderBy('meeting_doc_id', 'desc')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเอกสารการประชุมที่มีสถานะใช้งาน
     *
     * @return Collection
     */
    public function getActiveMeetingDocs(): Collection
    {
        return MeetingDoc::where('meeting_doc_status', Enum::DEFAULT_STATUS_A)
            ->orderBy('meeting_doc_date', 'desc')
            ->get();
    }

    /**
     * ค้นหาเอกสารการประชุมตาม ID
     *
     * @param int $id
     * @return MeetingDoc
     */
    public function findMeetingDocById(int $id): MeetingDoc
    {
        return MeetingDoc::findOrFail($id);
    }

    /**
     * สร้างเอกสารการประชุมใหม่
     *
     * @param array $data
     * @return MeetingDoc
     */
    public function createMeetingDoc(array $data): MeetingDoc
    {
        DB::beginTransaction();
        try {
            $file = $data['meeting_doc_pic'];

            // ตรวจสอบขนาดไฟล์ (mediumblob max = 16MB)
            $maxSize = 16 * 1024 * 1024; // 16MB in bytes
            if ($file->getSize() > $maxSize) {
                throw new \Exception('File size exceeds maximum limit of 16MB');
            }

            // ตรวจสอบ mime type
            $allowedTypes = Enum::ALLOWED_FILE_TYPES;
            $mimeType = $file->getMimeType();

            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG and PDF are allowed');
            }

            $fileContent = file_get_contents($file->getRealPath());

            $meetingDoc = new MeetingDoc();
            $meetingDoc->meeting_doc_no = $data['meeting_doc_no'];
            $meetingDoc->meeting_doc_year = $data['meeting_doc_year'];
            $meetingDoc->meeting_doc_date = $data['meeting_doc_date'];
            $meetingDoc->meeting_doc_title = $data['meeting_doc_title'];
            $meetingDoc->meeting_doc_remark = $data['meeting_doc_remark'] ?? null;
            $meetingDoc->meeting_doc_pic = $fileContent;
            $meetingDoc->meeting_doc_pic_type = $mimeType;
            $meetingDoc->meeting_doc_status = $data['meeting_doc_status'];
            $meetingDoc->meeting_doc_created_by = Auth::id();
            $meetingDoc->meeting_doc_updated_by = Auth::id();
            $meetingDoc->save();

            DB::commit();
            return $meetingDoc;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * อัพเดทเอกสารการประชุม
     *
     * @param MeetingDoc $meetingDoc
     * @param array $data
     * @return MeetingDoc
     */
    public function updateMeetingDoc(MeetingDoc $meetingDoc, array $data): MeetingDoc
    {
        DB::beginTransaction();
        try {
            $meetingDoc->meeting_doc_no = $data['meeting_doc_no'];
            $meetingDoc->meeting_doc_year = $data['meeting_doc_year'];
            $meetingDoc->meeting_doc_date = $data['meeting_doc_date'];
            $meetingDoc->meeting_doc_title = $data['meeting_doc_title'];
            $meetingDoc->meeting_doc_remark = $data['meeting_doc_remark'] ?? null;

            if (isset($data['meeting_doc_pic'])) {
                $file = $data['meeting_doc_pic'];

                // ตรวจสอบขนาดไฟล์ (mediumblob max = 16MB)
                $maxSize = 16 * 1024 * 1024; // 16MB in bytes
                if ($file->getSize() > $maxSize) {
                    throw new \Exception('File size exceeds maximum limit of 16MB');
                }

                // ตรวจสอบ mime type
                $allowedTypes = Enum::ALLOWED_FILE_TYPES;
                $mimeType = $file->getMimeType();

                if (!in_array($mimeType, $allowedTypes)) {
                    throw new \Exception('Invalid file type. Only JPG, PNG and PDF are allowed');
                }

                $fileContent = file_get_contents($file->getRealPath());
                $meetingDoc->meeting_doc_pic = $fileContent;
                $meetingDoc->meeting_doc_pic_type = $mimeType;
            }

            $meetingDoc->meeting_doc_status = $data['meeting_doc_status'];
            $meetingDoc->meeting_doc_updated_by = Auth::id();
            $meetingDoc->save();

            DB::commit();
            return $meetingDoc;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * เปลี่ยนสถานะประชุม
     *
     * @param MeetingDoc $meetingDoc
     * @param string $status
     * @return MeetingDoc
     */
    public function changeStatus(MeetingDoc $meetingDoc, string $status): MeetingDoc
    {
        DB::beginTransaction();
        try {
            $meetingDoc->meeting_doc_status = $status;
            $meetingDoc->meeting_doc_updated_by = Auth::id();
            $meetingDoc->save();

            DB::commit();
            return $meetingDoc;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ดึงข้อมูลเอกสารการประชุมตามปี
     *
     * @param int $year
     * @return Collection
     */
    public function getMeetingDocsByYear(int $year): Collection
    {
        return MeetingDoc::where('meeting_doc_year', $year)
            ->orderBy('meeting_doc_date', 'desc')
            ->get();
    }

    /**
     * ค้นหาประชุมตามคำค้น
     *
     * @param string|null $search
     * @return Collection
     */
    public function searchMeetingDocs(?string $search): Collection
    {
        if (!empty($search) && !mb_check_encoding($search, 'UTF-8')) {
            $search = mb_convert_encoding($search, 'UTF-8', 'UTF-8');
        }

        return MeetingDoc::where('meeting_doc_status', Enum::DEFAULT_STATUS_A)
            ->where(function ($query) use ($search) {
                $query->where('meeting_doc_title', 'LIKE', "%{$search}%");
            })
            ->orderBy('meeting_doc_id', 'desc')
            ->get();
    }
    /**
     * Get meeting documents with dynamic filtering and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFilteredMeetingDocs(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = MeetingDoc::query();

        if (!empty($filters['year'])) {
            $query->where('meeting_doc_year', $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('meeting_doc_status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('meeting_doc_title', 'LIKE', '%' . $filters['search'] . '%');
        }


        $query->orderBy('meeting_doc_id', 'desc');
        return $query->paginate($perPage);
    }

}
