<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlossaryController extends Controller
{
    /**
     * Halaman Glosarium Singkatan & Istilah Teknis Hyu PACS
     */
    public function index(Request $request)
    {
        return view('glosarium.index');
    }
}
