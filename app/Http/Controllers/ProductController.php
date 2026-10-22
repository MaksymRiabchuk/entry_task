<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = $this->getProducts();
        return $this->returnFormattedProducts($products);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->productData();
        $products = $this->getProducts();
        $products[] = [
            'id' => (string)Str::uuid(),
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'submitted_at' => now()->format('Y-m-d H:i:s'),
        ];
        $this->updateProducts($products);
        return $this->returnFormattedProducts($products);
    }

    public function update(ProductRequest $request, string $id)
    {
        $data = $request->productData();

        $products = $this->getProducts();
        $found = false;
        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product = array_merge($product, $data);
                $found = true;
                break;
            }
        }
        unset($product);
        if (!$found) {
            abort(404, 'Product not found');
        }
        $this->updateProducts($products);
        return $this->returnFormattedProducts($products);
    }

    private function getProducts()
    {
        $path = storage_path('app/products.json');
        if (!is_file($path)) {
            return [];
        }
        $decoded = json_decode(file_get_contents($path), true);
        return is_array($decoded) ? $decoded : [];
    }

    private function updateProducts(array $products)
    {
        file_put_contents(
            storage_path('app/products.json'),
            json_encode(array_values($products), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    private function returnFormattedProducts(array $products)
    {
        usort($products, fn($a, $b) => strcmp($a['submitted_at'], $b['submitted_at']));
        $products = array_map(function ($product) {
            $product['total'] = round($product['quantity'] * $product['price'], 2);
            return $product;
        }, $products);
        return response()->json([
            'products' => $products,
            'grand_total' => round(array_sum(array_column($products, 'total')), 2),
        ]);
    }
}
