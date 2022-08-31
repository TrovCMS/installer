<?php

namespace App\Http\Controllers;

use App\Models\Sheet;

class SheetController extends Controller
{
    public function show(string $type = null, Sheet $page = null)
    {
        abort_unless(($page->status == 'published' && $page->type == $type) || auth()->user(), 404);

        return view('sheet', [
            'page' => $page,
        ]);
    }
}
