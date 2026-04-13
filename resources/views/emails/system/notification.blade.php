<x-mail::message>
# {{ $title }}

{{ $messageContent }}

@if($actionUrl)
<x-mail::button :url="$actionUrl">
Lihat Detail
</x-mail::button>
@endif

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
