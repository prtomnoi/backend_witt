<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\AnnounceService;
use App\Services\MeetingDocService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class AnnounceController extends Controller
{

    protected $announceService;
    protected $meetingDocService;

    public function __construct(
        AnnounceService   $announceService,
        MeetingDocService $meetingDocService
    )
    {
        $this->announceService = $announceService;
        $this->meetingDocService = $meetingDocService;
    }

    // public function create(Request $request)
    // {
    //     $meeting = MeetingDoc::findOrFail($request->meeting_id);
    //     $announceTypes = Enum::getAnnounceTypeDropdown();
    //     $statuses = Enum::getDefaultStatusDropdown();

    //     return view('superAdmin.meeting.announce-create', compact('meeting', 'announceTypes', 'statuses'));
    // }
     /**
     * แสดงประกาศ
     */
    public function index(Request $request)
    {
  
        try {
            $announces = $this->announceService->getAllAnnounces(); 
            return view('superAdmin.meeting.announce-index', compact('announces'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to retrieve announcements.']);
        }
    }
    
    
    

    /**
     * แสดงฟอร์มสร้างประกาศ
     */
    public function create(Request $request)
    {
        $announceTypes = Enum::getAnnounceTypeDropdown();
        $statuses = Enum::getDefaultStatusDropdown();

        return view('superAdmin.meeting.announce-create',
            compact('announceTypes', 'statuses'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'meeting_doc_id' => 'required|exists:meeting_doc,meeting_doc_id',
    //         'announce_no' => 'required|max:10',
    //         'announce_year' => 'required|max:4',
    //         'announce_start_date' => 'required|date',
    //         'announce_end_date' => 'required|date|after_or_equal:announce_start_date',
    //         'announce_title' => 'required|max:800',
    //         'announce_type' => ['required', Rule::in(Enum::getAnnounceTypeForValidation())],
    //         'announce_pic' => 'required|file|mimes:pdf,jpg,jpeg,png',
    //         'announce_status' => ['required', Rule::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $file = $request->file('announce_pic');
    //     $fileContent = file_get_contents($file->getRealPath());

    //     $announce = new Announce();
    //     $announce->meeting_doc_id = $request->meeting_doc_id;
    //     $announce->announce_no = $request->announce_no;
    //     $announce->announce_year = $request->announce_year;
    //     $announce->announce_start_date = $request->announce_start_date;
    //     $announce->announce_end_date = $request->announce_end_date;
    //     $announce->announce_title = $request->announce_title;
    //     $announce->announce_remark = $request->announce_remark;
    //     $announce->announce_type = $request->announce_type;
    //     $announce->announce_pic = $fileContent;
    //     $announce->announce_status = $request->announce_status;
    //     $announce->announce_created_by = auth()->id();
    //     $announce->announce_updated_by = auth()->id();
    //     $announce->save();

    //     return redirect()->route('meeting.index')->with('success', 'สร้างประกาศสำเร็จ');
    // }

    /**
     * บันทึกประกาศใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'meeting_doc_id' => 'nullable',
            'announce_no' => 'required|max:10',
            'announce_year' => 'required|max:4',
            'announce_start_date' => 'required|date',
            'announce_end_date' => 'required|date|after_or_equal:announce_start_date',
            'announce_title' => 'required|max:800',
            'announce_type' => ['required', RuleValidation::in(Enum::getAnnounceTypeForValidation())],
            'announce_pic' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'announce_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $this->announceService->createAnnounce($request->all());
            return redirect()->route('announce.index')
                ->with('success', 'สร้างประกาศสำเร็จ');
        } catch (\Exception $e) {
          
            return back()->withErrors(['error' => 'Failed to create announce'])
                ->withInput();
        }
    }

    // public function edit($id)
    // {
    //     $announce = Announce::with('meetingDoc')->findOrFail($id);
    //     $announceTypes = Enum::getAnnounceTypeDropdown();
    //     $statuses = Enum::getDefaultStatusDropdown();

    //     return view('superAdmin.meeting.announce-edit', compact('announce', 'announceTypes', 'statuses'));
    // }

    /**
     * แสดงฟอร์มแก้ไขประกาศ
     */
    public function edit($id)
    {
        $announce = $this->announceService->findAnnounceById($id);
        $announceTypes = Enum::getAnnounceTypeDropdown();
        $statuses = Enum::getDefaultStatusDropdown();

        return view('superAdmin.meeting.announce-edit',
            compact('announce', 'announceTypes', 'statuses'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'announce_no' => 'required|max:10',
    //         'announce_year' => 'required|max:4',
    //         'announce_start_date' => 'required|date',
    //         'announce_end_date' => 'required|date|after_or_equal:announce_start_date',
    //         'announce_title' => 'required|max:800',
    //         'announce_type' => ['required', Rule::in(Enum::getAnnounceTypeForValidation())],
    //         'announce_pic' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
    //         'announce_status' => ['required', Rule::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $announce = Announce::findOrFail($id);
    //     $announce->announce_no = $request->announce_no;
    //     $announce->announce_year = $request->announce_year;
    //     $announce->announce_start_date = $request->announce_start_date;
    //     $announce->announce_end_date = $request->announce_end_date;
    //     $announce->announce_title = $request->announce_title;
    //     $announce->announce_remark = $request->announce_remark;
    //     $announce->announce_type = $request->announce_type;

    //     if ($request->hasFile('announce_pic')) {
    //         $file = $request->file('announce_pic');
    //         $fileContent = file_get_contents($file->getRealPath());
    //         $announce->announce_pic = $fileContent;
    //     }

    //     $announce->announce_status = $request->announce_status;
    //     $announce->announce_updated_by = auth()->id();
    //     $announce->save();

    //     return redirect()->route('meeting.index')->with('success', 'อัพเดตประกาศสำเร็จ');
    // }

    /**
     * อัพเดทประกาศ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'announce_no' => 'required|max:10',
            'announce_year' => 'required|max:4',
            'announce_start_date' => 'required|date',
            'announce_end_date' => 'required|date|after_or_equal:announce_start_date',
            'announce_title' => 'required|max:800',
            'announce_type' => ['required', RuleValidation::in(Enum::getAnnounceTypeForValidation())],
            'announce_pic' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'announce_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $announce = $this->announceService->findAnnounceById($id);
            $this->announceService->updateAnnounce($announce, $request->all());
            return redirect()->route('meeting.index')
                ->with('success', 'อัพเดตประกาศสำเร็จ');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->withErrors(['error' => 'Failed to update announce'])
                ->withInput();
        }
    }


    // public function view($id)
    // {
    //     $announce = Announce::findOrFail($id);
    //     return response($announce->announce_pic)
    //         ->header('Content-Type', 'application/pdf');
    // }

    /**
     * แสดงไฟล์ประกาศ
     */
    public function view($id)
    {
        try {
            $announce = $this->announceService->findAnnounceById($id);
            return response($announce->announce_pic)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to view announce']);
        }
    }

    /**
     * ค้นหาประกาศ
     */
    public function search(Request $request)
    {
        try {
            $announces = $this->announceService->searchAnnounces($request->input('query'));
            return response()->json($announces);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to search announces'], 500);
        }
    }

    /**
     * เปลี่ยนสถานะประกาศ
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $announce = $this->announceService->findAnnounceById($id);
            $this->announceService->changeStatus($announce, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to change status'], 500);
        }
    }

}
