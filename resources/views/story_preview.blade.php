<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

<div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden relative">
    <!-- Image Header -->
    <div class="relative h-96">
        @if($image_url)
            <img src="{{ $image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div
            class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-gray-800 shadow-lg">
            📍 {{ $restaurant->name }}
        </div>
    </div>

    <!-- Content -->
    <div class="p-6 -mt-10 relative z-10 bg-white rounded-t-3xl">
        <div class="flex justify-between items-start mb-2">
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>
            <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full whitespace-nowrap ml-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
        </div>

        <p class="text-gray-600 text-sm leading-relaxed mb-6">
            {{ $product->description ?: 'Nikmati kelezatan menu spesial kami yang satu ini.' }}
        </p>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button onclick="handleShare()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                Share ke WhatsApp
            </button>

            <a href="{{ route('product.story.image', ['subdomain' => request()->route('subdomain'), 'productId' => request()->route('productId')]) }}"
               class="block w-full bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-800 font-bold py-3 px-4 rounded-xl text-center transition hover:bg-gray-50">
                Download Gambar Story
            </a>

            <a href="{{ $product_url }}"
               class="block w-full bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-800 font-bold py-3 px-4 rounded-xl text-center transition hover:bg-gray-50">
                Pesan
            </a>
        </div>
    </div>
</div>

<script>
    // Data dari Controller (PHP)
    const shareData = {
        title: "{{ addslashes($share_title) }}",
        text: `{!! json_encode($share_text) !!}`.slice(1, -1), // Hack untuk handle newline dari PHP ke JS string
        url: "{{ $product_url }}"
    };

    const handleShare = async () => {
        if (navigator.share) {
            try {
                await navigator.share(shareData);
            } catch (err) {
                console.error('Gagal membagikan:', err);
            }
        } else {
            // Fallback ke WhatsApp Web
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(shareData.text)}`;
            window.open(whatsappUrl, '_blank');
        }
    };
</script>
</body>
</html>
