<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models as Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $main = Models\Accounts::where('status', 1)->orderByDesc('id')->get();
        return view('admin.profiles.index', compact('main'));
    }

    public function show(Request $request, $id)
    {

    }
    public function create(Request $request)
    {
        return view('admin.profiles.create');
    }
    public function store(Request $request)
    {
        $validate = $request->validate([
            'fname' => ['required'],
            'lname' => ['required'],
            'gender' => ['required', 'in:M,F,OTHER'],
            'birthday' => ['sometimes'],
            'email' => ['required', 'unique:users,email'],
            'tel' => ['sometimes', 'max:10', 'unique:users,tel'],
        ]);
        try {
            DB::beginTransaction();
            // check user
            $user = Models\Users::create([
                'name' => $request->input('fname') . " " . $request->input('lname'),
                'email' => $request->input('email'),
                'password' => '123456789',
                'role_id' => 4, // custoemr,
                'tel' => $request->input('tel'),
            ]);
            // check image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $file_name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('accounts', $file_name, 'local_public');
            } else {
                $file_name = null;
            }
            // check account
            $account  = Models\Accounts::create([
                'fname' => $request->input('fname'),
                'lname' => $request->input('lname'),
                'gender' => $request->input('gender'),
                'birthday' => $request->input('birthday'),
                'email' => $request->input('email'),
                'tel' => $request->input('tel'),
                'status' => $request->input('status', 1),
                'avatar' => $file_name,
                'user_id' => $user->id,
                'created_by' => auth()->user()?->id,
                'updated_by' => auth()->user()?->id,
            ]);
            DB::commit();
            return redirect()->route('profiles.index')->with('success', 'Data saved successfully.');
        } catch (\Throwable $e)
        {
            DB::rollBack();
            return redirect()->back()->withErrors(['Cannot insert value.', $e->getMessage()]);
        }
    }
    public function edit(Request $request, $id)
    {
        $main = Models\Accounts::find($id);
        return view('admin.profiles.edit', compact('main'));
    }
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'fname' => ['sometimes'],
            'lname' => ['sometimes'],
            'gender' => ['sometimes', 'in:M,F,OTHER'],
            'birthday' => ['sometimes'],
            'email' => ['sometimes'],
            'tel' => ['sometimes', 'max:10'],
            'status' => ['sometimes'],
        ]);
        try {
            DB::beginTransaction();
            $main = Models\Accounts::find($id);
            $main->user?->update([
                'email' => $request->input('email', $main->email),
                'tel' => $request->input('tel', $main->tel),
            ]);
            // check image
            if ($request->hasFile('image')) {
                if($main->avatar){
                    File::delete(public_path('app/accounts/') . $main->avatar);
                }
                $file = $request->file('image');
                $file_name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('accounts', $file_name, 'local_public');
            } else {
                $file_name = $main->avatar;
            }
            // check account
           $main->update([
                'fname' => $request->input('fname', $main->fname),
                'lname' => $request->input('lname', $main->lname),
                'gender' => $request->input('gender', $main->gender),
                'birthday' => $request->input('birthday', $main->birthday),
                'email' => $request->input('email', $main->email),
                'tel' => $request->input('tel', $main->tel),
                'status' => $request->input('status', $main->status),
                'avatar' => $file_name,
                'updated_by' => auth()->user()?->id,
            ]);
            DB::commit();
            return redirect()->route('profiles.index')->with('success', 'Data saved successfully.');
        } catch (\Throwable $e)
        {
            DB::rollBack();
            return redirect()->back()->withErrors(['Cannot update value.', $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $validate = $request->validate([
            'status' => ['required']
        ]);
        try {
            DB::beginTransaction();
            $main = Models\Accounts::find($id);
            $main->update($validate);
            DB::commit();
            $res = [
                'code' => 200,
                'message' => 'Delete value success.',
                'error' => null,
            ];
            return response()->json($res);
        } catch (\Exception $e) {
            DB::rollBack();
            $res = [
                'code' => 200,
                'message' => 'Cannot delete value.',
                'error' => $e->getMessage(),
            ];
            return response()->json($res);
        }
    }
}
