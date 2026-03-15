@if(!empty($optimizedImages))
    <img 
        src="{{ $src }}" 
        srcset="{{ $srcset }}" 
        sizes="{{ $sizes }}" 
        alt="{{ $product->name }}" 
        class="{{ $class }}" 
        loading="{{ $loading }}" 
        width="400" 
        height="400" 
        decoding="async" 
        fetchpriority="{{ $loading === 'eager' ? 'high' : 'auto' }}"
    >
@else
    <img 
        src="{{ $src }}" 
        alt="{{ $product->name }}" 
        class="{{ $class }}" 
        loading="{{ $loading }}" 
        width="400" 
        height="400" 
        decoding="async"
    >
@endif