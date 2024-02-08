<div class="row">
    <div class="col-md-6">
        @if (!empty($product->cover))
            <ul id="thumbnails" class="col-md-4 list-unstyled">
                <li>
                    <a href="javascript: void(0)">
                        <img class="img-responsive img-thumbnail" src="{{ $product->cover }}" alt="{{ $product->name }}" />
                    </a>
                </li>
                @if (isset($images) && !$images->isEmpty())
                    @foreach ($images as $image)
                        <li>
                            <a href="javascript: void(0)">
                                <img class="img-responsive img-thumbnail" src="{{ asset("storage/$image->src") }}"
                                    alt="{{ $product->name }}" />
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
            <figure class="text-center product-cover-wrap col-md-8">
                <img id="main-image" class="product-cover img-responsive" src="{{ $product->cover }}?w=400"
                    data-zoom="{{ $product->cover }}?w=1200">
            </figure>
        @else
            <figure>
                <img src="{{ asset('images/NoData.png') }}" alt="{{ $product->name }}"
                    class="img-bordered img-responsive">
            </figure>
        @endif
    </div>
    <div class="col-md-6">
        <div class="product-description">
            <h1>{{ $product->name }}</h1>
            <div class="product-total-price">
                <p>
                    <b>
                        <span class="product-price" style="color:#bf0000;font-size: 24px;">{{ $product->price * config('cart.usd_to_jpy_rate') }}{{ config('cart.currency_symbol') }}</span>
                        +
                        <span class="shipping-fee">送料980{{ config('cart.currency_symbol') }}</span>
                    </b>
                </p>
            </div>
            <div class="SKU">
                <p>SKU:{!! $product->sku !!}</p>
            </div>
            <div class="description">{!! $product->description !!}</div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    @include('layouts.errors-and-messages')
                    <form action="{{ route('cart.store') }}" class="form-inline" method="post">
                        {{ csrf_field() }}
                        @if (isset($productAttributes) && !$productAttributes->isEmpty())
                            <div class="form-group">
                                <label for="productAttribute">Choose Combination</label> <br />
                                <select name="productAttribute" id="productAttribute" class="form-control select2">
                                    @foreach ($productAttributes as $productAttribute)
                                        <option value="{{ $productAttribute->id }}">
                                            @foreach ($productAttribute->attributesValues as $value)
                                                {{ $value->attribute->name }} : {{ ucwords($value->value) }}
                                            @endforeach
                                            @if (!is_null($productAttribute->sale_price))
                                                ({{ config('cart.currency_symbol') }}
                                                {{ $productAttribute->sale_price }})
                                            @elseif(!is_null($productAttribute->price))
                                                ( {{ config('cart.currency_symbol') }} {{ $productAttribute->price }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <hr>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p>数量</p>
                            <div class="form-group">
                                <input type="text" class="form-control" name="quantity" id="quantity"
                                    placeholder="数量" value="{{ old('quantity') }}" />
                                <input type="hidden" name="product" value="{{ $product->id }}" />
                                <button type="submit" class="btn btn-warning"><i class="fa fa-cart-plus"></i>かごに追加</button>
                            </div>
                    </form>
                    <div class="product-reviews">
                    @if(isset($reviews))
                        @forelse($reviews as $review)
                        <div class="single-review">
                            <div class="star-rating">
                                @for($i = 0; $i < 5; $i++)
                                    @if($i < $review->evaluation)
                                        <i class="fa fa-star"></i> <!-- 評価分の星 -->
                                    @else
                                        <i class="fa fa-star-o"></i> <!-- 残りの空の星 -->
                                    @endif
                                @endfor
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                        @empty
                        <p>この商品にはまだレビューがありません。</p>
                        @endforelse
                    @endif
                    </div>
                    @if(auth()->check())
                    <form class="review-form" action="{{ route('front.review.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <input type="hidden" name="slug" value="{{ $product->slug }}" />
                        <div class="review-group">
                            <div class="evaluation-group">
                                <label for="evaluation">評価</label>
                                <select name="evaluation" id="evaluation" class="review-control">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="comment-group">
                                <label for="comment">コメント</label>
                                <input name="comment" id="comment" class="review-control" maxlength="100" minlength="10" placeholder="コメントを入力してください" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-review" id="submit-button">登録</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    // コメントと評価のフォームの機能
    const evaluation = document.getElementById('evaluation');
    const comment = document.getElementById('comment');
    const submitButton = document.getElementById('submit-button');

    function updateSubmitButtonState() {
        // コメントが空でない場合にボタンを有効化
        // 評価がデフォルト値以外であることは既に保証されているため、コメントのみをチェック
        submitButton.disabled = !comment.value.trim();
    }

    // コメントの入力状態が変わったときにボタンの状態を更新
    comment.addEventListener('input', updateSubmitButtonState);

    // ページ読み込み時にもボタンの状態を更新
    updateSubmitButtonState();

    // 商品画像のズーム機能
    var productPane = document.querySelector('.product-cover');
    var paneContainer = document.querySelector('.product-cover-wrap');

    new Drift(productPane, {
        paneContainer: paneContainer,
        inlinePane: false
    });
});
</script>
@endsection
