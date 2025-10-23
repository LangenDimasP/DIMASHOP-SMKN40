@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">inventory</span>
                    Kelola Produk
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Tambah, edit, dan hapus produk</p>
            </div>

            <!-- Add Product Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons text-cyan-500 mr-2">add_box</span>
                        Tambah Produk Baru
                    </h2>
                </div>
                
                <form id="form-produk" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                            <input type="text" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="Contoh: Indomie Goreng">
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="price" required
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                            <input type="number" name="stock" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="0">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                            <select name="category" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <option value="">Pilih Kategori</option>
                                <option value="Makanan & Minuman">Makanan & Minuman</option>
                                <option value="Susu & Produk Olahan">Susu & Produk Olahan</option>
                                <option value="Bumbu & Sembako">Bumbu & Sembako</option>
                                <option value="Kesehatan & Obat Ringan">Kesehatan & Obat Ringan</option>
                                <option value="Perawatan Tubuh">Perawatan Tubuh</option>
                                <option value="Perawatan Rumah Tangga">Perawatan Rumah Tangga</option>
                                <option value="Perlengkapan Sekolah & Kantor">Perlengkapan Sekolah & Kantor</option>
                                <option value="Elektronik & Aksesoris">Elektronik & Aksesoris</option>
                                <option value="Mainan & Hobi">Mainan & Hobi</option>
                                <option value="Makanan Beku & Siap Saji">Makanan Beku & Siap Saji</option>
                            </select>
                        </div>

                        <!-- Discount Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                            <select name="discount_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Fixed (Rp)</option>
                            </select>
                        </div>

                        <!-- Discount Value -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Diskon</label>
                            <input type="number" name="discount_value"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="0">
                        </div>
                        <!-- Unique Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode Produk</label>
                            <input type="text" id="kode-produk" name="unique_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="Scan barcode di sini atau auto generate jika kosong">
                        </div>

                        <!-- Promo Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Promo</label>
                            <select name="promo_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <option value="">No Promo</option>
                                <option value="buy_x_get_y_free">Buy X Get Y Free</option>
                                <option value="buy_x_for_y">Buy X For Y Rupiah</option>
                            </select>
                        </div>

                        <!-- Promo Buy -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Promo Buy (X)</label>
                            <input type="number" name="promo_buy"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="0">
                        </div>

                        <!-- Promo Get -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Promo Get (Y)</label>
                            <input type="number" name="promo_get"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                   placeholder="0">
                        </div>

                        <!-- Description (Full Width) -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                      placeholder="Deskripsi produk"></textarea>
                        </div>

                        <!-- Main Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Utama</label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>

                        <!-- Multiple Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Tambahan</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>

                        <!-- Promo Active Checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" name="promo_active" value="1" id="promo_active"
                                   class="w-4 h-4 text-cyan-500 border-gray-300 rounded focus:ring-cyan-500">
                            <label for="promo_active" class="ml-2 text-sm font-medium text-gray-700">Promo Aktif</label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center">
                            <span class="material-icons text-sm mr-2">add</span>
                            Tambah Produk
                        </button>
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons text-cyan-500 mr-2">list</span>
                        Daftar Produk
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Diskon</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Promo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50" id="product-row-{{ $product->id }}">
                                    <!-- Product Info -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="material-icons text-gray-400">image</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" value="{{ $product->name }}"
                                                       onchange="updateProduct({{ $product->id }}, 'name', this.value)"
                                                       class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm font-medium">
                                                <textarea onchange="updateProduct({{ $product->id }}, 'description', this.value)"
                                                          class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs text-gray-600 mt-1"
                                                          rows="2" placeholder="Deskripsi">{{ $product->description }}</textarea>
                                                
                                                <!-- Main Image Upload -->
                                                <div class="mt-2">
                                                    <label class="text-xs text-gray-600 block mb-1">Gambar Utama:</label>
                                                    <input type="file" onchange="updateProductImage({{ $product->id }}, this.files[0])"
                                                           class="text-xs w-full" accept="image/*">
                                                </div>

                                                <!-- Additional Images Upload -->
                                                <div class="mt-2">
                                                    <label class="text-xs text-gray-600 block mb-1">Gambar Tambahan:</label>
                                                    @if($product->images && count($product->images) > 0)
                                                        <div class="flex flex-wrap gap-1 mb-2">
                                                            @foreach($product->images as $img)
                                                                <div class="w-10 h-10 bg-gray-100 rounded overflow-hidden border border-gray-200">
                                                                    <img src="{{ asset('storage/' . $img) }}" alt="Additional" class="w-full h-full object-cover">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <input type="file" name="images[]" multiple onchange="updateProductImages({{ $product->id }}, this.files)"
                                                           class="text-xs w-full" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="px-4 py-4">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-xs text-gray-500">Rp</span>
                                            <input type="number" value="{{ $product->price }}"
                                                   onchange="updateProduct({{ $product->id }}, 'price', this.value)"
                                                   class="w-28 pl-7 pr-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm">
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ number_format($product->price, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-4 py-4">
                                        <input type="number" value="{{ $product->stock }}"
                                               onchange="updateProduct({{ $product->id }}, 'stock', this.value)"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm">
                                    </td>

                                    <!-- Discount -->
                                    <td class="px-4 py-4">
                                        <select onchange="updateProduct({{ $product->id }}, 'discount_type', this.value)"
                                                class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs mb-1">
                                            <option value="percent" {{ $product->discount_type == 'percent' ? 'selected' : '' }}>%</option>
                                            <option value="fixed" {{ $product->discount_type == 'fixed' ? 'selected' : '' }}>Rp</option>
                                        </select>
                                        <input type="number" value="{{ $product->discount_value }}"
                                               onchange="updateProduct({{ $product->id }}, 'discount_value', this.value)"
                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm"
                                               placeholder="0">
                                    </td>

                                    <!-- Category -->
                                    <td class="px-4 py-4">
                                        <select onchange="updateProduct({{ $product->id }}, 'category', this.value)"
                                                class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs">
                                            <option value="">Pilih</option>
                                            <option value="Makanan & Minuman" {{ $product->category == 'Makanan & Minuman' ? 'selected' : '' }}>Makanan & Minuman</option>
                                            <option value="Susu & Produk Olahan" {{ $product->category == 'Susu & Produk Olahan' ? 'selected' : '' }}>Susu & Produk Olahan</option>
                                            <option value="Bumbu & Sembako" {{ $product->category == 'Bumbu & Sembako' ? 'selected' : '' }}>Bumbu & Sembako</option>
                                            <option value="Kesehatan & Obat Ringan" {{ $product->category == 'Kesehatan & Obat Ringan' ? 'selected' : '' }}>Kesehatan & Obat Ringan</option>
                                            <option value="Perawatan Tubuh" {{ $product->category == 'Perawatan Tubuh' ? 'selected' : '' }}>Perawatan Tubuh</option>
                                            <option value="Perawatan Rumah Tangga" {{ $product->category == 'Perawatan Rumah Tangga' ? 'selected' : '' }}>Perawatan Rumah Tangga</option>
                                            <option value="Perlengkapan Sekolah & Kantor" {{ $product->category == 'Perlengkapan Sekolah & Kantor' ? 'selected' : '' }}>Perlengkapan Sekolah & Kantor</option>
                                            <option value="Elektronik & Aksesoris" {{ $product->category == 'Elektronik & Aksesoris' ? 'selected' : '' }}>Elektronik & Aksesoris</option>
                                            <option value="Mainan & Hobi" {{ $product->category == 'Mainan & Hobi' ? 'selected' : '' }}>Mainan & Hobi</option>
                                            <option value="Makanan Beku & Siap Saji" {{ $product->category == 'Makanan Beku & Siap Saji' ? 'selected' : '' }}>Makanan Beku & Siap Saji</option>
                                        </select>
                                    </td>

                                    <!-- Code -->
<td class="px-4 py-4">
    <input type="text" value="{{ $product->unique_code }}"
           onchange="updateProduct({{ $product->id }}, 'unique_code', this.value)"
           class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs font-mono">
</td>

                                    <!-- Promo -->
                                    <td class="px-4 py-4">
                                        <select onchange="updateProduct({{ $product->id }}, 'promo_type', this.value)"
                                                class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs mb-1">
                                            <option value="" {{ !$product->promo_type ? 'selected' : '' }}>No Promo</option>
                                            <option value="buy_x_get_y_free" {{ $product->promo_type == 'buy_x_get_y_free' ? 'selected' : '' }}>Buy X Get Y</option>
                                            <option value="buy_x_for_y" {{ $product->promo_type == 'buy_x_for_y' ? 'selected' : '' }}>Buy X For Y</option>
                                        </select>
                                        <div class="flex gap-1">
                                            <input type="number" value="{{ $product->promo_buy }}"
                                                   onchange="updateProduct({{ $product->id }}, 'promo_buy', this.value)"
                                                   class="w-16 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs"
                                                   placeholder="X">
                                            <input type="number" value="{{ $product->promo_get }}"
                                                   onchange="updateProduct({{ $product->id }}, 'promo_get', this.value)"
                                                   class="w-16 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-cyan-500 text-xs"
                                                   placeholder="Y">
                                        </div>
                                        <label class="flex items-center mt-1 text-xs">
                                            <input type="checkbox" {{ $product->promo_active ? 'checked' : '' }}
                                                   onchange="updateProduct({{ $product->id }}, 'promo_active', this.checked ? 1 : 0)"
                                                   class="w-3 h-3 text-cyan-500 border-gray-300 rounded focus:ring-cyan-500 mr-1">
                                            <span class="text-gray-600">Aktif</span>
                                        </label>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                              onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 font-medium text-sm flex items-center">
                                                <span class="material-icons text-sm mr-1">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateProduct(id, field, value) {
            fetch('/admin/products/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ [field]: value, '_method': 'PATCH' })
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error('HTTP ' + res.status + ': ' + text); });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Update berhasil');
                } else {
                    showNotification('Error: ' + (data.message || 'Update failed'), 'error');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                showNotification('Error: ' + err.message, 'error');
            });
        }

        function updateProductImage(id, file) {
            let formData = new FormData();
            formData.append('image', file);
            formData.append('_method', 'PATCH');

            fetch('/admin/products/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Gambar berhasil diupdate');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + (data.message || 'Update failed'), 'error');
                }
            })
            .catch(err => {
                showNotification('Error: ' + err.message, 'error');
            });
        }

        function updateProductImages(id, files) {
            let formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            formData.append('_method', 'PATCH');

            fetch('/admin/products/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Gambar berhasil diupdate');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + (data.message || 'Update failed'), 'error');
                }
            })
            .catch(err => {
                showNotification('Error: ' + err.message, 'error');
            });
        }

        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? 'check_circle' : 'error';
            
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-slide-up`;
            notification.innerHTML = `
                <span class="material-icons text-sm">${icon}</span>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(100%)';
                notification.style.transition = 'all 0.3s';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeProdukInput = document.getElementById('kode-produk');
    const formProduk = document.getElementById('form-produk');
    const btnTambahProduk = document.getElementById('btn-tambah-produk');

    // Prevent form submit on Enter in kode produk input
    kodeProdukInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Optional: bisa tambahkan validasi atau fetch data produk di sini
        }
    });

    // Form hanya submit saat tombol diklik
    btnTambahProduk.addEventListener('click', function(e) {
        // Validasi jika perlu
        // if (!kodeProdukInput.value) { alert('Kode produk harus diisi'); return; }
        formProduk.submit();
    });
});
</script>

    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }

        /* Hide number input spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
@endsection