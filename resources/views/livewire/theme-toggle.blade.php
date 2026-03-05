<button class="btn btn-link nav-link text-secondary p-2" wire:click="toggleTheme" title="Ganti Tema">
    @if($themeMode === 'dark')
        <i class="bi bi-sun-fill fs-5 text-warning"></i>
    @else
        <i class="bi bi-moon-stars fs-5"></i>
    @endif
</button>
