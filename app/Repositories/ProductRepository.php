<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\ProductRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\RecentView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Product::class;
    }

    public static function recentView(Product $product)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            RecentView::where('product_id', $product->id)->where('user_id', $user->id)->firstOrcreate([
                'product_id' => $product->id,
                'user_id' => $user->id,
            ])?->update(['updated_at' => now()]);
        }

        return $product;
    }

    /**
     * store new product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     *                                                      return \App\Models\Product
     */
    public static function storeByRequest(ProductRequest $request): Product
    {
        $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'thumbnail');

        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand,
            'unit_id' => $request->unit,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $request->quantity,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail->id,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
        ]);

        if ($request->is('api/*')) {
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
                $product->colors()->sync($colors);
            }
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }

            // sync tax
            $product->vatTaxes()->sync($request->taxs ?? []);
        }

        foreach ($request->additionThumbnail as $additionThumbnail) {
            $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
            $product->medias()->attach($thumbnail->id);
        }

        return $product;
    }

    /**
     * Update the product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     *                                                      return \App\Models\Product
     */
    public static function updateByRequest(ProductRequest $request, Product $product): Product
    {
        $thumbnail = $product->media;
        if ($request->hasFile('thumbnail') && $thumbnail) {
            $thumbnail = MediaRepository::updateByRequest(
                $request->thumbnail,
                'products',
                'image',
                $thumbnail
            );
        }

        if ($request->hasFile('thumbnail') && $thumbnail == null) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'image');
        }

        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->update_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        self::update($product, [
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand ?? null,
            'unit_id' => $request->unit ?? null,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $request->quantity,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => false,
            'is_approve' => $isAdmin ? true : $approve,
        ]);

        $product->colors()->detach();
        if ($request->is('api/*')) {
            $colors = [];
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
            }
            $product->colors()->attach($colors);
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }

            // sync tax
            $product->vatTaxes()->sync($request->taxs ?? []);
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        $product->sizes()->detach();
        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }
        }

        if ($request->is('api/*')) {
            self::updateAdditionThumbnails($request->previousThumbnail, $product);
        } else {
            foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
                $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
                $product->medias()->attach($thumbnail->id);
            }

            self::updatePreviousThumbnail($request->previousThumbnail);
        }

        return $product;
    }

    /**
     * store new product from bulk import.
     */
    public static function bulkItemStore($rows, $folders = null)
    {
        \Log::info('Bulk import started', [
            'rows_count' => count($rows),
            'folders_provided' => $folders !== null,
            'folders' => $folders
        ]);
        
        $invalidRows = [];

        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        $total = 0;

        $folders = $folders !== null ? array_keys($folders) : [];

        $galleryPath = 'gallery/shop'.$shop->id;
        
        // Get all available images from folders once
        $allAvailableImages = [];
        if (!empty($folders)) {
            foreach ($folders as $folder) {
                $folderPath = $galleryPath.'/'.$folder;
                if (Storage::disk('public')->exists($folderPath)) {
                    $files = File::files(Storage::disk('public')->path($folderPath));
                    foreach ($files as $file) {
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $allAvailableImages[] = $file->getPathname();
                        }
                    }
                }
            }
        }
        
        \Log::info('Gallery path setup', [
            'gallery_path' => $galleryPath,
            'folders_array' => $folders,
            'shop_id' => $shop->id,
            'total_images_found' => count($allAvailableImages)
        ]);

        $imageIndex = 0; // Track which image to assign to each product
        
        foreach ($rows as $row) {

            $createData = [];

            for ($i = 0; $i <= 13; $i++) {

                if ($i == 1) {
                    $createData['name'] = $row[$i];
                } elseif ($i == 2) {
                    $thumbnails = [];
                    
                    // If CSV has thumbnail names, use them
                    if (!empty($row[$i])) {
                        $explodeThumbnails = explode(',', $row[$i]);
                        foreach ($explodeThumbnails as $thumbnailName) {
                            $thumbnailName = trim($thumbnailName);
                            if (empty($thumbnailName)) continue;
                            
                            $foundFile = self::findThumbnailInFolders($thumbnailName, $folders, $galleryPath);
                            if ($foundFile) {
                                $thumbnails[] = $foundFile;
                            }
                        }
                    }
                    // If no thumbnail names in CSV but images available, assign one image per product
                    elseif (!empty($allAvailableImages)) {
                        if ($imageIndex < count($allAvailableImages)) {
                            $thumbnails[] = $allAvailableImages[$imageIndex];
                            \Log::info('Assigned image to product', [
                                'product_row' => $total + 1,
                                'image_index' => $imageIndex,
                                'assigned_image' => basename($allAvailableImages[$imageIndex])
                            ]);
                            $imageIndex++; // Move to next image for next product
                        }
                    }
                    $createData['thumbnails'] = $thumbnails;
                } elseif ($i == 3) {
                    $selectCategories = explode(',', $row[$i]);
                    $categorys = [];
                    foreach ($selectCategories as $categoryName) {

                        $category = $rootShop->categories()->where('name', $categoryName)->first();

                        if ($category) {
                            $categorys[] = $category->id;
                        }
                    }
                    $createData['categorys'] = $categorys;
                } elseif ($i == 4) {
                    $selectedSubCategories = explode(',', $row[$i]);
                    $subCategories = [];
                    foreach ($selectedSubCategories as $subCategoryName) {
                        $subCategory = $rootShop->subcategories()->where('name', $subCategoryName)->first();
                        if ($subCategory) {
                            $subCategories[] = $subCategory->id;
                        }
                    }
                    $createData['subCategories'] = $subCategories;
                } elseif ($i == 5) {
                    $brand = $rootShop->brands()->where('name', $row[$i])->first();
                    $createData['brand'] = $brand ? $brand->id : null;
                } elseif ($i == 6) {
                    $selectColors = explode(',', $row[$i]);
                    $colors = [];
                    foreach ($selectColors as $colorName) {
                        $color = $rootShop->colors()->where('name', $colorName)->first();
                        if ($color) {
                            $colors[] = $color->id;
                        }
                    }
                    $createData['colors'] = $colors;
                } elseif ($i == 7) {
                    $selectSizes = explode(',', $row[$i]);
                    $sizes = [];
                    foreach ($selectSizes as $sizeName) {
                        $size = $rootShop->sizes()->where('name', $sizeName)->first();
                        if ($size) {
                            $sizes[] = $size->id;
                        }
                    }
                    $createData['sizes'] = $sizes;
                } elseif ($i == 8) {
                    $createData['price'] = $row[$i];
                } elseif ($i == 9) {
                    $createData['discount_price'] = $row[$i];
                } elseif ($i == 10) {
                    $createData['sku'] = $row[$i];
                } elseif ($i == 11) {
                    $createData['stock_quantity'] = $row[$i];
                } elseif ($i == 12) {
                    $createData['short_description'] = $row[$i];
                } elseif ($i == 13) {
                    $createData['description'] = $row[$i];
                }
            }

            if ($createData['name'] != null && $createData['price'] != null && count($createData['categorys']) != 0) {

                if ($createData['price'] < $createData['discount_price']) {
                    $createData['discount_price'] = $createData['price'];
                }

                self::storeBulkProduct($createData);

                $total = $total + 1;
            }
        }

        return $total;
    }

    /**
     * store new product from bulk import.
     *
     * @return Product
     */
    private static function storeBulkProduct($data)
    {
        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        // Handle main thumbnail (first image)
        $mainThumbnail = !empty($data['thumbnails']) ? $data['thumbnails'][0] : null;
        $mainMediaId = self::storeMedia($mainThumbnail);
        
        // Debug logging
        \Log::info('Bulk import thumbnail debug', [
            'has_thumbnails' => !empty($data['thumbnails']),
            'thumbnail_count' => count($data['thumbnails'] ?? []),
            'main_thumbnail' => $mainThumbnail,
            'main_media_id' => $mainMediaId
        ]);

        // Handle additional thumbnails (remaining images)
        $additionalThumbnails = !empty($data['thumbnails']) ? array_slice($data['thumbnails'], 1) : [];
        $additionalMediaIds = [];
        foreach ($additionalThumbnails as $thumbnail) {
            $mediaId = self::storeMedia($thumbnail);
            if ($mediaId) {
                $additionalMediaIds[] = $mediaId;
            }
        }

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? 'description',
            'short_description' => $data['short_description'] ?? 'short description',
            'brand_id' => $data['brand'] ?? null,
            'price' => $data['price'] ?? 0,
            'discount_price' => $data['discount_price'] ?? 0,
            'quantity' => $data['stock_quantity'] ?? 1,
            'min_order_quantity' => 1,
            'media_id' => $mainMediaId ?? null,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'code' => $data['sku'] ?? random_int(100000, 999999),
        ]);

        $product->categories()->sync($data['categorys'] ?? []);
        $product->subCategories()->sync($data['subCategories'] ?? []);
        $product->colors()->sync($data['colors'], []);
        $product->sizes()->sync($data['sizes'], []);

        if (!empty($additionalMediaIds)) {
            $product->medias()->attach($additionalMediaIds);
        }

        return $product;
    }

    private static function findThumbnailInFolders($thumbnailName, $folders, $galleryPath)
    {
        foreach ($folders as $folder) {
            $folderPath = $galleryPath.'/'.$folder;
            if (Storage::disk('public')->exists($folderPath)) {
                $files = File::files(Storage::disk('public')->path($folderPath));
                foreach ($files as $file) {
                    if (basename($file) == $thumbnailName) {
                        return $file->getPathname();
                    }
                }
            }
        }
        return null;
    }

    public static function storeMedia($thumbnail)
    {
        if ($thumbnail == null) {
            return null;
        }

        try {
            $path = 'thumbnails';
            $fileName = random_int(100000, 999999).date('YmdHis');
            
            // Handle file object (from upload)
            if (is_object($thumbnail) && method_exists($thumbnail, 'getRealPath')) {
                $extension = $thumbnail->getClientOriginalExtension() ?? pathinfo($thumbnail->getRealPath(), PATHINFO_EXTENSION);
                $originalName = $thumbnail->getClientOriginalName() ?? basename($thumbnail->getRealPath());
                $fileName .= '.'.$extension;
                $storagePath = Storage::disk('public')->putFileAs($path, $thumbnail, $fileName);
            }
            // Handle file path (from folder)
            else {
                $filePath = is_string($thumbnail) ? $thumbnail : $thumbnail->getPathname();
                $originalName = basename($filePath);
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $fileName .= '.'.$extension;
                $destinationPath = $path.'/'.$fileName;
                
                // Get relative path from public storage
                $publicPath = Storage::disk('public')->path('');
                $relativePath = str_replace($publicPath, '', $filePath);
                $relativePath = ltrim($relativePath, '/');
                
                // Copy file to thumbnails directory
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->copy($relativePath, $destinationPath);
                    $storagePath = $destinationPath;
                } else {
                    return null;
                }
            }

            $media = Media::create([
                'name' => pathinfo($storagePath, PATHINFO_FILENAME),
                'src' => $storagePath,
                'type' => 'image',
                'original_name' => $originalName,
                'extension' => pathinfo($storagePath, PATHINFO_EXTENSION),
            ]);

            return $media->id;
        } catch (\Exception $e) {
            \Log::error('Error storing media: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update the previous thumbnails.
     *
     * @param  array  $previousThumbnails  The array of previous thumbnails
     */
    private static function updatePreviousThumbnail($previousThumbnails)
    {
        foreach ($previousThumbnails ?? [] as $thumbnail) {
            if (array_key_exists('file', $thumbnail) && array_key_exists('id', $thumbnail) && $thumbnail['file'] != null) {
                $media = Media::find($thumbnail['id']);

                MediaRepository::updateByRequest(
                    $thumbnail['file'],
                    'products',
                    'image',
                    $media
                );
            }
        }
    }

    /**
     * Update the additional thumbnails.
     *
     * @param  array  $additionalThumbnails  The array of additional thumbnails
     * @param  Product  $product
     */
    private static function updateAdditionThumbnails($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->medias()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->medias()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->medias()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }
}
