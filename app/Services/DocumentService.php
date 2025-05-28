<?php

namespace App\Services;
use App\Enums\Enum;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function create(array $data): Document
    {
        return Document::create([
            'title' => $data['title'],
            'file_path' => $data['file_path'],
            'file_type' => $data['file_type'],
            'status' => $data['status'] ?? Enum::DOCUMENT_STATUS_ACTIVE,
            'created_by' => $data['created_by'],
            'created_at' => now()
        ]);
    }

    public function update(Document $document, array $data): Document
    {
        $document->update($data);
        return $document;
    }

    public function delete(Document $document): void
    {
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();
    }

    public function getAll($perPage = 15)
    {
        return Document::orderByDesc('created_at')->paginate($perPage);
    }
}
