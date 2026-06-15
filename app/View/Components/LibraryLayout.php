<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LibraryLayout extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        // ✅ arahkan ke layout custom
        return view('layouts.library');
    }
}