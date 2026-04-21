<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDocumentController extends Controller
{
    public function index(): View
    {
        $documents = Material::with('user')->latest()->paginate(10);

        return view('pages.admin.documents.index', compact('documents'));
    }

    public function destroy(Material $material): RedirectResponse
    {
        if ($material->file_path && Storage::exists($material->file_path)) {
            Storage::delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }
}
