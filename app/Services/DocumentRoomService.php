<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\DocumentRoom;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Class DocumentRoomService
 * @package App\Services
 */
class DocumentRoomService
{
    /**
     * สร้างข้อมูลเอกสาร
     *
     * @param array $data ข้อมูลเอกสาร
     * @return DocumentRoom
     */
    public function create(array $data): DocumentRoom
    {
        // กำหนดค่าเริ่มต้นสำหรับวันที่สร้างและอัพเดต
        $now = now();

        return DocumentRoom::create([
            'user_id' => $data['user_id'],
            'account_id' => $data['account_id'],
            'document_room_type' => $data['document_room_type'] ?? Enum::DOCUMENT_ROOM_TYPE_G,
            'document_room_name' => $data['document_room_name'] ?? null,
            'document_room_pic' => $data['document_room_pic'] ?? null,
            'document_room_type_pic' => $data['document_room_type_pic'] ?? null,
            'document_room_created_by' => $data['document_room_created_by'],
            'document_room_created_date' => $data['document_room_created_date'] ?? $now,
            'document_room_updated_by' => $data['document_room_updated_by'],
            'document_room_updated_date' => $data['document_room_updated_date'] ?? $now,
        ]);
    }

    /**
     * อัพเดตข้อมูลเอกสาร
     *
     * @param DocumentRoom $documentRoom เอกสารที่ต้องการอัพเดต
     * @param array $data ข้อมูลที่ต้องการอัพเดต
     * @return DocumentRoom
     */
    public function update(DocumentRoom $documentRoom, array $data): DocumentRoom
    {
        // อัพเดตเฉพาะข้อมูลที่ส่งมา
        $documentRoom->update($data);

        return $documentRoom;
    }

    /**
     * ลบข้อมูลเอกสาร
     *
     * @param DocumentRoom $documentRoom เอกสารที่ต้องการลบ
     * @return bool|null
     */
    public function delete(DocumentRoom $documentRoom): ?bool
    {
        return $documentRoom->delete();
    }

    /**
     * ดึงข้อมูลเอกสารทั้งหมด
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15)
    {
        return DocumentRoom::with(['user', 'account', 'createdBy', 'updatedBy'])
            ->orderByDesc('document_room_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเอกสารตามผู้ใช้งาน
     *
     * @param int $userId รหัสผู้ใช้งาน
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByUserId(int $userId, int $perPage = 15)
    {
        return DocumentRoom::with(['account', 'createdBy', 'updatedBy'])
            ->where('user_id', $userId)
            ->orderByDesc('document_room_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเอกสารตามบัญชี
     *
     * @param int $accountId รหัสบัญชี
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByAccountId(int $accountId, int $perPage = 15)
    {
        return DocumentRoom::with(['user', 'createdBy', 'updatedBy'])
            ->where('account_id', $accountId)
            ->orderByDesc('document_room_created_date')
            ->paginate($perPage);
    }

    /**
     * ดึงข้อมูลเอกสารตามประเภท
     *
     * @param string $type ประเภทเอกสาร
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByType(string $type, int $perPage = 15)
    {
        return DocumentRoom::with(['user', 'account', 'createdBy', 'updatedBy'])
            ->where('document_room_type', $type)
            ->orderByDesc('document_room_created_date')
            ->paginate($perPage);
    }

    /**
     * อัพโหลดไฟล์เอกสารเป็นไบนารีและเข้ารหัส
     * 1. แตกไฟล์เป็นไบนารี่
     * 2. เข้ารหัสด้วย APP_KEY
     * 3. แปลงเป็น text เพื่อบันทึกลง DB
     *
     * @param mixed $file ไฟล์ที่ต้องการอัพโหลด
     * @return array ข้อมูลไฟล์ที่อัพโหลด [content, type]
     */
    public function uploadFile($file): array
    {
        // ถ้าเป็นไฟล์จาก request
        if (is_object($file) && method_exists($file, 'getClientOriginalExtension')) {
            // 1. แตกไฟล์เป็นไบนารี่
            $fileType = $file->getClientOriginalExtension();
            $binaryContent = file_get_contents($file->getRealPath());

            // 2. เข้ารหัสด้วย APP_KEY
            $encryptedContent = Crypt::encrypt($binaryContent);

            // 3. แปลงเป็น text เพื่อบันทึกลง DB (ไม่ต้องทำอะไรเพิ่มเพราะ encrypt ให้เป็น string แล้ว)

            return [
                'content' => $encryptedContent, // เป็น string ที่พร้อมบันทึกลง DB
                'type' => $fileType
            ];
        }

        // ถ้าเป็น base64 string
        if (is_string($file) && Str::startsWith($file, 'data:')) {
            // แยกประเภทไฟล์และเนื้อหา
            $parts = explode(';base64,', $file);
            $fileType = explode('/', $parts[0])[1];

            // 1. แตกไฟล์เป็นไบนารี่
            $binaryContent = base64_decode($parts[1]);

            // 2. เข้ารหัสด้วย APP_KEY
            $encryptedContent = Crypt::encrypt($binaryContent);

            // 3. แปลงเป็น text เพื่อบันทึกลง DB (ไม่ต้องทำอะไรเพิ่มเพราะ encrypt ให้เป็น string แล้ว)

            return [
                'content' => $encryptedContent, // เป็น string ที่พร้อมบันทึกลง DB
                'type' => $fileType
            ];
        }

        // กรณีอื่นๆ
        return [
            'content' => null,
            'type' => null
        ];
    }

