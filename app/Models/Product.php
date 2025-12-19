<?php
// ================================================================
// FILE: app/Models/Product.php
// ================================================================
//
// MODEL adalah representasi dari TABEL DATABASE.
// Setiap instance Product mewakili 1 ROW di tabel `products`.
//
// KEGUNAAN MODEL:
// 1. Membaca data dari database (SELECT)
// 2. Menyimpan data ke database (INSERT/UPDATE)
// 3. Menghapus data (DELETE)
// 4. Mendefinisikan RELASI antar tabel
// 5. Menambahkan LOGIC bisnis (accessor, mutator, scope)
//
// ================================================================

namespace App\Models;
// ↑ Semua model Laravel disimpan di folder app/Models/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
// ↑ Import class yang dibutuhkan:
//   - HasFactory: untuk membuat data dummy dengan Factory
//   - Model: class dasar Eloquent ORM
//   - Relations: tipe-tipe relasi database
//   - Str: helper untuk manipulasi string

class Product extends Model
// ↑ Product adalah class kita, extends Model dari Laravel
{
    use HasFactory;
    // ↑ TRAIT: menambahkan method factory() untuk testing/seeding
    //   Product::factory()->create() untuk bikin data dummy

    // ================================================================
    // FILLABLE - MASS ASSIGNMENT PROTECTION
    // ================================================================

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'weight',
        'is_active',
        'is_featured',
    ];
    // ↑ FILLABLE adalah daftar kolom yang BOLEH diisi secara mass-assignment
    //
    //   MASS ASSIGNMENT adalah ketika kita isi banyak kolom sekaligus:
    //   Product::create($request->all())
    //   Product::update($request->all())
    //
    //   TANPA FILLABLE (BAHAYA!):
    //   Hacker bisa kirim: { "name": "Test", "is_admin": true }
    //   Dan kolom is_admin akan ikut terisi!
    //
    //   DENGAN FILLABLE:
    //   Hanya kolom yang didaftarkan yang bisa diisi
    //   Kolom lain (seperti id, created_at) akan diabaikan
    //
    //   ALTERNATIF: protected $guarded = ['id'];
    //   Kebalikannya, daftar kolom yang TIDAK BOLEH diisi

    // ================================================================
    // CASTS - KONVERSI TIPE DATA OTOMATIS
    // ================================================================

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
    // ↑ CASTS mengkonversi tipe data otomatis saat baca dari database
    //
    //   DATABASE menyimpan:
    //   - price: "150000.00" (string)
    //   - is_active: 1 (integer)
    //
    //   SETELAH CAST:
    //   - $product->price = 150000.00 (float dengan 2 desimal)
    //   - $product->is_active = true (boolean)
    //
    //   TIPE CAST YANG TERSEDIA:
    //   - 'integer', 'float', 'double'
    //   - 'decimal:2' (angka dengan 2 desimal)
    //   - 'boolean' (true/false)
    //   - 'array', 'json' (decode JSON otomatis)
    //   - 'date', 'datetime' (object Carbon)
    //   - 'encrypted' (enkripsi otomatis)

    // ================================================================
    // RELATIONSHIPS - RELASI ANTAR TABEL
    // ================================================================

    /**
     * BELONGS TO: Produk ini MILIK satu Kategori.
     *
     * Relasi ini menunjukkan bahwa:
     * - Tabel products punya kolom "category_id"
     * - Kolom tersebut merujuk ke tabel categories.id
     *
     * CARA PAKAI:
     * $product->category->name  // Ambil nama kategori
     *
     * SQL YANG DIJALANKAN:
     * SELECT * FROM categories WHERE id = {product.category_id}
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
        // ↑ belongsTo(Category::class) artinya:
        //   - Cari di tabel categories
        //   - Gunakan foreign key default: category_id (nama model + _id)
        //   - Cocokkan dengan primary key: id
        //
        //   CUSTOM:
        //   $this->belongsTo(Category::class, 'cat_id', 'category_id')
        //   Jika nama kolom berbeda dari konvensi
    }

    /**
     * HAS MANY: Produk ini MEMILIKI banyak Gambar.
     *
     * Relasi ini menunjukkan bahwa:
     * - Tabel product_images punya kolom "product_id"
     * - Satu produk bisa punya banyak gambar
     *
     * CARA PAKAI:
     * $product->images           // Collection semua gambar
     * $product->images->first()  // Gambar pertama
     * $product->images->count()  // Jumlah gambar
     *
     * SQL:
     * SELECT * FROM product_images WHERE product_id = {product.id} ORDER BY sort_order
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
        // ↑ hasMany artinya "satu produk punya banyak gambar"
        //   ->orderBy('sort_order') mengurutkan berdasarkan urutan yang diset admin
    }

    /**
     * HAS ONE: Produk ini MEMILIKI satu Gambar Utama.
     *
     * Ini adalah variasi hasMany tapi hanya ambil 1.
     * Digunakan untuk thumbnail di listing produk.
     *
     * CARA PAKAI:
     * $product->primaryImage->image_url
     *
     * SQL:
     * SELECT * FROM product_images
     * WHERE product_id = {id} AND is_primary = 1
     * LIMIT 1
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
        // ↑ hasOne = cuma ambil 1 record
        //   ->where('is_primary', true) = filter yang gambar utama saja
    }

    // ================================================================
    // ACCESSORS - CUSTOM ATTRIBUTE (Computed Property)
    // ================================================================

    /**
     * ACCESSOR: Harga yang ditampilkan (bisa diskon atau normal)
     *
     * Accessor adalah property VIRTUAL yang dihitung saat diakses.
     * Tidak ada di database, tapi bisa diakses seperti kolom biasa.
     *
     * PENAMAAN:
     * get{NamaAttribute}Attribute
     * getDisplayPriceAttribute -> $product->display_price
     *
     * CARA PAKAI:
     * $product->display_price   // 120000 (kalau diskon)
     *                           // 150000 (kalau tidak diskon)
     */
    public function getDisplayPriceAttribute(): float
    {
        // Cek apakah ada harga diskon DAN diskon lebih murah dari harga normal
        if ($this->discount_price !== null && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }
        // Jika tidak ada diskon, return harga normal
        return (float) $this->price;
    }

    /**
     * ACCESSOR: Format harga ke Rupiah
     *
     * CARA PAKAI:
     * $product->formatted_price  // "Rp 1.500.000"
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->display_price, 0, ',', '.');
        // ↑ number_format(angka, desimal, separator_desimal, separator_ribuan)
        //   150000 -> "150.000"
        //   + 'Rp ' -> "Rp 150.000"
    }

    /**
     * ACCESSOR: Cek apakah produk punya diskon.
     *
     * CARA PAKAI:
     * @if($product->has_discount)
     *     <span>SALE!</span>
     * @endif
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price !== null
            && $this->discount_price < $this->price;
        // True jika:
        // 1. discount_price tidak null (ada diisi)
        // 2. DAN discount_price lebih kecil dari price (benar-benar diskon)
    }

    /**
     * ACCESSOR: Persentase diskon.
     *
     * CARA PAKAI:
     * "Diskon {{ $product->discount_percentage }}%"  // "Diskon 20%"
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount) {
            return 0;
        }
        // Rumus: ((Harga Asli - Harga Diskon) / Harga Asli) * 100
        // Contoh: ((150000 - 120000) / 150000) * 100 = 20%
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * ACCESSOR: URL gambar produk.
     *
     * Jika tidak ada gambar, return placeholder.
     * Ini mencegah error di view kalau gambar belum diupload.
     */
    public function getImageUrlAttribute(): string
    {
        // Cek apakah ada gambar utama
        if ($this->primaryImage) {
            return asset('storage/' . $this->primaryImage->image_path);
            // ↑ asset() membuat URL lengkap
            //   'storage/products/image.jpg' -> 'http://domain.com/storage/products/image.jpg'
        }
        // Return placeholder jika tidak ada gambar
        return asset('images/no-image.png');
    }

    // ================================================================
    // SCOPES - REUSABLE QUERY FILTERS
    // ================================================================

    /**
     * SCOPE: Filter produk aktif
     *
     * Scope adalah "shortcut" untuk query yang sering dipakai.
     *
     * PENAMAAN:
     * scope{Nama} -> Product::nama()
     * scopeActive -> Product::active()
     *
     * CARA PAKAI:
     * Product::active()->get()
     * Product::active()->where('stock', '>', 0)->get()
     *
     * SAMA DENGAN:
     * Product::where('is_active', true)->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
        // ↑ $query adalah Query Builder yang sedang dibangun
        //   Kita tambahkan kondisi WHERE is_active = true
    }

    /**
     * SCOPE: Filter produk yang ada stok
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * SCOPE: Filter produk featured (unggulan)
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * SCOPE: Filter produk yang sedang diskon
     *
     * CARA PAKAI:
     * Product::onSale()->get()  // Semua produk diskon
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('discount_price')
                     ->whereColumn('discount_price', '<', 'price');
        // ↑ whereColumn() membandingkan 2 kolom di database
        //   Berbeda dengan where() yang membandingkan kolom dengan nilai
    }

    /**
     * SCOPE: Pencarian produk
     *
     * CARA PAKAI:
     * Product::search('laptop')->get()  // Cari "laptop" di nama dan deskripsi
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            // ↑ where(function) = GROUP kondisi dalam kurung
            //   SQL: WHERE (name LIKE ... OR description LIKE ...)
            //   Penting untuk kombinasi dengan kondisi lain

            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
            // ↑ LIKE dengan % = wildcard
            //   %laptop% = mengandung kata "laptop" di mana saja
        });
    }

    /**
     * SCOPE: Filter berdasarkan kategori (by slug)
     */
    public function scopeByCategory($query, string $slug)
    {
        return $query->whereHas('category', function ($q) use ($slug) {
            // ↑ whereHas() = filter berdasarkan relasi
            //   "Ambil produk yang PUNYA kategori dengan slug = ..."
            $q->where('slug', $slug);
        });
    }
}
