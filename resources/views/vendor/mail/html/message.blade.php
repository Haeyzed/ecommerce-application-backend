@php
$isTenant = app()->bound('tenant');

if ($isTenant) {
$settings = \App\Models\Tenant\Setting::query()->first();
} else {
$settings = \App\Models\Central\Setting::query()->first();
}

$storeName = $settings?->name ?? config('app.name');

$logoUrl = null;
if ($settings) {
// Check if hasMedia exists and the logo is actually present before fetching
if (method_exists($settings, 'hasMedia') && $settings->hasMedia('logo')) {
$logoUrl = $settings->getFirstMediaUrl('logo');
} else {
$logoUrl = $settings->logo_path ?? null;
}
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
