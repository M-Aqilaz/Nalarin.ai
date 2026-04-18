<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class AdminDocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('user')->latest()->paginate(10);
        return view('Admin.documents.index', compact('documents'));
    }

    public function destroy(Document $document)
    {
        // Delete file from storage if needed
        $document->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }
}
