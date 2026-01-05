# Panduan Deployment

## Problem yang Diperbaiki
Error `EBADPLATFORM` untuk `lightningcss-win32-x64-msvc@1.30.2` terjadi karena dependency platform-specific Windows di-commit ke repository dan diinstall di server Linux.

## Solusi yang Diterapkan

### 1. **Membersihkan package.json**
- Menghapus `lightningcss-win32-x64-msvc` (platform-specific untuk Windows)
- Menghapus `laravel-mix` yang tidak digunakan (sudah pakai Vite)
- Mengorganisir dependencies dengan benar (dev dependencies vs runtime dependencies)

### 2. **Membuat .npmrc**
File konfigurasi npm untuk:
- Menonaktifkan optional dependencies yang platform-specific
- Menonaktifkan package-lock.json (lebih portable)

### 3. **Update .gitignore**
Menambahkan file-file yang tidak perlu di-commit:
- `package-lock.json`
- `yarn.lock`
- `.npm-cache`
- `/node_modules`

## Langkah Deployment di Server Linux

### 1. Pull perubahan terbaru
```bash
git pull origin main
```

### 2. Hapus node_modules dan package-lock.json lama (jika ada)
```bash
rm -rf node_modules
rm -f package-lock.json
```

### 3. Install dependencies
```bash
npm install
```

### 4. Build assets
```bash
npm run build
```

### 5. Set permissions (jika diperlukan)
```bash
chmod -R 755 public/build
```

## Catatan Penting

1. **Jangan commit package-lock.json** - File ini sudah ditambahkan ke .gitignore karena bisa menyebabkan conflict platform-specific dependencies antara Windows (development) dan Linux (production).

2. **Jangan commit node_modules** - Selalu install ulang di server.

3. **Gunakan npm install tanpa flag --production** - Karena Vite dan build tools ada di devDependencies, kita butuh semua dependencies untuk build.

4. **.npmrc sudah dikonfigurasi** untuk menghindari masalah platform-specific di masa depan.

## Troubleshooting

### Jika masih ada error EBADPLATFORM:
```bash
# Hapus cache npm
rm -rf ~/.npm

# Hapus node_modules dan install ulang
rm -rf node_modules
npm cache clean --force
npm install
```

### Jika build gagal:
```bash
# Pastikan Node.js versi >= 18
node -v

# Pastikan npm versi terbaru
npm -v

# Clear cache Vite
rm -rf node_modules/.vite
npm run build
```

## Workflow Development ke Production

1. **Development (Windows/Mac/Linux)**
   ```bash
   npm install
   npm run dev
   ```

2. **Before Commit**
   - Pastikan tidak ada file platform-specific di package.json
   - Pastikan package-lock.json tidak di-commit (sudah di .gitignore)

3. **Production (Linux Server)**
   ```bash
   git pull
   rm -rf node_modules
   npm install
   npm run build
   php artisan optimize
   ```

## File-file Penting

- **package.json** - Daftar dependencies yang platform-agnostic
- **.npmrc** - Konfigurasi npm untuk menghindari platform-specific deps
- **.gitignore** - Mengabaikan lock files dan node_modules
- **vite.config.js** - Konfigurasi build tool
