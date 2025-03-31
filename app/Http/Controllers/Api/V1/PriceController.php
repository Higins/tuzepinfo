<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Tüzépinfo API Dokumentáció",
 *     description="Tüzépinfo ár összesítő rendszer API dokumentációja",
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class PriceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/prices",
     *     summary="Összes termék listázása legfrissebb árakkal",
     *     tags={"Prices"},
     *     @OA\Response(
     *         response=200,
     *         description="Sikeres lekérdezés",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="category", type="string"),
     *                 @OA\Property(property="unit", type="string"),
     *                 @OA\Property(
     *                     property="latest_price",
     *                     type="object",
     *                     @OA\Property(property="price", type="number"),
     *                     @OA\Property(property="currency", type="string"),
     *                     @OA\Property(property="source", type="string"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['latestPrice.priceSource'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'unit' => $product->unit,
                    'latest_price' => $product->latestPrice ? [
                        'price' => $product->latestPrice->price,
                        'currency' => $product->latestPrice->currency,
                        'source' => $product->latestPrice->priceSource->name,
                        'updated_at' => $product->latestPrice->collected_at,
                    ] : null,
                ];
            });

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/prices/{product}",
     *     summary="Részletes ár előzmények lekérdezése egy adott termékhez",
     *     tags={"Prices"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Termék ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sikeres lekérdezés",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="unit", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(
     *                 property="price_history",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="price", type="number"),
     *                     @OA\Property(property="currency", type="string"),
     *                     @OA\Property(property="source", type="string"),
     *                     @OA\Property(property="collected_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Termék nem található"
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['priceHistory.priceSource']);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category,
            'unit' => $product->unit,
            'description' => $product->description,
            'price_history' => $product->priceHistory->map(function ($history) {
                return [
                    'price' => $history->price,
                    'currency' => $history->currency,
                    'source' => $history->priceSource->name,
                    'collected_at' => $history->collected_at,
                ];
            }),
        ]);
    }
}
