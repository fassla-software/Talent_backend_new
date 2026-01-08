<!-- resources/views/partials/category_row.blade.php -->

<tr>
    <td>{{ $category['id'] }}</td>
    <td>{{ str_repeat('—', $level) }} {{ $category['name'] }}</td>
    <td>{{ $category['points'] }}</td>
    <td>{{ $category['itemsFlag'] ? 'Yes' : 'No' }}</td>
</tr>

<!-- Check if this category has subcategories and if their product_flag is true -->
@foreach($category['subcategories'] as $subcategory)
    @include('partials.category_row', ['category' => $subcategory, 'level' => $level + 1])
@endforeach
