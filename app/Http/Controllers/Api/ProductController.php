<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display Customers listing.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::paginate(request()->all());
        return response()->json([
            "success" => true,
            "message" => "Products List.",
            "data" => $products
        ]);
    }
}
