@php
    // If the level is not set, default it to 0
    $level = $level ?? 0;
@endphp

<option value="{{ $category['id'] }}">
    @php
        // Indentation based on the level of subcategory
        $indentation = str_repeat('--', $level);
    @endphp
    {{ $indentation }} {{ $category['name'] }}
</option>

@if(!empty($category['subcategories']))
    @foreach($category['subcategories'] as $subcategory)
        @include('products.partials.category-option', ['category' => $subcategory, 'level' => $level + 1])
    @endforeach
@endif
