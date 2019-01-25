@extends('layouts.catalog')

@section('content')
<div class="super_container">
	
    @include('layouts.catalog_header')
    
    <div class="home">
        <div class="home_background parallax-window" data-parallax="scroll" data-image-src="assets/images/catalog/shop_background.jpg"></div>
        <div class="home_overlay"></div>
        <div class="home_content d-flex flex-column align-items-center justify-content-center">
        <h2 class="home_title">{{ !empty($category) ? $category : 'Catalog' }}</h2>
        </div>
    </div>

    <div class="shop">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">

                    <!-- Shop Sidebar -->
                    <div class="shop_sidebar">
                        <div class="sidebar_section">
                            <div class="sidebar_title">Categories</div>
                            <ul class="sidebar_categories">
                            <li><a href="{{ url('catalog') }}"> All categories</a></li>
                                @foreach ($categories as $category)
                            <li><a href="{{ url('catalog?category='.$category->id) }}">{{ $category->category_name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        {{-- <div class="sidebar_section filter_by_section">
                            <div class="sidebar_title">Filter By</div>
                            <div class="sidebar_subtitle">Price</div>
                            <div class="filter_price">
                                <div id="slider-range" class="slider_range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"><div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 0%; width: 58%;"></div><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 0%;"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 58%;"></span></div>
                                <p>Range: </p>
                                <p><input type="text" id="amount" class="amount" readonly="" style="border:0; font-weight:bold;"></p>
                            </div>
                        </div> --}}
                        {{-- <div class="sidebar_section">
                            <div class="sidebar_subtitle color_subtitle">Color</div>
                            <ul class="colors_list">
                                <li class="color"><a href="#" style="background: #b19c83;"></a></li>
                                <li class="color"><a href="#" style="background: #000000;"></a></li>
                                <li class="color"><a href="#" style="background: #999999;"></a></li>
                                <li class="color"><a href="#" style="background: #0e8ce4;"></a></li>
                                <li class="color"><a href="#" style="background: #df3b3b;"></a></li>
                                <li class="color"><a href="#" style="background: #ffffff; border: solid 1px #e1e1e1;"></a></li>
                            </ul>
                        </div> --}}
                    </div>

                </div>

                <div class="col-lg-9">
                    
                    <!-- Shop Content -->

                    <div class="shop_content">
                        <div class="shop_bar clearfix">
                        <div class="shop_product_count"><span>{{ count($items) }}</span> products found {!! !empty(request()->keyword) ? 'with keyword <em>"'.request()->keyword.'"</em>' : '' !!}</div>
                            <div class="shop_sorting">
                                <span>Sort by:</span>
                                <ul>
                                    <li>
                                        <span class="sorting_text">highest rated<i class="fas fa-chevron-down"></i></span>
                                        <ul>
                                            <li class="shop_sorting_button" data-isotope-option="{ &quot;sortBy&quot;: &quot;original-order&quot; }">highest rated</li>
                                            <li class="shop_sorting_button" data-isotope-option="{ &quot;sortBy&quot;: &quot;name&quot; }">name</li>
                                            <li class="shop_sorting_button" data-isotope-option="{ &quot;sortBy&quot;: &quot;price&quot; }">price</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="product_grid" style="position: relative; height: 1012px;">
                            <div class="product_grid_border"></div>
                            
                            @foreach ($items as $item)
                            <!-- Product Item -->
                            <div class="product_item is_new" style="position: absolute; left: 0px; top: 0px;">
                                <div class="product_border"></div>
                                <div class="product_image d-flex flex-column align-items-center justify-content-center"><img src="{{ !empty($item->feature_image) ?  url('uploads/'.$item->feature_image) : url('assets/images/default-image.png') }}" alt="" style="
                                    padding: 20px;
                                    object-fit: contain;
                                "></div>
                                <div class="product_content">
                                <div class="product_price">Rp. {{ number_format($item->item_price) }}</div>
                                <div class="product_name"><div><a href="#" tabindex="0">{{ $item->item_description }}</a></div></div>
                                </div>
                                <div class="product_fav"><i class="fas fa-shopping-cart"></i></div>
                            </div>
                            @endforeach

                        </div>

                        <!-- Shop Page Navigation -->

                        {{-- <div class="shop_page_nav d-flex flex-row">
                            <div class="page_prev d-flex flex-column align-items-center justify-content-center"><i class="fas fa-chevron-left"></i></div>
                            <ul class="page_nav d-flex flex-row">
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">...</a></li>
                                <li><a href="#">21</a></li>
                            </ul>
                            <div class="page_next d-flex flex-column align-items-center justify-content-center"><i class="fas fa-chevron-right"></i></div>
                        </div> --}}

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('style')
<link rel="stylesheet" type="text/css" href="assets/styles/bootstrap4/bootstrap.min.css">
<link href="assets/plugins/fontawesome-free-5.0.1/css/fontawesome-all.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/animate.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/styles/shop_styles.css">
<link rel="stylesheet" type="text/css" href="assets/styles/shop_responsive.css">
@endpush

@push('js')
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/styles/bootstrap4/popper.js"></script>
<script src="assets/styles/bootstrap4/bootstrap.min.js"></script>
<script src="assets/plugins/greensock/TweenMax.min.js"></script>
<script src="assets/plugins/greensock/TimelineMax.min.js"></script>
<script src="assets/plugins/scrollmagic/ScrollMagic.min.js"></script>
<script src="assets/plugins/greensock/animation.gsap.min.js"></script>
<script src="assets/plugins/greensock/ScrollToPlugin.min.js"></script>
<script src="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="assets/plugins/easing/easing.js"></script>
<script src="assets/plugins/Isotope/isotope.pkgd.min.js"></script>
<script src="assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script src="assets/plugins/parallax-js-master/parallax.min.js"></script>
<script src="assets/js/shop_custom.js"></script>
@endpush