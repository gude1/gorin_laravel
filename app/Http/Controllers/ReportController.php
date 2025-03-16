<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetReportRequest;
use App\Models\Item;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class ReportController extends Controller
{

    /**
     * Returns 
     */
    public function getReport(GetReportRequest $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $itemIds = $request->item_ids;


            // Get relevant rentals
            $rentals = Rental::whereIn('model', function ($query) use ($itemIds) {
                $query->select('model')->from('items')->whereIn('id', $itemIds);
            })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('outbound_date', [$startDate, $endDate])
                        ->orWhereBetween('inbound_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('outbound_date', '<=', $startDate)
                                ->where('inbound_date', '>=', $endDate);
                        });
                })
                ->get();

            // Track daily quantities
            $dailyOut = [];
            foreach ($rentals as $rental) {
                $outbound = Carbon::parse($rental->outbound_date);
                $inbound = Carbon::parse($rental->inbound_date);

                // Ensure the rental period falls within our requested range
                $start = max($startDate, $outbound);
                $end = min($endDate, $inbound);

                while ($start->lte($end)) {
                    $date = $start->toDateString();
                    $dailyOut[$date] = ($dailyOut[$date] ?? 0) + $rental->amount;
                    $start->addDay();
                }
            }

            // Calculate values
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $sumOutOfWarehouse = array_sum($dailyOut);
            $maxOutOfWarehouse = $dailyOut ? max($dailyOut) : 0;
            $avgOutOfWarehouse = $totalDays > 0 ? round($sumOutOfWarehouse / $totalDays) : 0;

            $totalQuantity = $rentals->sum('amount');
            $totalOrders = $rentals->groupBy('order_id')->count();

            // Fetch current inventory
            $currentInventory = Item::whereIn('id', $itemIds)->sum('total');

            // Calculate purchase amount
            $purchaseAmount = max(0, $totalQuantity - $currentInventory);

            return response()->json([
                'max_out_of_warehouse' => $maxOutOfWarehouse,
                'avg_out_of_warehouse' => $avgOutOfWarehouse,
                'total_quantity' => $totalQuantity,
                'total_orders' => $totalOrders,
                'current_inventory' => $currentInventory,
                'purchase_amount' => $purchaseAmount
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
