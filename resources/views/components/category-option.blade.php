<option value="{{ $category->id }}">
    {!! $prefix !!} {{ $category->name }}
</option>

@if ($category->activeSubcategories->count() > 0)
    @foreach ($category->activeSubcategories as $subcategory)
        @include('components.category-option', ['category' => $subcategory, 'prefix' => $prefix . '-- '])
    @endforeach
@endif
