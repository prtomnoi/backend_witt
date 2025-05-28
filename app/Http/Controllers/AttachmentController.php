<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models as Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AttachmentController extends Controller
{
    public function destroy(Request $request, $id) {
        try {
            DB::beginTransaction();
            $main = Models\Attachment::find($id);
            $main->delete();
            File::delete(public_path($main->path));
            DB::commit();
            return response()->json(['message' => 'ลบรูปสำเร็จ', 'error' => null, 'status' => 200]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'ไม่สามารถลบรูปได้', 'error' => $e->getMessage(), 'status' => 500]);
        }
    }
}
