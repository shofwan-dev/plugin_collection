# CF7 to WhatsApp Gateway Plugin

Plugin WordPress untuk mengirim data dari Contact Form 7 ke WhatsApp menggunakan MPWA Gateway API.

## Fitur

✅ **Notifikasi Admin** - Terima pesan WhatsApp otomatis saat ada submission form  
✅ **Multiple Admin Numbers** - Tambahkan beberapa nomor admin untuk menerima notifikasi  
✅ **Konfirmasi User** - Kirim pesan konfirmasi ke user yang mengisi form  
✅ **Template Pesan** - Customize template pesan dengan placeholder dinamis  
✅ **Logging Lengkap** - Track semua pesan yang dikirim dengan detail  
✅ **Resend Feature** - Kirim ulang pesan yang gagal langsung dari logs  
✅ **Statistik** - Dashboard statistik pengiriman pesan  
✅ **Test Message** - Fitur test koneksi WhatsApp  

## Persyaratan

- WordPress 5.0 atau lebih tinggi
- PHP 7.2 atau lebih tinggi
- Plugin Contact Form 7 terinstall dan aktif
- Akun MPWA dari [mpwa.mutekar.com](https://mpwa.mutekar.com)
- API Key dan Sender Number dari MPWA

## Instalasi

1. **Upload Plugin**
   - Upload folder `cf7-to-whatsapp` ke direktori `/wp-content/plugins/`
   - Atau install melalui WordPress admin → Plugins → Add New → Upload Plugin

2. **Aktivasi**
   - Aktifkan plugin melalui menu 'Plugins' di WordPress admin

3. **Konfigurasi**
   - Buka menu **CF7 to WhatsApp** → **Settings**
   - Masukkan **API Key** dan **Sender Number** dari MPWA
   - Konfigurasi pengaturan notifikasi
   - Customize template pesan sesuai kebutuhan
   - Klik **Save Settings**

4. **Test Koneksi**
   - Gunakan fitur **Test Message** di sidebar untuk test koneksi
   - Masukkan nomor WhatsApp test
   - Klik **Send Test Message**

## Konfigurasi

### API Configuration

- **API Key**: API key dari akun MPWA Anda
- **Sender Number**: Nomor WhatsApp yang terdaftar di MPWA (format: 62888xxxx)

### Notification Settings

- **Admin Notification**: Enable/disable notifikasi ke admin
- **Admin WhatsApp Number**: Nomor WhatsApp admin yang menerima notifikasi
- **User Notification**: Enable/disable konfirmasi ke user
- **User Phone Field Name**: Nama field nomor telepon di Contact Form 7 (default: `phone`)

### Message Templates

Gunakan placeholder berikut dalam template pesan:

- `{form_title}` - Judul form yang disubmit
- `{submission_date}` - Tanggal dan waktu submission
- `{all_fields}` - Semua field form yang terformat rapi
- `{field_name}` - Field spesifik dari form (contoh: `{name}`, `{email}`, `{phone}`)

**Contoh Template Admin:**
```
New form submission received:

{all_fields}

Form: {form_title}
Submitted at: {submission_date}
```

**Contoh Template User:**
```
Terima kasih {name}!

Kami telah menerima data Anda dan akan segera menghubungi Anda.

Data yang Anda kirim:
{all_fields}

Hormat kami,
Tim Support
```

## Cara Penggunaan

1. **Pastikan Contact Form 7 sudah terinstall**

2. **Buat atau edit Contact Form 7**
   - Jika ingin mengirim konfirmasi ke user, pastikan ada field untuk nomor telepon
   - Contoh: `[tel* phone placeholder "Nomor WhatsApp"]`

3. **Plugin akan otomatis bekerja**
   - Setiap kali ada submission, pesan WhatsApp akan dikirim sesuai konfigurasi
   - Cek logs di menu **CF7 to WhatsApp** → **Logs**

## Format Nomor Telepon

Gunakan format internasional **tanpa tanda +**:

- Indonesia: `62888xxxx` (bukan `08888xxxx` atau `+62888xxxx`)
- USA: `1555xxxx`
- Malaysia: `60123xxxx`

Plugin akan otomatis membersihkan nomor dari spasi, tanda hubung, dll.

## Logs & Monitoring

Akses **CF7 to WhatsApp** → **Logs** untuk:

- Melihat semua pesan yang terkirim
- Filter berdasarkan status (success/failed)
- Statistik pengiriman
- Detail response dari API

**Statistik yang ditampilkan:**
- Total Messages
- Successful
- Failed
- Success Rate
- Today's Messages

## Troubleshooting

### Pesan tidak terkirim

1. **Cek API Configuration**
   - Pastikan API Key dan Sender Number sudah benar
   - Test koneksi dengan fitur Test Message

2. **Cek Nomor Telepon**
   - Pastikan format nomor sudah benar (62888xxxx)
   - Nomor harus terdaftar di WhatsApp

3. **Cek Logs**
   - Lihat error message di halaman Logs
   - Periksa response dari API

### User tidak menerima konfirmasi

1. **Pastikan User Notification sudah enabled**
2. **Cek nama field phone**
   - Nama field di CF7 harus sesuai dengan setting "User Phone Field Name"
   - Default: `phone`
3. **Pastikan user mengisi nomor dengan benar**

## API Documentation

Plugin ini menggunakan MPWA API. Dokumentasi lengkap:
- [MPWA API Docs](https://mpwa.mutekar.com)

### Endpoint yang digunakan:

1. **Send Text Message**
   - Endpoint: `https://mpwa.mutekar.com/send-message`
   - Method: POST
   - Untuk mengirim pesan teks

2. **Send Media Message** (untuk pengembangan future)
   - Endpoint: `https://mpwa.mutekar.com/send-media`
   - Method: POST
   - Untuk mengirim gambar, video, dokumen

3. **Check Number** (untuk pengembangan future)
   - Endpoint: `https://mpwa.mutekar.com/check-number`
   - Method: POST
   - Untuk validasi nomor WhatsApp

## Struktur File

```
cf7-to-whatsapp/
├── cf7-to-whatsapp.php          # Main plugin file
├── readme.txt                    # WordPress plugin readme
├── README.md                     # Documentation
├── includes/
│   ├── class-cf7-to-wa-admin.php    # Admin settings class
│   ├── class-cf7-to-wa-api.php      # WhatsApp API integration
│   ├── class-cf7-to-wa-handler.php  # CF7 submission handler
│   └── class-cf7-to-wa-logger.php   # Logging system
├── templates/
│   ├── admin-settings.php       # Settings page template
│   └── admin-logs.php          # Logs page template
└── assets/
    ├── css/
    │   └── admin.css           # Admin styles
    └── js/
        └── admin.js            # Admin scripts
```

## Changelog

### Version 1.0.0
- Initial release
- Admin notifications
- User confirmations
- Customizable message templates
- Comprehensive logging system
- Statistics dashboard
- Test message functionality

## Support

Untuk bantuan dan support:
- Email: support@example.com
- Website: https://example.com

## License

GPL v2 or later

## Credits

Developed by: Mutekar Digital Solutions  
WhatsApp Gateway: [MPWA](https://mpwa.mutekar.com)
