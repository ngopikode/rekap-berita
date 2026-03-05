<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} di {{ $restaurant->name }}</title>

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $product->name }} di {{ $restaurant->name }}"/>
    <meta property="og:description" content="{{ $product->description }}"/>
    <meta property="og:image" content="{{ $image_url }}"/>
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="{{ $restaurant->name }}"/>

    <!-- Optional but helps WhatsApp -->
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->name }} di {{ $restaurant->name }}">
    <meta name="twitter:description" content="{{ $product->description }}">
    <meta name="twitter:image" content="{{ $image_url }}">
</head>
<body>
<h1>Memuat {{ $product->name }}...</h1>
</body>
</html>
