<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AuditLogs extends Component
{
    public function mount()
    {
        if (!Auth::user()?->can('audit_logs.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        return view('security.audits.index');
    }
}
