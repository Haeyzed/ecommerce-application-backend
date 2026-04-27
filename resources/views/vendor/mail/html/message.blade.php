<<<<<<< HEAD
<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
=======
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
>>>>>>> 6caccb9378d91cb4e0c71d86d91fe152d9f6bd5a
</x-mail::header>
</x-slot:header>

{{-- Body --}}
<<<<<<< HEAD
{!! $slot !!}
=======
{{ $slot }}
>>>>>>> 6caccb9378d91cb4e0c71d86d91fe152d9f6bd5a

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
<<<<<<< HEAD
{!! $subcopy !!}
=======
{{ $subcopy }}
>>>>>>> 6caccb9378d91cb4e0c71d86d91fe152d9f6bd5a
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<<<<<<< HEAD
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
=======
© {{ date('Y') }} {{ $storeName }}. @lang('All rights reserved.')
>>>>>>> 6caccb9378d91cb4e0c71d86d91fe152d9f6bd5a
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
