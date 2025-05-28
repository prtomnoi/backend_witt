<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\MeetingDocService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class MeetingDocController extends Controller
{

    protected $meetingDocService;

    public function __construct(MeetingDocService $meetingDocService)
    {
        $this->meetingDocService = $meetingDocService;
    }

    // public function index()
    // {
    //     $meetings = MeetingDoc::orderBy('meeting_doc_id', 'desc')->paginate(10);
    //     return view('superAdmin.meeting.index', compact('meetings'));
    // }

    public function index(Request $request)
    {
        $filters = [
            'year' => $request->get('year'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];
    
        $meetings = $this->meetingDocService->getFilteredMeetingDocs($filters);
    
        return view('superAdmin.meeting.index', compact('meetings'));
    }
    
    public function create()
    {
        $statuses = Enum::getDefaultStatusDropdown();
        return view('superAdmin.meeting.create', compact('statuses'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'meeting_doc_no' => 'required|max:10',
    //         'meeting_doc_year' => 'required|max:4',
    //         'meeting_doc_date' => 'required|date',
    //         'meeting_doc_title' => 'required|max:800',
    //         'meeting_doc_pic' => 'required|file|mimes:pdf,jpg,jpeg,png',
    //         'meeting_doc_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $file = $request->file('meeting_doc_pic');
    //     $fileContent = file_get_contents($file->getRealPath());

    //     $meeting = new MeetingDoc();
    //     $meeting->meeting_doc_no = $request->meeting_doc_no;
    //     $meeting->meeting_doc_year = $request->meeting_doc_year;
    //     $meeting->meeting_doc_date = $request->meeting_doc_date;
    //     $meeting->meeting_doc_title = $request->meeting_doc_title;
    //     $meeting->meeting_doc_remark = $request->meeting_doc_remark;
    //     $meeting->meeting_doc_pic = $fileContent;
    //     $meeting->meeting_doc_status = $request->meeting_doc_status;
    //     $meeting->meeting_doc_created_by = auth()->id();
    //     $meeting->meeting_doc_updated_by = auth()->id();
    //     $meeting->save();

    //     return redirect()->route('meeting.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'meeting_doc_no' => 'required|max:10',
            'meeting_doc_year' => 'required|max:4',
            'meeting_doc_date' => 'required|date',
            'meeting_doc_title' => 'required|max:800',
            'meeting_doc_pic' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'meeting_doc_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $this->meetingDocService->createMeetingDoc($request->all());
            return redirect()->route('meeting.index')
                ->with('success', 'เพิ่มข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create meeting document'])
                ->withInput();
        }
    }

    // public function edit($id)
    // {
    //     $meeting = MeetingDoc::findOrFail($id);

    //     $statuses = Enum::getDefaultStatusDropdown();
    //     return view('superAdmin.meeting.edit', compact('meeting', 'statuses'));
    // }

    public function edit($id)
    {
        $meeting = $this->meetingDocService->findMeetingDocById($id);
        $statuses = Enum::getDefaultStatusDropdown();
        return view('superAdmin.meeting.edit', compact('meeting', 'statuses'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'meeting_doc_no' => 'required|max:10',
    //         'meeting_doc_year' => 'required|max:4',
    //         'meeting_doc_date' => 'required|date',
    //         'meeting_doc_title' => 'required|max:800',
    //         'meeting_doc_pic' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
    //         'meeting_doc_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $meeting = MeetingDoc::findOrFail($id);
    //     $meeting->meeting_doc_no = $request->meeting_doc_no;
    //     $meeting->meeting_doc_year = $request->meeting_doc_year;
    //     $meeting->meeting_doc_date = $request->meeting_doc_date;
    //     $meeting->meeting_doc_title = $request->meeting_doc_title;
    //     $meeting->meeting_doc_remark = $request->meeting_doc_remark;

    //     if ($request->hasFile('meeting_doc_pic')) {
    //         $file = $request->file('meeting_doc_pic');
    //         $fileContent = file_get_contents($file->getRealPath());
    //         $meeting->meeting_doc_pic = $fileContent;
    //     }

    //     $meeting->meeting_doc_status = $request->meeting_doc_status;
    //     $meeting->meeting_doc_updated_by = auth()->id();
    //     $meeting->save();

    //     return redirect()->route('meeting.index')->with('success', 'อัพเดตข้อมูลสำเร็จ');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'meeting_doc_no' => 'required|max:10',
            'meeting_doc_year' => 'required|max:4',
            'meeting_doc_date' => 'required|date',
            'meeting_doc_title' => 'required|max:800',
            'meeting_doc_pic' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'meeting_doc_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $meeting = $this->meetingDocService->findMeetingDocById($id);
            $this->meetingDocService->updateMeetingDoc($meeting, $request->all());
            return redirect()->route('meeting.index')
                ->with('success', 'อัพเดตข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Failed to update meeting document'])
                ->withInput();
        }
    }

    // public function view($id)
    // {
    //     $meeting = MeetingDoc::findOrFail($id);
    //     return response($meeting->meeting_doc_pic)
    //         ->header('Content-Type', 'application/pdf');
    // }

    public function view($id)
    {
        try {
            $meeting = $this->meetingDocService->findMeetingDocById($id);
            return response($meeting->meeting_doc_pic)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to view document']);
        }
    }
}
