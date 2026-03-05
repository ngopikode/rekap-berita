<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public string $elementId = 'sidebar-wrapper';

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<aside id="{{ $elementId }}">

    <div class="sidebar-heading text-center py-4">
        <div class="d-flex flex-column align-items-center">
            <span class="font-script text-brand"
                  style="font-size: 2.2rem; line-height: 1;">{{ config('app.name') }}</span>
            <small class="text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 2px;">RESTAURANT
                DASHBOARD</small>
        </div>
    </div>

    @php
        // todo: make this to database
         $currentRoute = request()->route()->getName();

$menuSections = [
    [
        'title' => 'Menu Utama',
        'items' => [
            ['route' => 'dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Dashboard'],
            ['route' => 'menu.index', 'icon' => 'bi bi-journal-richtext', 'label' => 'Menu Restoran'],
            ['route' => 'orders.index', 'icon' => 'bi bi-receipt-cutoff', 'label' => 'Pesanan Masuk'],
        ]
    ],
    [
        'title' => 'Pengaturan',
        'items' => [
            ['route' => 'settings.index', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Resto'],
            ['route' => 'profile', 'icon' => 'bi bi-person-gear', 'label' => 'Profil Akun'],
        ]
    ]
];
    @endphp

    <nav class="list-group list-group-flush my-2 flex-grow-1">

        @foreach($menuSections as $section)
            {{-- Section Title --}}
            <div class="small text-muted fw-bold px-4 mb-2 mt-2 text-uppercase" style="font-size: 0.7rem;">
                {{ $section['title'] }}
            </div>

            {{-- Menu Items --}}
            @foreach($section['items'] as $item)
                <x-sidebar-item
                    :route="$item['route']"
                    :icon="$item['icon']"
                    :label="$item['label']"
                    :active-route="$currentRoute"
                />
            @endforeach
        @endforeach

    </nav>

    <div class="p-3 border-top">
        <button type="button" wire:click="logout()"
                class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2">
            <i class="bi bi-box-arrow-left"></i> Log Out
        </button>
    </div>
</aside>
