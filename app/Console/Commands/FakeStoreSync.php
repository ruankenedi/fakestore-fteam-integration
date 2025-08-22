<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FakeStoreClient;
use App\Models\Product;
use App\Models\Category;

class FakeStoreSync extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fakestore:sync';

    /**
     * The console command description.
     */
    protected $description = 'Synchronizes products and categories from the FakeStore API with the local database';

    protected $client;

    public function __construct(FakeStoreClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $this->info('Starting FakeStore API synchronization...');

        // Synchronize categories
        $categories = $this->client->getCategories();
        $importedCategories = 0;

        foreach ($categories as $catName) {
            $category = Category::firstOrCreate(['name' => $catName]);
            if ($category->wasRecentlyCreated) {
                $importedCategories++;
            }
        }

        $this->info("Imported categories: {$importedCategories}");

        // Synchronize products
        $products = $this->client->getProducts();
        $importedProducts = 0;

        foreach ($products as $p) {
            $category = Category::where('name', $p['category'])->first();
            if (!$category) {
                $category = Category::create(['name' => $p['category']]);
            }

            $product = Product::updateOrCreate(
                ['external_id' => $p['id']],
                [
                    'category_id' => $category->id,
                    'title' => $p['title'],
                    'description' => $p['description'],
                    'price' => $p['price'],
                    'image_url' => $p['image'],
                    'raw' => json_encode($p),
                ]
            );

            $importedProducts++;
        }

        $this->info("Imported products: {$importedProducts}");
        $this->info('Synchronization completed successfully!');
    }
}
