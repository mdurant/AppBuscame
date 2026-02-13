<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TermsVersion;
use Illuminate\View\View;

class TermsController extends Controller
{
    public function show(TermsVersion $termsVersion): View
    {
        return view('terms.show', [
            'terms' => $termsVersion,
        ]);
    }
}
