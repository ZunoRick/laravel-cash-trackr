@props(['type' => 'success', 'message' => ''])

@php
    $colors = [
        'success' => 'border-green-700 text-green-700 bg-green-100',
        'error' => 'border-red-700 text-red-700 bg-red-100',
    ];
    $class = $colors[$type] ?? $colors['success'];
@endphp

@if ($message)
    <p class="my-10 text-center border-l-8 py-3 text-sm font-bold uppercase {{ $class }}">
        {{ $message }}</p>
@endif
