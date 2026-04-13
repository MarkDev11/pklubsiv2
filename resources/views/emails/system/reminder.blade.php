<x-mail::message>
# Peringatan: Batas Waktu Hampir Habis!

Halo **{{ $name }}**,

Berdasarkan catatan kami, Anda **belum melakukan pendaftaran atau submit identitas PKL**. 

Pendaftaran PKL akan ditutup secara otomatis pada:
## **{{ $deadline }}**

Kami sangat menyarankan Anda untuk segera mengambil tindakan ini. Jika melewati batas waktu tersebut, akses Anda akan dikunci.

<x-mail::button :url="route('login')">
Login & Daftar Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
