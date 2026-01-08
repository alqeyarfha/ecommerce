{{-- ================================================
FILE: resources/views/partials/navbar.blade.php
FUNGSI: Navigation bar untuk customer - Tema Dark Elegan
================================================ --}}

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top"
     style="background: linear-gradient(135deg, #8E1CCB,gold, #14A8C5); border-bottom: 1px solid #333;">
    <div class="container">
        {{-- Logo & Brand --}}
        <a class="navbar-brand text-white fw-bold" href="{{ route('home') }}">
            <i class="bi bi-bag-heart-fill me-2 text-primary"></i>
            TokoParfumeOnline
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navbar Content --}}
        <div class="collapse navbar-collapse" id="navbarMain">
            {{-- Search Form --}}
            <form class="d-flex mx-auto" style="max-width: 500px; width: 100%;" action="{{ route('catalog.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control bg-white text-white border-secondary"
                           placeholder="Cari parfum impianmu..." value="{{ request('q') }}" style="border-right: none;">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            {{-- Right Menu --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Katalog --}}
                <li class="nav-item">
                    <a class="nav-link text-white opacity-90 hover-opacity-100 px-3" href="{{ route('catalog.index') }}">
                        <i class="bi bi-grid me-1"></i> Katalog
                    </a>
                </li>

                @auth
                {{-- Wishlist --}}
                <li class="nav-item">
                    <a class="nav-link position-relative text-white px-3" href="{{ route('wishlist.index') }}">
                        <i class="bi bi-heart fs-5"></i>
                        @if(auth()->user()->wishlists()->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size: 0.65rem;">
                            {{ auth()->user()->wishlists()->count() }}
                        </span>
                        @endif
                    </a>
                </li>

                {{-- Cart --}}
                <li class="nav-item">
                    <a class="nav-link position-relative text-white px-3" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3 fs-5"></i>
                        @php
                            $cartCount = auth()->user()->cart?->items()->count() ?? 0;
                        @endphp
                        @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                              style="font-size: 0.65rem;">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>
                </li>

                {{-- User Dropdown --}}
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white"
                       href="#" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}"
                             class="rounded-circle me-2 border border-light"
                             width="36" height="36" alt="{{ auth()->user()->name }}">
                        <span class="d-none d-lg-inline fw-medium">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2"
                        style="background-color: #D78444; min-width: 200px;">
                        <li>
                            <a class="dropdown-item text-white opacity-90 hover-bg-dark" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-white opacity-90 hover-bg-dark" href="{{ route('orders.index') }}">
                                <i class="bi bi-bag me-2"></i> Pesanan Saya
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                        <li><hr class="dropdown-divider bg-secondary"></li>
                        <li>
                            <a class="dropdown-item text-primary fw-bold hover-bg-dark" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider bg-secondary"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger opacity-90 hover-bg-dark">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                {{-- Guest Links --}}
                <li class="nav-item">
                    <a class="nav-link text-white opacity-90 px-3" href="{{ route('login') }}">Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary btn-sm border-white text-white hover-bg-primary ms-2"
                       href="{{ route('register') }}">
                        Daftar
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Custom CSS untuk hover effect halus --}}
<style>
    .navbar-dark .nav-link {
        transition: all 0.3s ease;
    }
    .navbar-dark .nav-link:hover {
        opacity: 1 !important;
        transform: translateY(-1px);
    }
    .hover-bg-dark:hover {
        background-color: #E2D6D6 !important;
    }
    .hover-bg-primary:hover {
        background-color: #FEFFFF !important;
        border-color: #0d6efd !important;
    }
</style>
