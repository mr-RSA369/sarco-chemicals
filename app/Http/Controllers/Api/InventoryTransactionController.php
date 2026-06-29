<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\ItemBundleSize;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function items(Request $request)
    {
        return Item::query()
            ->with('type')
            ->where(
                'category_id',
                $request->category_id
            )
            ->orderBy('item_name')
            ->get();
    }

    public function itemDetails(Item $item)
    {
        return response()->json([
            'id' => $item->id,
            'name' => $item->item_name,
            'size' => $item->size,
            'unit' => $item->size_unit,
            'category_id' => $item->category_id,
            'type_id' => $item->type_id,
        ]);
    }

    public function index(Request $request)
    {
        $perpage = $request->per_page ?? 50;
        return InventoryTransaction::query()
            ->with([
                'item.type',
                'item.category',
                'batch',
                'bundleSize',
                'item.bundleSizes'
            ])
            ->when(
                $request->category_id,
                fn($q) =>
                    $q->whereHas(
                        'item',
                        fn($item) =>
                            $item->where(
                                'category_id',
                                $request->category_id
                            )
                    )
            )
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate($perpage);
    }

    public function bundleSizes(Item $item)
    {
        return ItemBundleSize::query()
            ->where('item_id', $item->id)
            ->orderBy('bundle_size')
            ->get();
    }

    public function batchLookup(Request $request)
    {
        $query = InventoryBatch::query()
            ->where('item_id', $request->item_id);

        if ($request->filled('batch_no')) {

            $query->where(
                'batch_no',
                $request->batch_no
            );

        } else {

            $query->whereNull('batch_no');
        }

        $batch = $query->first();

        if (!$batch) {
            return response()->json([
                'exists' => false,
                'opening_balance' => 0,
                'mfg_date' => null,
                'exp_date' => null,
                'rack_no' => null,
            ]);
        }

        return response()->json([
            'exists' => true,
            'opening_balance' => $batch->current_qty,
            'mfg_date' => $batch->mfg_date,
            'exp_date' => $batch->exp_date,
            'rack_no' => $batch->rack_no,
        ]);
    }

    public function batches(Item $item)
    {
        return InventoryBatch::query()
            ->where('item_id', $item->id)
            ->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:receipt,issue',

            'bundle_size' => 'required|min:1',
            'bundle_count' => 'required|min:1',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Find / Create Batch
            |--------------------------------------------------------------------------
            */

            if ($request->filled('batch_no')) {

                $batch = InventoryBatch::firstOrCreate(
                    [
                        'item_id' => $request->item_id,
                        'batch_no' => $request->batch_no,
                    ],
                    [
                        'mfg_date' => $request->mfg_date,
                        'exp_date' => $request->exp_date,
                        'rack_no' => $request->rack_no,
                        'qty_per_bundle' => $request->qty_per_bundle ?? 1,
                        'current_qty' => 0,
                    ]
                );

            } else {

                $batch = InventoryBatch::firstOrCreate(
                    [
                        'item_id' => $request->item_id,
                        'batch_no' => null,
                    ],
                    [
                        'mfg_date' => $request->mfg_date,
                        'exp_date' => $request->exp_date,
                        'rack_no' => $request->rack_no,
                        'qty_per_bundle' => $request->qty_per_bundle ?? 1,
                        'current_qty' => 0,
                    ]
                );
            }

            $bundleSize = ItemBundleSize::firstOrCreate(
            [
                'item_id' => $request->item_id,
                'bundle_size' => $request->bundle_size
            ],
            [
                'available_bundles' => 0
            ]
        );

            $quantity = $request->bundle_size * $request->bundle_count;

            /*
            |--------------------------------------------------------------------------
            | Opening Balance
            |--------------------------------------------------------------------------
            */

            $openingBalance = $batch->current_qty;

            /*
            |--------------------------------------------------------------------------
            | Stock Validation + Closing Balance
            |--------------------------------------------------------------------------
            */

            if ($request->transaction_type === 'issue') {

                if (
                    $bundleSize->available_bundles
                    <
                    $request->bundle_count
                ) {
                    throw new Exception(
                        'Not enough bundles available.'
                    );
                }

                if ($openingBalance < $quantity) {
                    throw new Exception(
                        'Insufficient stock.'
                    );
                }

                $closingBalance =
                    $openingBalance - $quantity;

            } else {

                $closingBalance =
                    $openingBalance + $quantity;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Transaction
            |--------------------------------------------------------------------------
            */



            $transaction = InventoryTransaction::create([
                'item_id' => $request->item_id,
                'inventory_batch_id' => $batch->id,

                'transaction_date' => $request->transaction_date,

                'transaction_type' => $request->transaction_type,

                'quantity' => $quantity,

                'item_bundle_size_id' => $bundleSize->id,

                'bundle_count' => $request->bundle_count,

                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,


                'remarks' => $request->remarks,

                'created_by' => auth()->id(),
            ]);

            if ($request->transaction_type === 'receipt') {

                $bundleSize->increment(
                    'available_bundles',
                    $request->bundle_count
                );

            } else {

                $bundleSize->decrement(
                    'available_bundles',
                    $request->bundle_count
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update Current Stock
            |--------------------------------------------------------------------------
            */

            $batch->update([
                'current_qty' => $closingBalance
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transaction saved successfully.',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(InventoryTransaction $transaction)
    {
        DB::beginTransaction();

        try {

            $batch = $transaction->batch;

            $bundleSize =
                $transaction->bundleSize;

            if ($batch) {

                if (
                    $transaction->transaction_type
                    === 'receipt'
                ) {

                    if (
                        $batch->current_qty
                        <
                        $transaction->quantity
                    ) {
                        throw new Exception(
                            'Unable to delete transaction.'
                        );
                    }

                    $batch->decrement(
                        'current_qty',
                        $transaction->quantity
                    );

                    if ($bundleSize) {

                        if (
                            $bundleSize->available_bundles
                            <
                            $transaction->bundle_count
                        ) {
                            throw new Exception(
                                'Unable to delete transaction.'
                            );
                        }

                        $bundleSize->decrement(
                            'available_bundles',
                            $transaction->bundle_count
                        );
                    }

                } else {

                    $batch->increment(
                        'current_qty',
                        $transaction->quantity
                    );

                    if ($bundleSize) {

                        $bundleSize->increment(
                            'available_bundles',
                            $transaction->bundle_count
                        );
                    }
                }
            }

            $transaction->delete();

            DB::commit();

            return response()->json([
                'message' =>
                    'Transaction deleted successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function stock(Request $request)
    {
        return InventoryBatch::query()
            ->select([
                'id',
                'item_id',
                'batch_no',
                'mfg_date',
                'exp_date',
                'rack_no',
                'current_qty'
            ])
            ->with([
                'item:id,category_id,product_name,item_name,size,size_unit,minimum_qty',
                'item.bundleSizes:id,item_id,bundle_size,available_bundles'
            ])
            ->whereHas(
                'item',
                fn ($q) => $q->where(
                    'category_id',
                    $request->category_id
                )
            )
            ->where('current_qty', '>', 0)
            ->orderBy('item_id')
            ->get();
    }

    public function latestTransactions(Request $request)
        {
            $perPage = $request->get('per_page', 10);

            return InventoryTransaction::query()
                ->with([
                    'item:id,item_name,size,size_unit',
                    'item.category:id,name',
                    'batch:id,batch_no',

                ])
                ->latest('transaction_date')
                ->latest('id')
                ->paginate($perPage);
        }
}
