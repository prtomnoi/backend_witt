<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Enums\Enum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service สำหรับจัดการข้อมูล AuditLog
 */
class AuditLogService
{

    public function __construct()
    {
        // ไม่มีการ inject อะไร
    }

    /**
     * บันทึกข้อมูลการสร้างข้อมูลใหม่
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param array $data ข้อมูลที่สร้าง
     * @return AuditLog
     */
    public function logCreate(string $tableName, $recordId, array $data = []): AuditLog
    {
        return $this->createLog(
            $tableName,
            $recordId,
            Enum::AUDIT_ACTION_CREATE,
            null,
            null,
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * บันทึกข้อมูลการอัพเดทข้อมูล
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param array $oldData ข้อมูลเดิม
     * @param array $newData ข้อมูลใหม่
     * @return AuditLog[]
     */
    public function logUpdate(string $tableName, $recordId, array $oldData, array $newData): array
    {
        $logs = [];

        // เปรียบเทียบข้อมูลเดิมกับข้อมูลใหม่
        foreach ($newData as $field => $value) {
            // ถ้าฟิลด์นี้มีการเปลี่ยนแปลง
            if (isset($oldData[$field]) && $oldData[$field] !== $value) {
                $logs[] = $this->createLog(
                    $tableName,
                    $recordId,
                    Enum::AUDIT_ACTION_UPDATE,
                    $field,
                    $oldData[$field],
                    $value
                );
            }
        }

        return $logs;
    }

    /**
     * บันทึกข้อมูลการลบข้อมูล
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param array $data ข้อมูลที่ถูกลบ
     * @return AuditLog
     */
    public function logDelete(string $tableName, $recordId, array $data = []): AuditLog
    {
        return $this->createLog(
            $tableName,
            $recordId,
            Enum::AUDIT_ACTION_DELETE,
            null,
            json_encode($data, JSON_UNESCAPED_UNICODE),
            null
        );
    }

    /**
     * บันทึกข้อมูลการเปลี่ยนสถานะ
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param string $statusField ชื่อฟิลด์สถานะ
     * @param string $oldStatus สถานะเดิม
     * @param string $newStatus สถานะใหม่
     * @return AuditLog
     */
    public function logStatusChange(string $tableName, $recordId, string $statusField, string $oldStatus, string $newStatus): AuditLog
    {
        return $this->createLog(
            $tableName,
            $recordId,
            Enum::AUDIT_ACTION_STATUS_CHANGE,
            $statusField,
            $oldStatus,
            $newStatus
        );
    }

    /**
     * สร้างบันทึก Audit Log
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param string $action การกระทำ
     * @param string|null $fieldName ชื่อฟิลด์ที่เปลี่ยนแปลง
     * @param string|null $oldValue ค่าเดิม
     * @param string|null $newValue ค่าใหม่
     * @return AuditLog
     */
    protected function createLog(
        string  $tableName,
                $recordId,
        string  $action,
        ?string $fieldName = null,
                $oldValue = null,
                $newValue = null
    ): AuditLog
    {
        $userId = Auth::id() ?? 0;

        $log = new AuditLog();
        $log->table_name = $tableName;
        $log->record_id = (string)$recordId;
        $log->action = $action;
        $log->field_name = $fieldName;
        // $log->old_value = is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : $oldValue;
        // $log->new_value = is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : $newValue;
        $log->user_id = $userId;
        $log->ip_address = Request::ip();
        $log->user_agent = Request::userAgent();
        $log->save();

        return $log;
    }

    /**
     * ดึงข้อมูล Audit Log ทั้งหมดพร้อม pagination
     *
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return LengthAwarePaginator
     */
    public function getAllLogs(int $perPage = 10): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->orderBy('audit_created_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหา Audit Log ตาม ID
     *
     * @param int $id ID ของ Audit Log
     * @return AuditLog
     */
    public function findLogById(int $id): AuditLog
    {
        return AuditLog::with('user')->findOrFail($id);
    }

    /**
     * ค้นหา Audit Log ตามตารางและ ID ของข้อมูล
     *
     * @param string $tableName ชื่อตาราง
     * @param string|int $recordId ID ของข้อมูล
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return LengthAwarePaginator
     */
    public function findLogsByTableAndRecordId(string $tableName, $recordId, int $perPage = 10): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->where('table_name', $tableName)
            ->where('record_id', (string)$recordId)
            ->orderBy('audit_created_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหา Audit Log ตามผู้ใช้
     *
     * @param int $userId ID ของผู้ใช้
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return LengthAwarePaginator
     */
    public function findLogsByUserId(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->where('user_id', $userId)
            ->orderBy('audit_created_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหา Audit Log ตามการกระทำ
     *
     * @param string $action การกระทำ
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return LengthAwarePaginator
     */
    public function findLogsByAction(string $action, int $perPage = 10): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->where('action', $action)
            ->orderBy('audit_created_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * ค้นหา Audit Log ตามช่วงเวลา
     *
     * @param string $startDate วันที่เริ่มต้น (Y-m-d)
     * @param string $endDate วันที่สิ้นสุด (Y-m-d)
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return LengthAwarePaginator
     */
    public function findLogsByDateRange(string $startDate, string $endDate, int $perPage = 10): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->whereBetween('audit_created_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('audit_created_date', 'desc')
            ->paginate($perPage);
    }
}
