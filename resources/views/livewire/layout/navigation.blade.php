<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $header = 'Dashboard Overview';

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="navbar navbar-expand navbar-light sticky-top px-3 px-lg-4 border-bottom shadow-sm"
     id="mainNavbar" style="min-height: 70px;">

    <div class="d-flex align-items-center justify-content-between w-100 flex-nowrap">

        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!-- Mobile Toggle (Offcanvas) -->
            <button class="btn text-primary border-0 p-2 d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <!-- Desktop Toggle -->
            <button class="btn text-primary border-0 p-2 d-none d-md-block" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>

            <h5 class="m-0 font-serif fw-bold d-none d-md-block text-truncate" style="max-width: 300px;">
                {{ $header ?? 'Restaurant Management' }}
            </h5>
        </div>

        <ul class="navbar-nav ms-auto flex-row align-items-center gap-2 gap-lg-3">

            <li class="nav-item">
                <livewire:theme-toggle />
            </li>

            <li class="nav-item">
                <a class="nav-link text-secondary position-relative p-2" href="#">
                    <i class="bi bi-bell fs-5"></i>
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-1 mt-2 me-2">
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-1" href="#" id="navbarDropdown"
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">

                    <div
                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                        style="width: 38px; height: 38px;">
                        <span class="fw-bold">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    </div>

                    <div class="d-none d-lg-block text-start lh-1">
                        <div class="fw-bold small text-dark text-truncate" style="max-width: 120px;">
                            {{ Auth::user()->name ?? 'User' }}
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">Admin</small>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2"
                    aria-labelledby="navbarDropdown">
                    <li class="d-lg-none px-3 py-2 border-bottom mb-2 bg-light">
                        <span class="fw-bold d-block text-dark">{{ Auth::user()->name ?? 'User' }}</span>
                        <small class="text-muted">Admin</small>
                    </li>

                    <li>
                        <a class="dropdown-item py-2" href="{{ route('profile') }}" wire:navigate><i
                                class="bi bi-person me-2"></i>
                            Edit Profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button type="button" wire:click="logout()"
                                class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                        </button>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

@push('custom-scripts')
    <script>
        // Desktop sidebar toggle logic
        function initDesktopSidebarToggle() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.onclick = function (e) {
                    e.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem(
                        'sb|sidebar-toggle',
                        document.body.classList.contains('sb-sidenav-toggled').toString()
                    );
                };
            }
        }

        document.addEventListener('DOMContentLoaded', initDesktopSidebarToggle);
        document.addEventListener('livewire:navigated', initDesktopSidebarToggle);
    </script>
@endpush
