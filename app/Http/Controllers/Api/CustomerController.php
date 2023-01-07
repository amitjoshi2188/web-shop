<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display Customers listing.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $customers = Customer::paginate(request()->all());
        return response()->json([
            "success" => true,
            "message" => "Customers List.",
            "data" => $customers
        ]);
    }
}
