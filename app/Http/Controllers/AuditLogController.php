<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Http\Request;
use App\Enums\Enum;

/**
 * Controller สำหรับจัดการข้อมูล AuditLog
 */
class AuditLogController extends Controller
{
    protected $auditLogService;

    /**
     * สร้าง instance ของ AuditLogController
     *
     * @param AuditLogService $auditLogService
     */
    public function __construct(AuditLogService $auditLogService)
    {
        parent::__construct();
        $this->auditLogService = $auditLogService;
    }

    /**
     * แสดงรายการ Audit Log ทั้งหมด
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $logs = $this->auditLogService->getAllLogs($perPage);
        $auditActions = Enum::getAuditActionDropdown();

        return view('superAdmin.audit_log.index', compact('logs', 'auditActions'));
    }

    /**
     * แสดงรายละเอียดของ Audit Log
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $log = $this->auditLogService->findLogById($id);
        return view('superAdmin.audit_log.show', compact('log'));
    }

    /**
     * ค้นหา Audit Log ตามเงื่อนไข
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $logs = null;
        $auditActions = Enum::getAuditActionDropdown();

        // ค้นหาตามตารางและ ID ของข้อมูล
        if ($request->has('table_name') && $request->has('record_id')) {
            $logs = $this->auditLogService->findLogsByTableAndRecordId(
                $request->input('table_name'),
                $request->input('record_id'),
                $perPage
            );
        } // ค้นหาตามผู้ใช้
        elseif ($request->has('user_id')) {
            $logs = $this->auditLogService->findLogsByUserId(
                $request->input('user_id'),
                $perPage
            );
        } // ค้นหาตามการกระทำ
        elseif ($request->has('action')) {
            $logs = $this->auditLogService->findLogsByAction(
                $request->input('action'),
                $perPage
            );
        } // ค้นหาตามช่วงเวลา
        elseif ($request->has('start_date') && $request->has('end_date')) {
            $logs = $this->auditLogService->findLogsByDateRange(
                $request->input('start_date'),
                $request->input('end_date'),
                $perPage
            );
        } // ถ้าไม่มีเงื่อนไขการค้นหา ให้แสดงทั้งหมด
        else {
            $logs = $this->auditLogService->getAllLogs($perPage);
        }

        return view('superAdmin.audit_log.index', compact('logs', 'auditActions'));
    }

    /**
     * แสดงประวัติการเปลี่ยนแปลงของข้อมูล
     *
     * @param Request $request
     * @param string $tableName
     * @param string $recordId
     * @return \Illuminate\View\View
     */
    public function history(Request $request, $tableName, $recordId)
    {
        $perPage = $request->input('per_page', 10);
        $logs = $this->auditLogService->findLogsByTableAndRecordId($tableName, $recordId, $perPage);

        return view('superAdmin.audit_log.history', compact('logs', 'tableName', 'recordId'));
    }
}
