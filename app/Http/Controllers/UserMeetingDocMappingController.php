<?php

namespace App\Http\Controllers;

use App\Services\AnnounceService;
use App\Services\UserMeetingDocMappingService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserMeetingDocMappingController extends Controller
{

    protected $userMeetingDocMappingService;
    protected $userService;
    protected $announceService;

    public function __construct(
        UserMeetingDocMappingService $userMeetingDocMappingService,
        UserService                  $userService,
        AnnounceService              $announceService
    )
    {
        $this->userMeetingDocMappingService = $userMeetingDocMappingService;
        $this->userService = $userService;
        $this->announceService = $announceService;
    }

    // public function index()
    // {
    //     $mappings = userMeetingDocMapping::with(['user', 'announce'])
    //         ->orderBy('umdm_id', 'desc')
    //         ->paginate(10);
    //     return view('superAdmin.user-announce-mapping.index', compact('mappings'));
    // }

    public function index()
    {
        $mappings = $this->userMeetingDocMappingService->getAllMappings();
        return view('superAdmin.user-announce-mapping.index', compact('mappings'));
    }

    // public function create()
    // {
    //     $users = User::where('user_status', Enum::USER_STATUS_A)->get();
    //     $announces = Announce::where('announce_status', Enum::DEFAULT_STATUS_A)->get();
    //     return view('superAdmin.user-announce-mapping.create', compact('users', 'announces'));
    // }

    public function create()
    {
        $users = $this->userService->getActiveUsers();
        $announces = $this->announceService->getActiveAnnounces();
        return view('superAdmin.user-announce-mapping.create',
            compact('users', 'announces'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:user,user_id',
    //         'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
    //     ]);

    //     $mapping = new userMeetingDocMapping();
    //     $mapping->user_id = $request->user_id;
    //     $mapping->meeting_doc_id = $request->meeting_doc_id;
    //     $mapping->umdm_created_by = auth()->id();
    //     $mapping->umdm_created_date = now();
    //     $mapping->save();

    //     return redirect()->route('user-announce-mapping.index')
    //         ->with('success', 'เพิ่มข้อมูลการประกาศสำเร็จ');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
        ]);

        try {
            $this->userMeetingDocMappingService->createMapping($request->all());
            return redirect()->route('user-announce-mapping.index')
                ->with('success', 'เพิ่มข้อมูลการประกาศสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create mapping'])
                ->withInput();
        }
    }

    // public function edit($id)
    // {
    //     $mapping = userMeetingDocMapping::findOrFail($id);
    //     $users = User::where('user_status', Enum::USER_STATUS_A)->get();
    //     $announces = Announce::where('announce_status', Enum::DEFAULT_STATUS_A)->get();
    //     return view('superAdmin.user-announce-mapping.edit',
    //         compact('mapping', 'users', 'announces'));
    // }

    public function edit($id)
    {
        $mapping = $this->userMeetingDocMappingService->findMappingById($id);
        $users = $this->userService->getActiveUsers();
        $announces = $this->announceService->getActiveAnnounces();
        return view('superAdmin.user-announce-mapping.edit',
            compact('mapping', 'users', 'announces'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:user,user_id',
    //         'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
    //     ]);

    //     $mapping = userMeetingDocMapping::findOrFail($id);
    //     $mapping->user_id = $request->user_id;
    //     $mapping->meeting_doc_id = $request->meeting_doc_id;
    //     $mapping->save();

    //     return redirect()->route('user-announce-mapping.index')
    //         ->with('success', 'อัพเดตข้อมูลการประกาศสำเร็จ');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
        ]);

        try {
            $mapping = $this->userMeetingDocMappingService->findMappingById($id);
            $this->userMeetingDocMappingService->updateMapping($mapping, $request->all());
            return redirect()->route('user-announce-mapping.index')
                ->with('success', 'อัพเดตข้อมูลการประกาศสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update mapping'])
                ->withInput();
        }
    }

    // public function destroy($id)
    // {
    //     $mapping = userMeetingDocMapping::findOrFail($id);
    //     $mapping->delete();

    //     return redirect()->route('user-announce-mapping.index')
    //         ->with('success', 'ลบข้อมูลการประกาศสำเร็จ');
    // }

    public function destroy($id)
    {
        try {
            $mapping = $this->userMeetingDocMappingService->findMappingById($id);
            $this->userMeetingDocMappingService->deleteMapping($mapping);
            return redirect()->route('user-announce-mapping.index')
                ->with('success', 'ลบข้อมูลการประกาศสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete mapping']);
        }
    }
}
