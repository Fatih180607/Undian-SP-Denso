<?php

namespace App\Http\Controllers;

use App\Models\Plant; // <--- INI YANG HILANG! Pastikan baris ini ada
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index()
    {
        $plants = Plant::all();
        return view('plants', compact('plants'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_plant' => 'required']);
        Plant::create($request->all());
        return redirect()->back()->with('success', 'Plant baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Plant::destroy($id);
        return redirect()->back()->with('success', 'Plant berhasil dihapus!');
    }
}
