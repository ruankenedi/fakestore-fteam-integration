<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FakeStoreClient;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

Route::middleware(['integration'])->group(function () {

    // Fake Store API Synchronization
    Route::post('/integrations/fakestore/sync', function () {
        $client = new FakeStoreClient();

        $categoriesData = $client->getCategories();
        $productsData = $client->getProducts();

        // Create categories
        foreach ($categoriesData as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }

        // Create or update products
        foreach ($productsData as $product) {
            $category = Category::where('name', $product['category'])->first();

            Product::updateOrCreate(
                ['external_id' => $product['id']],
                [
                    'category_id' => $category ? $category->id : null,
                    'title'       => $product['title'],
                    'description' => $product['description'],
                    'price'       => $product['price'],
                    'image_url'   => $product['image'],
                    'raw'         => $product
                ]
            );
        }

        return response()->json([
            'message' => 'Sincronização concluída',
            'imported_products' => count($productsData),
            'imported_categories' => count($categoriesData)
        ]);
    });

    // Product listing with filters, sorting, and pagination
    Route::get('/products', function (Request $request) {
        $query = Product::with('category');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->sort_price) {
            $query->orderBy('price', $request->sort_price);
        }

        $perPage = $request->per_page ?? 15;
        $products = $query->paginate($perPage);

        return response()->json($products);
    });

    // Buscar produto por ID
    Route::get('/products/{id}', function ($id) {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    });

    // Endpoints of categories
    Route::get('/categories', function () {
        return response()->json(Category::all());
    });

    Route::get('/categories/{id}', function ($id) {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    });

    // Stats
    Route::get('/stats', function () {
        $totalProducts = Product::count();

        $totalPerCategory = Product::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->get();

        $avgPrice = DB::select('SELECT AVG(price) as avg_price FROM products')[0]->avg_price;

        $top5 = Product::orderBy('price', 'desc')->take(5)->get();

        return response()->json([
            'total_products' => $totalProducts,
            'total_per_category' => $totalPerCategory,
            'avg_price' => $avgPrice,
            'top_5_expensive' => $top5,
        ]);
    });
});
