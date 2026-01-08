<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Return list of sizes (id, name).
     * Optional: can be extended to filter by shop.
     */
    public function index(Request $request)
    {
        $query = Size::query()->isActive();

        // optional: support shop_id filtering if requested (sizes may be scoped per shop)
        if ($request->has('shop_id')) {
            $shopId = $request->get('shop_id');
            $query->where('shop_id', $shopId);
        }

        $sizes = $query->get(['id', 'name']);

        return $this->json('sizes', $sizes);
    }
}
