<x-mail::message>
# Reset Password

Halo,

Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses pengaturan ulang password:

## **{{ $otp }}**

Kode ini akan kedaluwarsa dalam **5 menit**. 

Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini. Akun Anda tetap aman.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
