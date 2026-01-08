<option value="{{ $category->id }}">
    {!! str_repeat('&nbsp;&nbsp;&nbsp;', $level) !!}{{-- Indentation --}}
    — {{ $category->name }}
</option>

@if ($category->subcategories->where('product_flag', 0)->count() > 0)
    @foreach ($category->subcategories->where('product_flag', 0) as $subcategory)
        @include('partials.category-option', ['category' => $subcategory, 'level' => $level + 1])
    @endforeach
@endif
