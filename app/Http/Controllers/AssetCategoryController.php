<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->latest()->get();
        return view('aset.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'umur_ekonomis' => 'required|integer|min:1|max:100'
        ]);

        AssetCategory::create($validated);
        return back()->with('success', 'Kategori aset berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $category = AssetCategory::findOrFail($id);
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'umur_ekonomis' => 'required|integer|min:1|max:100'
        ]);

        $category->update($validated);
        return back()->with('success', 'Kategori aset berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);
        if ($category->assets()->count() > 0) {
            return back()->withErrors(['msg' => 'Kategori ini tidak bisa dihapus karena masih memiliki aset!']);
        }
        $category->delete();
        return back()->with('success', 'Kategori aset berhasil dihapus!');
    }
}
