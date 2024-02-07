@extends('layouts.admin.app')

@section('content')
    <!-- Main content -->
    <section class="content">

    @include('layouts.errors-and-messages')
    <!-- Default box -->
        @if($reviews)
            <div class="box">
                <div class="box-body">
                    <h2>Reviews</h2>
                    @include('layouts.search', ['route' => route('admin.reviews.index')])
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="col-md-3">日時</td>
                                <td class="col-md-3">商品ID</td>
                                <td class="col-md-2">ユーザー（ID）</td>
                                <td class="col-md-2">評価</td>
                                <td class="col-md-2">コメント</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($reviews as $review)
                        <tr>
                            <td>{{ date('M d, Y h:i a', strtotime($review->created_at)) }}</td>
                            <td><a href="{{ route('admin.products.show', $review->product_id) }}">{{ $review->product->name }}</a></td>
                            {{-- 商品ID->管理者ページの商品詳細ページへのリンク --}}
                            <td><a href="{{ route('admin.customers.show', $review->user_id) }}">{{ $review->user->name }} ({{ $review->user_id }})</a></td>
                            {{-- ユーザーID->管理者ページのユーザー詳細ページへのリンク --}}
                            <td>{{ $review->rating }}</td>
                            <td>{{ $review->comment }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {{ $orders->links() }}
                </div>
            </div>
            <!-- /.box -->
        @endif

    </section>
    <!-- /.content -->
@endsection
{{--
<div>
    <a href="{{ route('admin.dashboard') }}">
        <h1>こんにちは！</h1>
    </a>
</div> --}}