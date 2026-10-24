@props([
    'title' => '',
    'description' => '',
    'type' => '',
    'image' => '',
    'robots' => '',
    'publishedTime' => '',
    'author' => '',
])

@php
    $shareTitle = trim($title) !== '' ? trim($title) : \App\Support\Seo::title(null);
    $metaDescription = \App\Support\Seo::description($description);
    $shareImage = \App\Support\Seo::image($image);
    $canonical = \App\Support\Seo::canonical(request());
@endphp

<title>{{ \App\Support\Seo::title($title) }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $robots !== '' ? $robots : 'index, follow' }}">
<link rel="canonical" href="{{ $canonical }}">

{{-- OpenGraph --}}
<meta property="og:site_name" content="{{ config('cbngo.organization.short_name') }}">
<meta property="og:locale" content="pt_BR">
<meta property="og:type" content="{{ $type !== '' ? $type : 'website' }}">
<meta property="og:title" content="{{ $shareTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $shareImage }}">
@if ($publishedTime !== '')
<meta property="article:published_time" content="{{ $publishedTime }}">
@endif
@if ($author !== '')
<meta property="article:author" content="{{ $author }}">
@endif

{{-- Twitter Cards --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $shareTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $shareImage }}">
