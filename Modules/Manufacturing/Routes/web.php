<?php

use App\Http\Controllers\ProductController;
use Modules\Manufacturing\Http\Controllers\ManufacturingController;
use Modules\Manufacturing\Http\Controllers\ProductionController;
use Modules\Manufacturing\Http\Controllers\RecipeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route names use the manufacturing.* prefix so they never collide with
| core app names and `php artisan route:cache` succeeds.
|
*/

Route::middleware(['common', 'auth', 'active'])->group(function () {
    Route::controller(ProductionController::class)->group(function () {
        Route::prefix('manufacturing/productions')->group(function () {
            Route::post('production-data', 'productionData')->name('manufacturing.productions.data');
            Route::get('product_production/{id}', 'productProductionData')
                ->name('manufacturing.productions.product_production');
        });
    });

    Route::resource('manufacturing/productions', ProductionController::class)
        ->except(['show'])
        ->names('manufacturing.productions');

    Route::resource('manufacturing/recipes', RecipeController::class)
        ->except(['show'])
        ->names('manufacturing.recipes');

    Route::post('manufacturing/products/product-data', [ProductController::class, 'productData'])
        ->name('manufacturing.get-products');

    Route::post('manufacturing/product-data', [RecipeController::class, 'productData'])
        ->name('manufacturing.product-data');

    Route::get('manufacturing/recipes/lims_product_search', [ProductController::class, 'limsProductSearch'])
        ->name('manufacturing.product.search');

    Route::post('manufacturing/get-Ingredients', [ProductionController::class, 'getIngredients'])
        ->name('manufacturing.get-Ingredients');

    Route::post('products/getdata/{id}/{variant_id}', [ProductController::class, 'getData'])
        ->name('manufacturing.products.getdata');

    Route::prefix('manufacturing')->group(function () {
        Route::get('/', [ManufacturingController::class, 'index'])->name('manufacturing.home');
    });
});
