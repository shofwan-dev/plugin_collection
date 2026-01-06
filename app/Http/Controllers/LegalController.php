<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function terms(): View
    {
        return view('legal.terms', [
            'title' => 'Terms of Service',
        ]);
    }

    public function privacy(): View
    {
        return view('legal.privacy', [
            'title' => 'Privacy Policy',
        ]);
    }

    public function refund(): View
    {
        return view('legal.refund', [
            'title' => 'Refund Policy',
        ]);
    }
}
