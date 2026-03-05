<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeToggle extends Component
{
    public $themeMode;

    public function mount()
    {
        $this->themeMode = Auth::user()->theme_mode ?? 'light';
    }

    public function toggleTheme()
    {
        $user = Auth::user();
        $this->themeMode = ($this->themeMode === 'light') ? 'dark' : 'light';
        $user->theme_mode = $this->themeMode;
        $user->save();

        $this->dispatch('theme-updated', theme: $this->themeMode);
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
