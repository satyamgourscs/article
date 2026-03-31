<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CVController extends Controller
{
    public function generate()
    {
        $user = Auth::user();

        if (! $user->isPaid()) {
            abort(403, __('Upgrade required'));
        }

        $user->load(['portfolios', 'educations', 'skills', 'studentProfile']);

        if (! $user->hasCvGenerationPayload()) {
            $notify[] = ['warning', __('Add your name and at least one of: bio, skills, education, or portfolio before generating a CV.')];

            return to_route('user.home')->withNotify($notify);
        }

        $pdf = Pdf::loadView('Template::cv.template', compact('user'));
        $slug = $user->username ?: 'student-'.$user->id;

        return $pdf->download('cv-'.$slug.'.pdf');
    }
}
