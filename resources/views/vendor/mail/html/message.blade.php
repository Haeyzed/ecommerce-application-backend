@php
$settings = \App\Models\Tenant\Setting::query()->first();
$storeName = $settings?->name ?? config('app.name');

// Safely attempt to get the Spatie Media URL, falling back to the old logo_path if needed
$logoUrl = null;
if ($settings) {
$logoUrl = method_exists($settings, 'getFirstMediaUrl')
? $settings->getFirstMediaUrl('logo')
: $settings->logo_path;
}
@endphp

<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="url('/')">
@if($logoUrl)
<img src="{{ $logoUrl }}" class="logo" alt="{{ $storeName }}" height="50" style="max-height: 50px; object-fit: contain;" />
@else
{{ $storeName }}
@endif
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $storeName }}. @lang('All rights reserved.')
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
