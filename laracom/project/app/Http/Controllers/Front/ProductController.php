<?php

namespace App\Http\Controllers\Front;

use App\Shop\Products\Product;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Shop\Products\Transformations\ProductTransformable;
use App\Shop\Reviews\Review;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ProductTransformable;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    /**
     * ProductController constructor.
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepo = $productRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $list = $this->productRepo->searchProduct(request()->input('q'));

        $products = $list->where('status', 1)->map(function (Product $item) {
            return $this->transformProduct($item);
        });

        return view('front.products.product-search', [
            'products' => $this->productRepo->paginateArrayResults($products->all(), 10)
        ]);
    }

    //ベクトルの取得
    public function getVector()
    {
        // 商品IDとその商品のorder_status_idのリストを格納する配列
        $productStatusVectors = [];

        // 商品ごとにループ
        $products = Product::all(); // すべての商品を取得
        foreach ($products as $product) {
            // 特定の商品に関連するordersテーブルのorder_status_idを取得
            $statusIds = DB::table('order_product')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->where('order_product.product_id', $product->id)
                ->pluck('orders.order_status_id'); // order_status_idのみを取得

            // 商品IDをキーとして、order_status_idの配列を保存
            $productStatusVectors[$product->id] = $statusIds->toArray();
        }

        return $productStatusVectors;
    }


    //コサイン類似度計算
    public function calculateCosineSimilarity($productStatusVectors, $currentProductId)
    {
        $currentProductVector = $productStatusVectors[$currentProductId];
        $similarityScores = [];

        foreach ($productStatusVectors as $productId => $statusVector) {
            if ($productId == $currentProductId) {
                continue; // 現在の商品との比較はスキップ
            }

            // コサイン類似度の計算
            $dotProduct = 0;
            $normA = 0;
            $normB = 0;
            $vectorLength = max(count($currentProductVector), count($statusVector)); // ベクトルの長さを揃える

            for ($i = 0; $i < $vectorLength; $i++) {
                $valA = $i < count($currentProductVector) ? $currentProductVector[$i] : 0; // 不足分は0で補う
                $valB = $i < count($statusVector) ? $statusVector[$i] : 0; // 不足分は0で補う

                $dotProduct += $valA * $valB;
                $normA += $valA * $valA;
                $normB += $valB * $valB;
            }

            $normA = sqrt($normA);
            $normB = sqrt($normB);
            $similarity = $dotProduct / ($normA * $normB);

            // 類似度スコアを保存
            $similarityScores[$productId] = $similarity;
        }

        return $similarityScores;
    }


    /**
     * Get the product
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(string $slug)
    {
        $product = $this->productRepo->findProductBySlug(['slug' => $slug]);
        $product = $this->transformProduct($product);
        $images = $product->images()->get();
        $category = $product->categories()->first();
        $productAttributes = $product->attributes;
        $reviews = Review::where('product_id', $product->id)->latest()->paginate(5);

        // getVectorメソッドを呼び出して、商品ごとのorder_status_idのリストを取得
        $productStatusVectors = $this->getVector();

        // 現在の商品IDを取得
        $currentProductId = $product->id;

        // コサイン類似度を計算
        $similarityScores = $this->calculateCosineSimilarity($productStatusVectors, $currentProductId);

        // スコアに基づいて降順にソート
        arsort($similarityScores);

        // 上位5位の商品IDを取得
        $topSimilarityProductIds = array_slice(array_keys($similarityScores), 0, 5, true);

        // 上位5位の商品IDから評価を取得する
        $topProductsEvaluation = Product::whereIn('id', $topSimilarityProductIds)
            ->with(['reviews' => function ($query) {
                $query->select('product_id', DB::raw('ROUND(AVG(evaluation)) as averageEvaluation'))
                    ->groupBy('product_id');
            }])
            ->get(['id', 'name', 'slug']);

        return view('front.products.product', compact(
            'product',
            'images',
            'category',
            'productAttributes',
            'reviews',
            'topProductsEvaluation'
        ));
    }
}
