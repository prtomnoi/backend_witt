<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $documents = $this->documentService->getAll(10); // กำหนดจำนวนต่อหน้า
        return view('superAdmin.documents.index', compact('documents'));
    }
    

    public function create()
    {
        return view('superAdmin.documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpeg,jpg,png|max:16384', // Max 16MB
        ]);

        $file = $request->file('file');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('documents', $filename, 'public');

        $this->documentService->create([
            'title' => $request->title,
            'file_path' => $path, 
            'file_type' => $file->getClientMimeType(),
            'created_by' => Auth::user()->id ?? 'system',
        ]);

        return redirect()->route('document.index')->with('success', 'อัปโหลดไฟล์เรียบร้อยแล้ว');
    }

    public function destroy(Document $document)
    {
        $this->documentService->delete($document);
        return back()->with('success', 'Document deleted.');
    }
}