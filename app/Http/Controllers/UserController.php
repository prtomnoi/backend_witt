<?php

namespace App\Http\Controllers;

use App\Enums\Enum;
use App\Services\AddressService;
use App\Services\PositionService;
use App\Services\RuleService;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $userService;
    protected $addressService;
    protected $ruleService;
    protected $positionService;

    public function __construct(
        UserService     $userService,
        AddressService  $addressService,
        RuleService     $ruleService,
        PositionService $positionService
    )
    {
        $this->userService = $userService;
        $this->addressService = $addressService;
        $this->ruleService = $ruleService;
        $this->positionService = $positionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'position']);
        $users = $this->userService->filterUsers($filters);
        $positions = $this->positionService->getActivePositions();

        return view('superAdmin.user.index', compact('users', 'positions'));
    }

    public function showAccount(Request $request)
    {

        $filters = $request->only(['search', 'status', 'position']);
        $users = $this->userService->filterUsers($filters);
        $positions = $this->positionService->getActivePositions();
        return view('superAdmin.user.account', compact('users', 'positions'));
    }


    public function openAccount(Request $request)
    {

        $filters = $request->only(['search', 'status', 'position']);
        $users = $this->userService->filterUsers($filters);
        $positions = $this->positionService->getActivePositions();

        return view('superAdmin.user.index2', compact('users', 'positions'));
    }

    public function create()
    {
        $rules = $this->ruleService->getActiveRules();
        $positions = $this->positionService->getActivePositions();
        $provinces = $this->addressService->getAllProvinces();
        return view('superAdmin.user.create', compact('rules', 'positions', 'provinces'));
    }

    public function getAmphurs($provinceId)
    {
        $amphurs = $this->addressService->getAmphursByProvinceId($provinceId);
        return response()->json($amphurs);
    }

    public function getDistricts($amphurId)
    {
        $districts = $this->addressService->getDistrictsByAmphurId($amphurId);
        return response()->json($districts);
    }

    public function getZipcode($districtCode)
    {
        $zipcode = $this->addressService->getZipcodeByDistrictCode($districtCode);
        return response()->json($zipcode);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id_no' => 'required|unique:user|size:13',
            'user_password' => 'required|min:6',
            'user_email' => 'required|email|unique:user',
            'user_fname' => 'required|max:500',
            'user_lname' => 'required|max:500',
            'user_tel' => 'nullable|max:30',
            'user_address' => 'required',
            'district_code' => 'required',
            'amphur_id' => 'required',
            'province_id' => 'required',
            'zip_id' => 'required',
            'rule_id' => 'required',
            'position_id' => 'required',
            'user_id_no_pic' => 'nullable|image',
            'user_home_pic' => 'nullable|image',
            'user_number' => 'required|size:4',
            'witness1' => 'required|string|max:1000',
            'witness2' => 'nullable|max:1000',
            'beneficiaries_name.*' => 'required|string|max:1000',
            'beneficiaries_age.*' => 'required|integer|min:0|max:99',
            'beneficiaries_relation.*' => 'required|string|max:1000',
            'occupation_name.*' => 'required|string|max:500',
            'occupation_income.*' => 'required|numeric|min:0',
            'occupation_type.*' => 'required|in:M,S',
        ]);



        try {
            $this->userService->createUser($request->all());
            return redirect()->route('user.index')->with('success', 'สร้างผู้ใช้งานเสร็จสิ้น');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = $this->userService->findUserById($id);
        $rules = $this->ruleService->getActiveRules();
        $positions = $this->positionService->getActivePositions();
        $provinces = $this->addressService->getAllProvinces();
        $amphurs = $this->addressService->getAmphursByProvinceId($user->province_id);
        $districts = $this->addressService->getDistrictsByAmphurId($user->amphur_id);

        // $amphurs = Amphur::where('province_id', $user->province_id)->get();
        // $districts = District::where('amphur_id', $user->amphur_id)->get();

        $statuses = Enum::getUserStatusesDropdown();

        return view('superAdmin.user.edit', compact(
            'user', 'rules', 'positions', 'provinces', 'amphurs', 'districts', 'statuses'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = $this->userService->findUserById($id);

        $request->validate([
            'user_id_no' => 'required|size:13|unique:user,user_id_no,' . $id . ',user_id',
            'user_email' => 'required|email|unique:user,user_email,' . $id . ',user_id',
            'user_fname' => 'required|max:500',
            'user_lname' => 'required|max:500',
            'user_tel' => 'nullable|max:30',
            'user_address' => 'required',
            'district_code' => 'required',
            'amphur_id' => 'required',
            'province_id' => 'required',
            'zip_id' => 'required',
            'rule_id' => 'required',
            'position_id' => 'required',
            'user_id_no_pic' => 'nullable|image',
            'user_home_pic' => 'nullable|image',
            'user_number' => 'required|size:4|unique:user,user_number,' . $id . ',user_id',
            'witness1' => 'required|string|max:1000',
            'witness2' => 'string|max:1000',
            'beneficiaries_name.*' => 'required|string|max:1000',
            'beneficiaries_age.*' => 'required|integer|min:0|max:99',
            'beneficiaries_relation.*' => 'required|string|max:1000',
            'occupation_name.*' => 'required|string|max:500',
            'occupation_income.*' => 'required|numeric|min:0',
            'occupation_type.*' => 'required|in:M,S',
        ]);

        try {
            $this->userService->updateUser($user, $request->all());
            return redirect()->route('user.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function exportToPdf($userId)
    {
        try {

            $user = $this->userService->findUserById($userId);
            $beneficiaries = $this->userService->findBeneficiaryById($userId);
            $occupations = $this->userService->findOccupationById($userId);

            $pdf = Pdf::loadView('superAdmin.user.user_pdf', compact('user', 'beneficiaries', 'occupations'));

            return $pdf->stream('user_information.pdf');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('PDF generation error: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to export user information to PDF. Please try again later.']);
        }
    }

    public function exportDepositToPdf($userId)
    {
        try {

            $user = $this->userService->findUserById($userId);
            $beneficiaries = $this->userService->findBeneficiaryById($userId);
            $occupations = $this->userService->findOccupationById($userId);

            $pdf = Pdf::loadView('deposit', compact('user', 'beneficiaries', 'occupations'));

            return $pdf->stream('user_information.pdf');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('PDF generation error: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to export user information to PDF. Please try again later.']);
        }
    }

    public function exportToPdfForm()
    {


        $pdf = Pdf::loadView('superAdmin.user.user_pdf_blank');

        return $pdf->stream('user_information_blank.pdf');
    }

    public function exportAllPdf(Request $request)
    {
        $filters = $request->only(['search', 'status', 'position']);

        $users = app(UserService::class)->filterUsers($filters, 1000);

        $pdf = PDF::loadView('superAdmin.user.user_pdf_all', compact('users'));
        return $pdf->stream('users.pdf');
    }


    public function closeAccount()
    {
        $users = $this->userService->getAllUsers();
        return view('superAdmin.user.closeAccount', compact('users'));
    }

}
