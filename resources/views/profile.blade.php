<x-app-layout>
    <x-slot name="header">
        {{ __('Profile') }}
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-xl">

            <!-- Header Section -->
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Pengaturan Akun</h3>
                    <p class="text-muted small mb-0">Perbarui profil, email, dan keamanan akun Anda.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column: Profile Information -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <livewire:profile.update-profile-information-form/>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Security (Password & Delete) -->
                <div class="col-lg-6">
                    <!-- Update Password -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <livewire:profile.update-password-form/>
                        </div>
                    </div>

                    <!-- Delete User -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <livewire:profile.delete-user-form/>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Bootstrap Modal -->
    <!-- wire:ignore.self: Mencegah modal tertutup sendiri saat livewire re-render -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
         aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form wire:submit="deleteUser">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title font-serif fw-bold text-dark" id="deleteAccountModalLabel">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small mb-4">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <!-- Password Input -->
                        <div class="form-floating">
                            <input wire:model="password"
                                   type="password"
                                   class="form-control rounded-3 @error('password') is-invalid @enderror"
                                   id="delete_password"
                                   placeholder="Password"
                                   autocomplete="current-password">
                            <label for="delete_password" class="text-muted">
                                <i class="bi bi-key me-1"></i> {{ __('Password') }}
                            </label>
                            @error('password')
                            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 ms-2">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
