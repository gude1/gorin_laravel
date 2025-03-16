<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Log;

class ItemController extends Controller
{
    /**
     * Summary of getItems
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function getItems(Request $request)
    {
        try {
            $items = Item::limit(500)->get();

            return response()->json([
                "data" => $items,
                "message" => "Items retrieved!"
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                "message" => "Request failed, please try again",
                "data" => null
            ], 400);

        }
    }
}
