<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\InventoryTransactionController;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum', 'manager'])->group(function () {
    Route::get(
        '/users',
        [UserController::class, 'index']
    );

    Route::patch(
        '/users/{user}/role',
        [UserController::class, 'updateRole']
    );

    Route::delete(
        '/users/{user}',
        [UserController::class, 'destroy']
    );

});

Route::get('/categories', [ItemController::class, 'categories']);

Route::get('/item-types/{category}', [ItemController::class, 'itemTypes']);

Route::post('/items', [ItemController::class, 'store']);


Route::middleware(['auth:sanctum', 'admin_or_manager'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Item Management
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'items',
        ItemController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Inventory Transactions
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/transactions/items',
        [InventoryTransactionController::class, 'items']
    );

    Route::get(
        '/transactions',
        [InventoryTransactionController::class, 'index']
    );

    Route::post(
        '/transactions',
        [InventoryTransactionController::class, 'store']
    );

    Route::delete(
        '/transactions/{transaction}',
        [InventoryTransactionController::class, 'destroy']
    );

    Route::get(
    '/transactions/batch-lookup',
    [InventoryTransactionController::class, 'batchLookup']
);

Route::get(
    '/items/{item}',
    [InventoryTransactionController::class, 'itemDetails']
);

Route::get(
    '/items/{item}/batches',
    [InventoryTransactionController::class,'batches']
);

Route::get(
    '/items/{item}/bundle-sizes',
    [InventoryTransactionController::class, 'bundleSizes']
);


Route::get(
    '/dashboard/latest-transactions',
    [InventoryTransactionController::class, 'latestTransactions']
);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
    '/stock',
    [InventoryTransactionController::class, 'stock']
);
});

