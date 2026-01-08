{{-- ================================================================
     FILE: resources/views/home.blade.php
     TEMA: Toko Parfum Online Mewah & Elegan dengan Background Switcher
     ================================================================ --}}

@extends('layouts.app')

@section('title', 'Beranda - Parfum Impian Anda')

@section('content')

    <!-- Theme Switcher Button (pojok kanan atas) -->
    <div class="position-fixed top-0 end-0 p-4" style="z-index: 1050;">
        <div class="dropdown">
            <button class="btn btn-light rounded-circle shadow-lg p-3" data-bs-toggle="dropdown">
                <i class="bi bi-palette fs-4"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item active" href="#" data-theme="vibrant">Vibrant Crystal</a></li>
                <li><a class="dropdown-item" href="#" data-theme="purple">Purple Luxury</a></li>
                <li><a class="dropdown-item" href="#" data-theme="dark">Dark Glitter</a></li>
                <li><a class="dropdown-item" href="#" data-theme="golden">Golden Elegance</a></li>
                <li><a class="dropdown-item" href="#" data-theme="blue-fabric">Blue Fabric</a></li>
                <li><a class="dropdown-item" href="#" data-theme="minimal">Minimal White</a></li>
            </ul>
        </div>
    </div>

    <!-- Hero Section dengan full background image dinamis -->
    <section id="hero-section" class="d-flex align-items-center py-5 position-relative overflow-hidden" style="min-height: 80vh; background-size: cover; background-position: center; background-color: #000; background-image: url('https://thumbs.dreamstime.com/b/luxurious-crystal-perfume-bottles-vibrant-gradient-background-sophisticated-beauty-banner-captivating-displayed-against-chic-374954694.jpg');">
        <!-- Overlay gelap agar teks terbaca jelas -->
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50"></div>

        <div class="container position-relative z-index-2">
            <div class="row justify-content-start">
                <div class="col-lg-6 text-white">
                    <h1 class="display-3 fw-bold mb-4 text-shadow">
                        Temukan Aroma yang Mencerminkan Dirimu
                    </h1>
                    <p class="lead mb-5 opacity-90 text-shadow">
                        Koleksi parfum premium dari brand ternama dunia.<br>
                        Wangi yang tahan lama, elegan, dan tak terlupakan.
                    </p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-light btn-lg px-5 py-3 shadow-lg">
                        <i class="bi bi-search me-2"></i>Jelajahi Koleksi Parfum
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Aroma Populer -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 fw-light">Kategori Aroma Populer</h2>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-6 g-4 text-center">
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://byisranewyork.com/cdn/shop/articles/Untitled_design_4.png?v=1733503723&width=1100" class="card-img-top p-4" alt="Floral" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Floral</h5>
                            <p class="card-text small">Aroma bunga romantis & segar</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://www.shutterstock.com/image-vector/perfume-types-glass-bottles-oriental-600w-2520928263.jpg" class="card-img-top p-4" alt="Woody" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Woody</h5>
                            <p class="card-text small">Hangat, kayu & maskulin</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://ewxd4bbz5hp.exactdn.com/wp-content/uploads/2022/09/floral-fragrance-perfumes-scents-chanel-cartier-issey-miyake-mark-jacobs-loewe-chloe-hermes.webp?strip=all&lossy=1&ssl=1" class="card-img-top p-4" alt="Fresh" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Fresh</h5>
                            <p class="card-text small">Segar, citrus & aquatic</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://www.shutterstock.com/image-vector/vector-set-icons-aromas-top-260nw-1882178572.jpg" class="card-img-top p-4" alt="Oriental" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Oriental</h5>
                            <p class="card-text small">Eksotis, rempah & manis</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://scentchronicles.com/wp-content/uploads/2024/11/Common-Misconceptions-About-What-Are-Fragrance-Families-1024x683.webp" class="card-img-top p-4" alt="Citrus" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Citrus</h5>
                            <p class="card-text small">Energik & jeruk segar</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://www.shutterstock.com/image-vector/vector-icon-set-different-perfume-260nw-2079945979.jpg" class="card-img-top p-4" alt="Spicy" style="object-fit: contain; height: 200px;">
                        <div class="card-body">
                            <h5 class="card-title">Spicy</h5>
                            <p class="card-text small">Pedas, hangat & misterius</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    .text-shadow { text-shadow: 2px 2px 8px rgba(0,0,0,0.8); }
    #hero-section { transition: background-image 1s ease-in-out; }
</style>
@endpush

@push('scripts')
<script>
    const themes = {
        vibrant: 'https://thumbs.dreamstime.com/b/luxurious-crystal-perfume-bottles-vibrant-gradient-background-sophisticated-beauty-banner-captivating-displayed-against-chic-374954694.jpg',
        purple: 'https://img.freepik.com/free-psd/luxury-perfume-banner-template_23-2149365618.jpg', // alternatif yang lebih stabil
        dark: 'https://thumbs.dreamstime.com/b/dark-perfume-bottle-glitter-podium-luxury-fragrance-product-stage-cosmetics-mockup-promo-male-cologne-metallic-background-407424055.jpg',
        golden: 'https://thumbs.dreamstime.com/b/luxury-perfume-bottle-sits-golden-satin-background-close-up-view-exquisite-design-elegant-feminine-fragrance-perfect-352479863.jpg',
        'blue-fabric': 'https://thumbs.dreamstime.com/b/captivating-blue-perfume-bottle-luxurious-fabric-fragrance-still-life-ideal-advertising-immerse-yourself-allure-381755806.jpg',
        minimal: 'https://www.creativefabrica.com/wp-content/uploads/2023/09/11/Fragsence-Perfume-Store-Hero-Section-Graphics-78996015-1.jpg'
    };

    document.querySelectorAll('[data-theme]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            const hero = document.getElementById('hero-section');

            hero.style.backgroundImage = `url('${themes[theme] || themes.vibrant}')`;

            document.querySelectorAll('[data-theme]').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            localStorage.setItem('perfumeHeroBg', theme);
        });
    });

    // Load saved theme
    const saved = localStorage.getItem('perfumeHeroBg') || 'vibrant';
    const savedItem = document.querySelector(`[data-theme="${saved}"]`);
    if (savedItem) savedItem.click();
</script>
@endpush
