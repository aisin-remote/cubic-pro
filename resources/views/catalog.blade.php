@extends('layouts.catalog')
<div class="super_container">
	
	<!-- Header -->
	
	<header class="header">

		<!-- Top Bar -->

		<div class="top_bar">
			<div class="container">
				<div class="row">
					<div class="col d-flex flex-row">
						<div class="top_bar_content ml-auto">
							<div class="top_bar_user">
								<div class="user_icon"><img src="assets/images/catalog/user.svg" alt=""></div>
								<div><a href="login">Sign in</a></div>
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>

		<!-- Header Main -->

		<div class="header_main">
			<div class="container">
				<div class="row">

					<!-- Logo -->
					<div class="col-lg-4 col-sm-4 col-3 order-1">
						<div class="logo_container">
							<div class="logo"><a href="#">Cubic Pro</a></div>
						</div>
					</div>

					<!-- Search -->
					<div class="col-lg-6 col-12 order-lg-2 order-3 text-lg-left text-right">
						<div class="header_search">
							<div class="header_search_content">
								<div class="header_search_form_container">
									<form action="#" class="header_search_form clearfix">
										<input type="search" required="required" class="header_search_input" placeholder="Search for products...">
										<div class="custom_dropdown">
											<div class="custom_dropdown_list">
												<span class="custom_dropdown_placeholder clc">All Categories</span>
												<i class="fas fa-chevron-down"></i>
												<ul class="custom_list clc">
													<li><a class="clc" href="#">All Categories</a></li>
													<li><a class="clc" href="#">Computers</a></li>
													<li><a class="clc" href="#">Laptops</a></li>
													<li><a class="clc" href="#">Cameras</a></li>
													<li><a class="clc" href="#">Hardware</a></li>
													<li><a class="clc" href="#">Smartphones</a></li>
												</ul>
											</div>
										</div>
										<button type="submit" class="header_search_button trans_300" value="Submit"><img src="assets/images/catalog/search.png" alt=""></button>
									</form>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
		</div>
		
		<!-- Main Navigation -->

		<nav class="main_nav">
			<div class="container">
				<div class="row">
					<div class="col">
						
						<div class="main_nav_content d-flex flex-row">

							<!-- Categories Menu -->

							<div class="cat_menu_container">
								<div class="cat_menu_title d-flex flex-row align-items-center justify-content-start">
									<div class="cat_burger"><span></span><span></span><span></span></div>
									<div class="cat_menu_text">categories</div>
								</div>

								<ul class="cat_menu">
									<li><a href="#">PTR (Part Trial)<i class="fas fa-chevron-right ml-auto"></i></a></li>
									<li><a href="#">ITC (IT & Computer)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">EMC (Employee Consumable)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">OFS (Office Equipment Stationary)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">RMS (Repair Maintenance Service)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">SER (SService)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">CCP (Chemical, Consumable & Packaging)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">RPM (Repair Maintenance Part)<i class="fas fa-chevron-right"></i></a></li>
									<li><a href="#">EQM (Equipment & Machine)<i class="fas fa-chevron-right"></i></a></li>
								</ul>
							</div>

							<!-- Main Nav Menu -->

							<div class="main_nav_menu ml-auto">
								<ul class="standard_dropdown main_nav_dropdown">
									
								</ul>
							</div>

							<!-- Menu Trigger -->

							<div class="menu_trigger_container ml-auto">
								<div class="menu_trigger d-flex flex-row align-items-center justify-content-end">
									<div class="menu_burger">
										<div class="menu_trigger_text">menu</div>
										<div class="cat_burger menu_burger_inner"><span></span><span></span><span></span></div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</nav>
		
		<!-- Menu -->

		<div class="page_menu">
			<div class="container">
				<div class="row">
					<div class="col">
						
						<div class="page_menu_content">
							
							<div class="page_menu_search">
								<form action="#">
									<input type="search" required="required" class="page_menu_search_input" placeholder="Search for products...">
								</form>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>

	</header>
	
	<!-- Banner -->

	<div class="banner" style="min-height: 500px">
		<div class="banner_background" style="background-image:url(assets/images/catalog/banner_background.jpg)"></div>
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="banner_product_image"><img src="assets/images/catalog/banner_new.png" alt=""></div>
				<div class="col-lg-5 offset-lg-4 fill_height">
					<div class="banner_content">
						<h1 class="banner_text">EPS Electronic Purchasing System</h1>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Deals of the week -->

	
	<div class="new_arrivals">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="tabbed_container">
						<div class="tabs clearfix tabs-right">
							<div class="new_arrivals_title">Tooling Equipment Sparepart</div>
							<ul class="clearfix">
								<li class="active"></li>
							</ul>
							<div class="tabs_line"><span></span></div>
						</div>
						<div class="row">
							<div class="col-lg-9" style="z-index:1;">

								<!-- Product Panel -->
								<div class="product_panel panel active">
									<div class="arrivals_slider slider">

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture1.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 150,000</div>
													<div class="product_name"><div><a href="product.html">Selector Switch</a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture2.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 150,000</div>
													<div class="product_name"><div><a href="product.html">Push Button</a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture3.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 1,520,000</div>
													<div class="product_name"><div><a href="product.html">AC Split</a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture4.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 25,000</div>
													<div class="product_name"><div><a href="product.html">Grind Stone</a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture5.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 9,730,000</div>
													<div class="product_name"><div><a href="product.html">Pump</a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>

										<!-- Slider Item -->
										<div class="arrivals_slider_item">
											<div class="border_active"></div>
											<div class="product_item is_new d-flex flex-column align-items-center justify-content-center text-center">
												<div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-slick" src="assets/images/catalog/Picture6.png" alt=""></div>
												<div class="product_content">
													<div class="product_price">Rp. 6,838,891</div>
													<div class="product_name"><div><a href="product.html">Air Cylinder </a></div></div>
													<div class="product_extras">
														<button class="product_cart_button">Add to Cart</button>
													</div>
												</div>
												<div class="product_fav"><i class="fas fa-heart"></i></div>
											</div>
										</div>
										
									</div>
									<div class="arrivals_slider_dots_cover"></div>
								</div>

							</div>

							<div class="col-lg-3">
								<div class="arrivals_single clearfix">
									<div class="d-flex flex-column align-items-center justify-content-center">
										<div class="arrivals_single_content">
											<div class="arrivals_single_category"><a href="#">Category</a></div>
											<div class="arrivals_single_name_container clearfix">
												<div class="arrivals_single_name"><a href="#">Tooling Equipment Sparepart</a></div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
								
					</div>
				</div>
			</div>
		</div>		
	</div>	
</div>
