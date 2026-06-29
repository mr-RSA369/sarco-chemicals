<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use Exception;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request)
    {
        try {
            Item::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function categories()
    {
        try {
            return ItemCategory::all();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories: ' . $e->getMessage()
            ], 500);
        }
    }

        public function itemTypes($categoryId)
        {
            try {
            return ItemType::where(
                'category_id',
                $categoryId
            )->get();
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch item types: ' . $e->getMessage()
                ], 500);
            }
        }
}
