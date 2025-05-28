<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Models\DocumentRoom;
use App\Services\DocumentRoomService;
use App\Services\AccountService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class DocumentRoomController
 * @package App\Http\Controllers
 */
class DocumentRoomController extends Controller
{
    /**
     * @var DocumentRoomService
     */
    protected $documentRoomService;

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * DocumentRoomController constructor.
     *
     * @param DocumentRoomService $documentRoomService
     * @param AccountService $accountService
     * @param UserService $userService
     */
    public function __construct(
        DocumentRoomService $documentRoomService,
        AccountService $accountService,
        UserService $userService
    ) {
        $this->documentRoomService = $documentRoomService;
        $this->accountService = $accountService;
        $this->userService = $userService;
    }

    /**
     * แสดงรายการเอกสารทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $documents = $this->documentRoomService->getAll(10);
        return view('admin.document_room.index', compact('documents'));
    }

    /**
     * แสดงฟอร์มสร้างเอกสารใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = $this->userService->getAllActive();
        $accounts = $this->accountService->getAllActive();
        $documentTypes = Enum::getDocumentRoomTypeDropdown();

        return view('admin.document_room.create', compact('users', 'accounts', 'documentTypes'));
    }

    /**
     * บันทึกข้อมูลเอกสารใหม่
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id',
            'account_id' => 'required|exists:account,account_id',
            'document_room_type' => 'required|in:' . implode(',', Enum::getDocumentRoomTypeForValidation()),
            'document_room_name' => 'nullable|string|max:800',
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // อัพโหลดไฟล์ถ้ามี
        $fileContent = null;
        $fileType = null;

        if ($request->hasFile('document_file')) {
            $fileData = $this->documentRoomService->uploadFile($request->file('document_file'));
            $fileContent = $fileData['content'];
            $fileType = $fileData['type'];
        }

        // สร้างข้อมูลเอกสาร
        $userId = Auth::id() ?? 1; // ถ้าไม่มี Auth ให้ใช้ ID 1 แทน

        $this->documentRoomService->create([
            'user_id' => $request->user_id,
            'account_id' => $request->account_id,
            'document_room_type' => $request->document_room_type,
            'document_room_name' => $request->document_room_name,
            'document_room_pic' => $fileContent,
            'document_room_type_pic' => $fileType,
            'document_room_created_by' => $userId,
            'document_room_updated_by' => $userId,
        ]);

      return redirect()->back()->with('success', 'อัปโหลดเอกสารสำเร็จแล้ว');
    }

    /**
     * แสดงรายละเอียดเอกสาร
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $data = $this->documentRoomService->getByIdWithFileData($id);

        if (!$data['documentRoom']) {
            return redirect()->route('document-room.index')
                ->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        return view('admin.document_room.show', [
            'documentRoom' => $data['documentRoom'],
            'fileData' => $data['fileData']
        ]);
    }

    /**
     * แสดงฟอร์มแก้ไขเอกสาร
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $data = $this->documentRoomService->getByIdWithFileData($id);

        if (!$data['documentRoom']) {
            return redirect()->route('document-room.index')
                ->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        $users = $this->userService->getAllActive();
        $accounts = $this->accountService->getAllActive();
        $documentTypes = Enum::getDocumentRoomTypeDropdown();

        return view('admin.document_room.edit', [
            'documentRoom' => $data['documentRoom'],
            'fileData' => $data['fileData'],
            'users' => $users,
            'accounts' => $accounts,
            'documentTypes' => $documentTypes
        ]);
    }

    /**
     * อัพเดตข้อมูลเอกสาร
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $documentRoom = $this->documentRoomService->getById($id);

        if (!$documentRoom) {
            return redirect()->route('document-room.index')
                ->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id',
            'account_id' => 'required|exists:account,account_id',
            'document_room_type' => 'required|in:' . implode(',', Enum::getDocumentRoomTypeForValidation()),
            'document_room_name' => 'nullable|string|max:800',
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ข้อมูลที่จะอัพเดต
        $updateData = [
            'user_id' => $request->user_id,
            'account_id' => $request->account_id,
            'document_room_type' => $request->document_room_type,
            'document_room_name' => $request->document_room_name,
            'document_room_updated_by' => Auth::id() ?? 1,
            'document_room_updated_date' => now(),
        ];

        // อัพโหลดไฟล์ใหม่ถ้ามี
        if ($request->hasFile('document_file')) {
            $fileData = $this->documentRoomService->uploadFile($request->file('document_file'));
            $updateData['document_room_pic'] = $fileData['content'];
            $updateData['document_room_type_pic'] = $fileData['type'];
        }

        // อัพเดตข้อมูล
        $this->documentRoomService->update($documentRoom, $updateData);

        return redirect()->route('document-room.index')
            ->with('success', 'อัพเดตเอกสารเรียบร้อยแล้ว');
    }

    /**
     * ลบเอกสาร
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $documentRoom = $this->documentRoomService->getById($id);

        if (!$documentRoom) {
            return redirect()->route('document-room.index')
                ->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        $this->documentRoomService->delete($documentRoom);

        return redirect()->route('document-room.index')
            ->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายการเอกสารตามผู้ใช้งาน
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function getByUser($userId)
    {
        $documents = $this->documentRoomService->getByUserId($userId, 10);
        $user = $this->userService->getById($userId);

        return view('admin.document_room.by_user', compact('documents', 'user'));
    }

    /**
     * แสดงรายการเอกสารตามบัญชี
     *
     * @param int $accountId
     * @return \Illuminate\View\View
     */
    public function getByAccount($accountId)
    {
        $documents = $this->documentRoomService->getByAccountId($accountId, 10);
        $account = $this->accountService->getById($accountId);

        return view('admin.document_room.by_account', compact('documents', 'account'));
    }

    /**
     * ดาวน์โหลดไฟล์เอกสาร
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        $documentRoom = $this->documentRoomService->getById($id);

        if (!$documentRoom) {
            return back()->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        // ถอดรหัสไฟล์เอกสาร
        $fileData = $this->documentRoomService->getFileForDownload(
            $documentRoom->document_room_pic,
            $documentRoom->document_room_type_pic
        );

        if (!$fileData['content']) {
            return back()->with('error', 'ไม่พบไฟล์เอกสาร');
        }

        // สร้างชื่อไฟล์
        $filename = 'document_' . $documentRoom->document_room_id . '.' . $documentRoom->document_room_type_pic;

        // ส่งไฟล์ให้ผู้ใช้ดาวน์โหลด
        return Response::make($fileData['content'], 200, [
            'Content-Type' => $fileData['type'],
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * แสดงไฟล์เอกสาร
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function view($id)
    {
        $documentRoom = $this->documentRoomService->getById($id);

        if (!$documentRoom) {
            return back()->with('error', 'ไม่พบข้อมูลเอกสาร');
        }

        // ถอดรหัสไฟล์เอกสาร
        $fileData = $this->documentRoomService->getFileForDownload(
            $documentRoom->document_room_pic,
            $documentRoom->document_room_type_pic
        );

        if (!$fileData['content']) {
            return back()->with('error', 'ไม่พบไฟล์เอกสาร');
        }

        // ส่งไฟล์ให้แสดงในเบราว์เซอร์
        return Response::make($fileData['content'], 200, [
            'Content-Type' => $fileData['type'],
            'Content-Disposition' => 'inline; filename="document.' . $documentRoom->document_room_type_pic . '"',
        ]);
    }
}
