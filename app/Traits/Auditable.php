<?php

namespace App\Traits;

use App\Services\AuditLogService;
use App\Enums\Enum;
use Illuminate\Support\Facades\App;

/**
 * Trait สำหรับติดตามการเปลี่ยนแปลงของ Model
 */
trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable()
    {
        // บันทึกข้อมูลเมื่อสร้าง Model ใหม่
        static::created(function ($model) {
            $auditLogService = App::make(AuditLogService::class);
            $auditLogService->logCreate(
                $model->getTable(),
                $model->{$model->getKeyName()},
                $model->getAttributes()
            );
        });

        // บันทึกข้อมูลเมื่ออัพเดท Model
        static::updated(function ($model) {
            $auditLogService = App::make(AuditLogService::class);

            // ตรวจสอบว่ามีการเปลี่ยนแปลงสถานะหรือไม่
            $statusField = self::getStatusField();
            if ($statusField && $model->isDirty($statusField)) {
                $auditLogService->logStatusChange(
                    $model->getTable(),
                    $model->{$model->getKeyName()},
                    $statusField,
                    $model->getOriginal($statusField),
                    $model->getAttribute($statusField)
                );
            }

            // บันทึกการเปลี่ยนแปลงฟิลด์อื่นๆ
            $auditLogService->logUpdate(
                $model->getTable(),
                $model->{$model->getKeyName()},
                $model->getOriginal(),
                $model->getAttributes()
            );
        });

        // บันทึกข้อมูลเมื่อลบ Model
        static::deleted(function ($model) {
            $auditLogService = App::make(AuditLogService::class);
            $auditLogService->logDelete(
                $model->getTable(),
                $model->{$model->getKeyName()},
                $model->getAttributes()
            );
        });
    }

    /**
     * ระบุฟิลด์สถานะของ Model
     *
     * @return string|null
     */
    protected static function getStatusField()
    {
        // ตัวอย่างการระบุฟิลด์สถานะตามชื่อตาราง
        $statusFields = [
            'user' => 'user_status',
            'account' => 'account_status',
            'loan' => 'loan_status',
            'deposit' => 'deposit_flag',
            'annual_profit' => 'ap_status',
            'dividend_allocation' => 'da_status',
            'member_dividend' => 'md_status',
            'status' => 'status_status',
            'occupation' => 'occupation_status',
            'other_group_members' => 'ogm_status',
            'announce' => 'announce_status',
            'document_room' => 'dr_status',
            'meeting_doc' => 'md_status',
            'rule' => 'rule_status',
            'position' => 'position_status',
        ];

        $model = new static;
        $tableName = $model->getTable();

        return $statusFields[$tableName] ?? null;
    }
}
