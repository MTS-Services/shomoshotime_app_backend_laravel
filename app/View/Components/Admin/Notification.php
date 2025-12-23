<?php

namespace App\View\Components\Admin;

use App\Models\CustomNotification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notification extends Component
{
   

    public function render(): View|Closure|string
    {
        return view('backend.admin.layouts.partials.notification');
    }
}