    /**
     * ถอดรหัสและแปลงไฟล์เอกสารเป็น base64 สำหรับแสดงผล
     * ทำย้อนขั้นตอนจาก uploadFile
     *
     * @param string|null $encryptedContent เนื้อหาไฟล์ที่เข้ารหัส
     * @param string|null $fileType ประเภทไฟล์
     * @return string|null
     */
    public function getFileForDisplay(?string $encryptedContent, ?string $fileType): ?string
    {
        if (!$encryptedContent || !$fileType) {
            return null;
        }

        try {
            // 1. ถอดรหัสจาก text เป็นไบนารี่
            $binaryContent = Crypt::decrypt($encryptedContent);

            // 2. แปลงไบนารี่เป็น base64 สำหรับแสดงผล
            $base64Content = base64_encode($binaryContent);

            // 3. กำหนด MIME type ตามประเภทไฟล์
            $mimeType = $this->getMimeTypeFromExtension($fileType);

            // 4. สร้าง data URL
            return "data:{$mimeType};base64,{$base64Content}";
        } catch (\Exception $e) {
            // กรณีถอดรหัสไม่สำเร็จ
            return null;
        }
    }

    /**
     * ถอดรหัสและส่งไฟล์เอกสารสำหรับดาวน์โหลด
     * ทำย้อนขั้นตอนจาก uploadFile
     *
     * @param string|null $encryptedContent เนื้อหาไฟล์ที่เข้ารหัส
     * @param string|null $fileType ประเภทไฟล์
     * @return array [content, type]
     */
    public function getFileForDownload(?string $encryptedContent, ?string $fileType): array
    {
        if (!$encryptedContent || !$fileType) {
            return [
                'content' => null,
                'type' => null
            ];
        }

        try {
            // 1. ถอดรหัสจาก text เป็นไบนารี่
            $binaryContent = Crypt::decrypt($encryptedContent);

            // 2. กำหนด MIME type ตามประเภทไฟล์
            $mimeType = $this->getMimeTypeFromExtension($fileType);

            return [
                'content' => $binaryContent,
                'type' => $mimeType
            ];
        } catch (\Exception $e) {
            // กรณีถอดรหัสไม่สำเร็จ
            return [
                'content' => null,
                'type' => null
            ];
        }
    }

    /**
     * แปลงนามสกุลไฟล์เป็น MIME type
     *
     * @param string $extension นามสกุลไฟล์
     * @return string
     */
    private function getMimeTypeFromExtension(string $extension): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * ดึงข้อมูลเอกสารตาม ID
     *
     * @param int $id รหัสเอกสาร
     * @return DocumentRoom|null
     */
    public function getById(int $id): ?DocumentRoom
    {
        return DocumentRoom::with(['user', 'account', 'createdBy', 'updatedBy'])
            ->find($id);
    }

    /**
     * ดึงข้อมูลเอกสารพร้อมแปลงไฟล์เป็น base64 สำหรับแสดงผล
     *
     * @param int $id รหัสเอกสาร
     * @return array [documentRoom, fileData]
     */
    public function getByIdWithFileData(int $id): array
    {
        $documentRoom = $this->getById($id);

        if (!$documentRoom) {
            return [
                'documentRoom' => null,
                'fileData' => null
            ];
        }

        $fileData = $this->getFileForDisplay(
            $documentRoom->document_room_pic,
            $documentRoom->document_room_type_pic
        );

        return [
            'documentRoom' => $documentRoom,
            'fileData' => $fileData
        ];
    }
}
