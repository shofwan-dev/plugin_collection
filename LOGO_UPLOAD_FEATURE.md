# Logo Upload Feature Documentation

## Fitur Upload Logo

### Lokasi
Admin Panel → Settings → General Settings → Branding

### Fitur yang Ditambahkan

#### **Upload Logo**
- Field: `site_logo`
- Format: JPG, JPEG, PNG, SVG
- Ukuran Maksimal: 2MB
- Lokasi Penyimpanan: `storage/app/public/logos/`
- Preview: Real-time preview saat memilih file
- **Multi-Purpose**: Logo digunakan untuk navbar, footer, DAN favicon

### Sinkronisasi Logo

Logo akan otomatis ditampilkan di:

#### 1. **Navbar (Header)**
- Logo ditampilkan di navbar brand
- Jika tidak ada logo, tampilkan icon WhatsApp + Site Name
- Tinggi logo: 40px (auto width)

#### 2. **Footer**
- Logo ditampilkan di footer (dengan filter invert untuk dark background)
- Jika tidak ada logo, tampilkan icon WhatsApp + Site Name
- Tinggi logo: 30px (auto width)

#### 3. **Favicon (Browser Tab)**
- Logo yang sama digunakan sebagai favicon
- Jika tidak ada logo, gunakan emoji default 💬
- Browser akan otomatis resize logo untuk favicon

### File yang Dimodifikasi

1. **Controller**: `app/Http/Controllers/Admin/SettingController.php`
   - Menambahkan validasi upload file
   - Handle upload dan delete old files
   - Menyimpan path file ke database
   - **Removed**: Favicon field handling

2. **View**: `resources/views/admin/settings/index.blade.php`
   - Menambahkan form upload dengan `enctype="multipart/form-data"`
   - Field upload logo (single field)
   - Preview image dengan JavaScript
   - Menampilkan current logo
   - **Removed**: Favicon upload field

3. **Layout**: `resources/views/layouts/public.blade.php`
   - Dynamic favicon menggunakan `site_logo`
   - Dynamic logo di navbar
   - Dynamic logo di footer
   - Dynamic site name dan description

### Cara Menggunakan

#### Upload Logo Baru:
1. Login sebagai Admin
2. Buka **Admin → Settings**
3. Scroll ke bagian **Branding**
4. Klik **Choose File** pada field **Site Logo**
5. Pilih file gambar (PNG, JPG, atau SVG)
6. Preview akan muncul otomatis
7. Klik **Save Settings**
8. Logo akan langsung tampil di:
   - ✅ Navbar
   - ✅ Footer
   - ✅ Favicon (browser tab)

### Fitur Tambahan

#### 1. **Auto Delete Old Files**
Saat upload logo baru, file lama akan otomatis dihapus dari storage untuk menghemat space.

#### 2. **Image Preview**
JavaScript function `previewImage()` memberikan preview real-time sebelum upload.

#### 3. **Fallback System**
- Jika logo tidak ada: tampilkan icon + text
- Logo yang sama digunakan untuk favicon
- Jika site name tidak ada: gunakan "CF7 to WhatsApp"

#### 4. **Multi-Purpose Logo**
Satu logo untuk semua kebutuhan:
- Navbar branding
- Footer branding
- Browser favicon (auto-resized)

### Database Schema

Logo disimpan di tabel `settings` dengan:
- **Key**: `site_logo`
- **Value**: Path file (contoh: `logos/abc123.png`)
- **Type**: `string`
- **Group**: `general`

### Storage Path

File disimpan di:
```
storage/app/public/logos/
```

Dan dapat diakses via:
```
public/storage/logos/
```

### Validasi

```php
'site_logo' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048'
```

### Testing

1. ✅ Upload logo PNG - Success
2. ✅ Upload logo JPG - Success
3. ✅ Upload logo SVG - Success
4. ✅ Preview image - Success
5. ✅ Delete old file - Success
6. ✅ Display in navbar - Success
7. ✅ Display in footer - Success
8. ✅ Display as favicon - Success
9. ✅ Fallback system - Success

### Troubleshooting

#### Logo tidak muncul?
1. Pastikan `php artisan storage:link` sudah dijalankan
2. Cek permission folder `storage/app/public/logos/`
3. Cek apakah file berhasil diupload di folder storage

#### File terlalu besar?
1. Logo maksimal 2MB
2. Compress image sebelum upload
3. Gunakan tools seperti TinyPNG atau ImageOptim

#### Preview tidak muncul?
1. Pastikan JavaScript enabled
2. Cek console browser untuk error
3. Pastikan file format didukung

#### Favicon tidak update?
1. Clear browser cache (Ctrl+F5)
2. Close dan reopen browser
3. Favicon mungkin di-cache oleh browser

### Best Practices

1. **Logo**:
   - Gunakan PNG dengan transparent background
   - Resolusi minimal: 200x50 pixels untuk navbar
   - Logo akan auto-resize untuk favicon
   - Maksimal: 2MB

2. **Format**:
   - **PNG**: Terbaik untuk logo dengan transparency
   - **SVG**: Scalable, bagus untuk semua ukuran
   - **JPG**: Untuk logo tanpa transparency

3. **File Naming**:
   - Gunakan nama file yang descriptive
   - Hindari spasi dan karakter khusus
   - Contoh: `company-logo.png`, `brand-logo.svg`

### Keuntungan Single Logo

1. **Simplicity**: Hanya perlu upload 1 file
2. **Consistency**: Logo sama di semua tempat
3. **Storage**: Hemat storage space
4. **Maintenance**: Lebih mudah manage
5. **Auto-Resize**: Browser otomatis resize untuk favicon

### Notes

- Logo yang sama digunakan untuk navbar, footer, dan favicon
- Browser akan otomatis resize logo untuk favicon
- Untuk hasil terbaik, gunakan logo square atau dengan aspect ratio seimbang
- SVG format recommended untuk scalability
