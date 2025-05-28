<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\LogLogin;
use App\Models\Users;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models as Models;
use Illuminate\Support\Facades\DB;
use App\Models\Visitor;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        if (auth()->check() && auth()->user()->checkSuperAdmin()) {
            return $this->dashboardSuperAdmin($request);
        } else if(auth()->check()) {
            return $this->dashboard($request);
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        try {
            if (auth()->attempt($credentials)) {
                return redirect('/');
            } else {
                return back()->withErrors(['Invalid email or password.']);
            }
        } catch (\Throwable $th) {
            return back()->withErrors(['function login Error', $th->getMessage()]);
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    // create for api token auth
    public function loginAPI(Request $request)
    {
        // $credentials = $request->only(['email', 'password']);

        // if (!Auth::attempt($credentials)) {
        //     return response()->json(['message' => 'Login Fail']);
        // }

        // $user = Auth::user();
        // $user->tokens()->delete(); // Delete existing tokens

        // $token = $user->createToken('api_token')->plainTextToken;

        // $expires_at = now()->addMinutes(config('sanctum.expiration'))->format('Y-m-d H:i:s');

        // return response()->json([
        //     'message' => 'Login Success',
        //     'access_token' => $token,
        //     'token_type' => 'Bearer',
        //     'expires_at' => $expires_at,
        // ]);
    }

    public function dashboard(Request $request)
    {
        return view('home');
        // $c_order = Models\Order::select('id')->count();
        // $price_order = Models\Order::select('total_price')->sum('total_price');
        // $sum_donation = Models\DonationData::select('qty')->sum('qty');
        // return view('index', compact('c_order', 'price_order', 'sum_donation'));
    }

    public function dashboardSuperAdmin(Request $request)
    {
        $today = Carbon::today();

        return view('superAdmin.home', [
            'todayVisitors' => Visitor::whereDate('created_at', $today)->count(),
            'totalVisitors' => Visitor::count(),
            'bannerCount' => \App\Models\Banner::where('status', 'A')->count(),
        ]);
    }
}
