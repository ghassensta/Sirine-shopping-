@extends('front-office.layouts.app')

@section('title', $blog->meta_title ?? $blog->title . ' - Sirine Shopping')

@section('meta')
    {{-- ══ SEO Essentiels ══ --}}
    <meta name="description" content="{{ $blog->meta_description ?? Str::limit(strip_tags($blog->resume ?? $blog->description), 155) }}">
    <meta name="keywords" content="blog déco Tunisie, {{ $blog->title }}, décoration intérieure, conseils déco, Sirine Shopping">
    <meta name="author" content="Sirine Shopping">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ══ Hreflang ══ --}}
    <link rel="alternate" href="{{ url()->current() }}" hreflang="fr-tn">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default">

    {{-- ══ Open Graph (article) ══ --}}
    <meta property="og:locale"              content="fr_TN">
    <meta property="og:type"                content="article">
    <meta property="og:site_name"           content="Sirine Shopping">
    <meta property="og:title"              content="{{ $blog->meta_title ?? $blog->title }}">
    <meta property="og:description"        content="{{ $blog->meta_description ?? Str::limit(strip_tags($blog->resume ?? $blog->description), 155) }}">
    <meta property="og:url"                content="{{ url()->current() }}">
    <meta property="og:image"              content="{{ $blog->image ? asset('storage/' . $blog->image) : asset('assets/img/og-image-sirine.jpg') }}">
    <meta property="og:image:width"        content="1200">
    <meta property="og:image:height"       content="630">
    <meta property="og:image:alt"          content="{{ $blog->title }} - Sirine Shopping">

    {{-- ══ Open Graph Article (dates) ══ --}}
    <meta property="article:published_time" content="{{ $blog->created_at->toIso8601String() }}">
    <meta property="article:modified_time"  content="{{ $blog->updated_at->toIso8601String() }}">
    <meta property="article:author"         content="Sirine Shopping">
    <meta property="article:section"        content="Décoration Intérieure">

    {{-- ══ Twitter Card ══ --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $blog->meta_title ?? $blog->title }}">
    <meta name="twitter:description" content="{{ $blog->meta_description ?? Str::limit(strip_tags($blog->resume ?? $blog->description), 155) }}">
    <meta name="twitter:image"       content="{{ $blog->image ? asset('storage/' . $blog->image) : asset('assets/img/og-image-sirine.jpg') }}">

    {{-- ══ Schema.org BlogPosting ══ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ addslashes($blog->meta_title ?? $blog->title) }}",
        "description": "{{ addslashes($blog->meta_description ?? Str::limit(strip_tags($blog->resume ?? $blog->description ?? ''), 155)) }}",
        "image": "{{ $blog->image ? asset('storage/' . $blog->image) : asset('assets/img/og-image-sirine.jpg') }}",
        "url": "{{ url()->current() }}",
        "datePublished": "{{ $blog->created_at->toIso8601String() }}",
        "dateModified": "{{ $blog->updated_at->toIso8601String() }}",
        "inLanguage": "fr-TN",
        "author": {
            "@type": "Organization",
            "name": "Sirine Shopping",
            "url": "{{ url('/') }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Sirine Shopping",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('assets/img/logo-sirine.png') }}"
            }
        },
        "isPartOf": {
            "@type": "Blog",
            "name": "Blog Déco Sirine Shopping",
            "url": "{{ route('allblogs') }}"
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}"
        }
    }
    </script>

    {{-- ══ Schema.org BreadcrumbList ══ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Accueil",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Blog",
                "item": "{{ route('allblogs') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes(Str::limit($blog->title, 60)) }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light py-3">
        <div class="container mx-auto px-4">
            <nav class="flex text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="/" class="text-gray-500 hover:text-primary transition">Accueil</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ route('allblogs') }}" class="text-gray-500 hover:text-primary transition">Blog</a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <span class="text-dark font-medium">{{ Str::limit($blog->title, 40) }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Article Header -->
    <article class="bg-white">
        <div class="container mx-auto px-4 py-8 sm:py-12">
            <div class="max-w-4xl mx-auto">
                <!-- Article Image -->
                @if($blog->image)
                    <div class="mb-8">
                        <img src="{{ asset('storage/' . $blog->image) }}"
                             alt="{{ $blog->title }}"
                             class="w-full h-64 sm:h-80 md:h-96 object-cover rounded-lg shadow-lg">
                    </div>
                @endif

                <!-- Article Meta -->
                <div class="mb-6">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                        <time datetime="{{ $blog->created_at->format('Y-m-d') }}">
                            {{ $blog->created_at->format('d M Y') }}
                        </time>
                        @if($blog->updated_at && $blog->updated_at->ne($blog->created_at))
                            <span class="mx-2">•</span>
                            <span>Mis à jour le {{ $blog->updated_at->format('d M Y') }}</span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        {{ $blog->title }}
                    </h1>

                    @if($blog->excerpt)
                        <p class="text-lg text-gray-600 leading-relaxed">
                            {{ $blog->excerpt }}
                        </p>
                    @endif
                </div>

                <!-- Article Content -->
                <div class="prose prose-lg max-w-none mb-12">
                    {!! $blog->description !!}
                </div>

                <!-- Share Buttons -->
                <div class="border-t border-gray-200 pt-8 mb-12">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Partager cet article</h3>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fab fa-facebook-f mr-2"></i>
                            Facebook
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . url()->current()) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                            <i class="fab fa-whatsapp mr-2"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Articles -->
    @if($relatedBlogs->count() > 0)
        <section class="bg-gray-50 py-12 sm:py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                        Articles similaires
                    </h2>
                    <div class="w-20 h-1 bg-primary mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($relatedBlogs as $relatedBlog)
                        <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 overflow-hidden">
                            @if($relatedBlog->image)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ asset('storage/' . $relatedBlog->image) }}"
                                         alt="{{ $relatedBlog->title ?? 'Article blog décoration' }}"
                                         title="{{ $relatedBlog->title ?? 'Article blog décoration' }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="text-sm text-gray-500 mb-2">
                                    {{ $relatedBlog->created_at->format('d M Y') }}
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-3 line-clamp-2">
                                    <a href="{{ route('preview-blog', $relatedBlog->slug) }}"
                                       class="hover:text-primary transition">
                                        {{ $relatedBlog->title }}
                                    </a>
                                </h3>

                                @if($relatedBlog->excerpt)
                                    <p class="text-gray-600 text-sm line-clamp-3">
                                        {{ Str::limit($relatedBlog->excerpt, 100) }}
                                    </p>
                                @endif

                                <a href="{{ route('preview-blog', $relatedBlog->slug) }}"
                                   class="inline-flex items-center mt-4 text-primary hover:text-secondary transition">
                                    <span class="text-sm font-medium">Lire la suite</span>
                                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('js')
<script>
    // Smooth scroll for anchor links if any
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
