<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetReportRequest;
use App\Models\Item;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Generate a utilization report for specified items within a date range
     * 
     * @param \App\Http\Requests\GetReportRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getReport(GetReportRequest $request)
    {
        try {
            $itemIds = $request->item_ids;
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

            // Calculate date range
            $dateRange = Carbon::parse($startDate)->daysUntil($endDate);
            $totalDays = $dateRange->count();

            $result = [];

            // Process each requested item
            foreach ($itemIds as $itemId) {
                // Get all relevant items
                $items = Item::where('model', $itemId)->get();

                if ($items->isEmpty()) {
                    continue;
                }

                // Get all networks for this model
                $networks = $items->pluck('network')->unique();

                foreach ($networks as $network) {
                    // Get the current inventory for this item/network combination
                    $currentItem = $items->where('network', $network)->first();
                    $currentInventory = $currentItem ? $currentItem->total : 0;

                    // Get all rentals for this item in date range
                    $rentals = Rental::where('model', $itemId)
                        ->where('network', $network)
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->whereBetween('outbound_date', [$startDate, $endDate])
                                ->orWhereBetween('inbound_date', [$startDate, $endDate])
                                ->orWhere(function ($q) use ($startDate, $endDate) {
                                    $q->where('outbound_date', '<', $startDate)
                                        ->where('inbound_date', '>', $endDate);
                                });
                        })
                        ->get();

                    if ($rentals->isEmpty()) {
                        continue;
                    }

                    // Calculate total orders
                    $orderIds = $rentals->pluck('order_id')->unique();
                    $totalOrders = $orderIds->count();

                    // Calculate total quantity
                    $totalQuantity = $rentals->sum('amount');

                    // Calculate max quantity in an order
                    $maxQuantityInOrder = $rentals->max('amount');

                    // Calculate average quantity per order
                    $avgQtyPerOrder = $totalOrders > 0 ? round($totalQuantity / $totalOrders, 2) : 0;

                    // Calculate purchase amount
                    $purchaseAmount = $totalQuantity > $currentInventory ? $totalQuantity - $currentInventory : 0;

                    // Calculate max and avg out of warehouse
                    $dailyOutOfWarehouse = [];

                    // Calculate for each day in the range
                    foreach ($dateRange as $date) {
                        $currentDate = $date->format('Y-m-d');
                        $outCount = 0;

                        foreach ($rentals as $rental) {
                            $rentalOutbound = Carbon::parse($rental->outbound_date)->format('Y-m-d');
                            $rentalInbound = Carbon::parse($rental->inbound_date)->format('Y-m-d');

                            // Check if the item is out of warehouse on this day
                            if (($currentDate >= $rentalOutbound && $currentDate <= $rentalInbound)) {
                                $outCount += $rental->amount;
                            }
                        }

                        $dailyOutOfWarehouse[$currentDate] = $outCount;
                    }

                    // Max out of warehouse is the maximum value for any day
                    $maxOutOfWarehouse = !empty($dailyOutOfWarehouse) ? max($dailyOutOfWarehouse) : 0;

                    // Average out of warehouse is the sum divided by days
                    $avgOutOfWarehouse = !empty($dailyOutOfWarehouse) && $totalDays > 0
                        ? round(array_sum($dailyOutOfWarehouse) / $totalDays)
                        : 0;

                    // Create report data for this item/network combination
                    $result[] = [
                        'model' => $itemId,
                        'network' => $network,
                        'max_out_of_warehouse' => $maxOutOfWarehouse,
                        'avg_out_of_warehouse' => $avgOutOfWarehouse,
                        'total_quantity' => $totalQuantity,
                        'total_orders' => $totalOrders,
                        'avg_qty_per_order' => $avgQtyPerOrder,
                        'max_quantity_in_order' => $maxQuantityInOrder,
                        'current_inventory' => $currentInventory,
                        'purchase_amount' => $purchaseAmount
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Report generated successfully'
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => "Request failed, please try again"
            ], 400);
        }
    }
}