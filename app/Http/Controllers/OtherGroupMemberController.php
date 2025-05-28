<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\OtherGroupMemberService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as RuleValidation;

class OtherGroupMemberController extends Controller
{

    protected $otherGroupMemberService;
    protected $userService;

    public function __construct(
        OtherGroupMemberService $otherGroupMemberService,
        UserService             $userService
    )
    {
        $this->otherGroupMemberService = $otherGroupMemberService;
        $this->userService = $userService;
    }

    // public function index()
    // {
    //     $members = OtherGroupMember::with(['user'])
    //         ->orderBy('ogm_id', 'desc')
    //         ->paginate(10);
    //     return view('superAdmin.other-group-member.index', compact('members'));
    // }

    public function index()
    {
        $members = $this->otherGroupMemberService->getAllMembers();
        return view('superAdmin.other-group-member.index', compact('members'));
    }

    // public function create()
    // {
    //     $users = User::whereIn('user_status', [
    //         Enum::USER_STATUS_A,
    //         Enum::USER_STATUS_P
    //     ])->get();
    //     $statuses = Enum::getDefaultStatusDropdown();
    //     return view('superAdmin.other-group-member.create', compact('users', 'statuses'));
    // }

    public function create()
    {
        $users = $this->userService->getActiveUsers();
        $statuses = Enum::getDefaultStatusDropdown();
        return view('superAdmin.other-group-member.create', compact('users', 'statuses'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:user,user_id',
    //         'ogm_name' => 'required|max:500',
    //         'ogm_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $member = new OtherGroupMember();
    //     $member->user_id = $request->user_id;
    //     $member->ogm_name = $request->ogm_name;
    //     $member->ogm_status = $request->ogm_status;
    //     $member->ogm_created_by = auth()->id();
    //     $member->ogm_updated_by = auth()->id();
    //     $member->save();

    //     return redirect()->route('other-group-member.index')
    //         ->with('success', 'เพิ่มข้อมูลสำเร็จ');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'ogm_name' => 'required|max:500',
            'ogm_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $this->otherGroupMemberService->createMember($request->all());
            return redirect()->route('other-group-member.index')
                ->with('success', 'เพิ่มข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create member'])
                ->withInput();
        }
    }


    // public function edit($id)
    // {
    //     $member = OtherGroupMember::findOrFail($id);
    //     $users = User::whereIn('user_status', [
    //         Enum::USER_STATUS_A,
    //         Enum::USER_STATUS_P
    //     ])->get();
    //     $statuses = Enum::getDefaultStatusDropdown();
    //     return view('superAdmin.other-group-member.edit',
    //         compact('member', 'users', 'statuses'));
    // }

    public function edit($id)
    {
        $member = $this->otherGroupMemberService->findMemberById($id);
        $users = $this->userService->getActiveUsers();
        $statuses = Enum::getDefaultStatusDropdown();
        return view('superAdmin.other-group-member.edit',
            compact('member', 'users', 'statuses'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:user,user_id',
    //         'ogm_name' => 'required|max:500',
    //         'ogm_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
    //     ]);

    //     $member = OtherGroupMember::findOrFail($id);
    //     $member->user_id = $request->user_id;
    //     $member->ogm_name = $request->ogm_name;
    //     $member->ogm_status = $request->ogm_status;
    //     $member->ogm_updated_by = auth()->id();
    //     $member->save();

    //     return redirect()->route('other-group-member.index')
    //         ->with('success', 'อัพเดตข้อมูลสำเร็จ');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'ogm_name' => 'required|max:500',
            'ogm_status' => ['required', RuleValidation::in(Enum::getDefaultStatusesForValidation())]
        ]);

        try {
            $member = $this->otherGroupMemberService->findMemberById($id);
            $this->otherGroupMemberService->updateMember($member, $request->all());
            return redirect()->route('other-group-member.index')
                ->with('success', 'อัพเดตข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update member'])
                ->withInput();
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $member = $this->otherGroupMemberService->findMemberById($id);
            $this->otherGroupMemberService->changeMemberStatus($member, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

}
