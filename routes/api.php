<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FakeStoreClient;

Route::middleware(['integration'])->group(function () {

    Route::post('/integrations/fakestore/sync', function (Request $request) {

        $client = new FakeStoreClient();

        $products = $client->getProducts();
        $categories = $client->getCategories();

        // Aqui você pode fazer a lógica de salvar/atualizar no banco

        return response()->json([
            'message' => 'Synchronization completed',
            'imported_products' => count($products),
            'imported_categories' => count($categories),
        ]);
    });
});
