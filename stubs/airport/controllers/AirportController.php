<?php

namespace App\Http\Controllers;

use App\Models\Runway;

class AirportController extends Controller
{
    public function show(Runway $page)
    {
        abort_unless($page->status == 'published' || auth()->user(), 404);

        return view('runway', [
            'page' => $page,
        ]);
    }
}
