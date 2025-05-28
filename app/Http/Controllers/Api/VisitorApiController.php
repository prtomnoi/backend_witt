<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;

class VisitorApiController extends Controller
{
    public function store(Request $request)
    {
        Visitor::create([
            'ip'         => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'url'        => $request->input('url'),
            'device'     => $request->input('device'), // optional
        ]);

        return response()->json(['message' => 'Visitor recorded'], 201);
    }
}
