<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tentang', function () {
    return view('tentang');
});
Route::get('/sapa/{nama}', function ($nama) {
    return "Halo, $nama! Selamat datang di toko barang Mahal.";
});
Route::get('/kategori/{nama?}', function ($nama = 'Semua') {
    return "Tampilan kategori: $nama";
});

Route::get('/produk/{id}', function ($id) {
    return "Detail produk #$id";
})->name('produk.detail');
