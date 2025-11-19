
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pil Andina - Nutricion Boliviana Premium</title>
    <link rel="stylesheet" href="{{ asset('landing/landing.css') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4e6baf',
                        'primary-dark': '#3a5186',
                        'primary-light': '#86acd4',
                        accent: '#42568b',
                    }
                }
            }
        }
    </script>
</head>
<body class="text-white">
    <nav class="fixed top-0 left-0 w-full z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-light to-primary flex items-center justify-center">
                            <i class="fas fa-industry text-white text-xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight">
                        Pil<span class="bg-gradient-to-r from-primary-light to-white bg-clip-text text-transparent">Andina</span>
                    </a>
                </div>
                <button id="menu-btn" class="lg:hidden text-white hover:text-primary-light transition-colors">
                    <i class="ri-menu-line text-3xl"></i>
                </button>
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#home" class="text-sm font-medium hover:text-primary-light transition-colors">Inicio</a>
                    <a href="#products" class="text-sm font-medium hover:text-primary-light transition-colors">Productos</a>
                    <a href="#promotions" class="text-sm font-medium hover:text-primary-light transition-colors">Ofertas</a>
                    <a href="#about" class="text-sm font-medium hover:text-primary-light transition-colors">Nosotros</a>
                    <a href="#testimonials" class="text-sm font-medium hover:text-primary-light transition-colors">Opiniones</a>
                    <a href="{{ url('/login') }}" class="btn-glass px-6 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2">
                        <i class="ri-login-box-line"></i>
                        <span>Ingresar</span>
                    </a>
                </div>
            </div>
            <div id="nav-links" class="hidden lg:hidden pb-6">
                <div class="flex flex-col gap-4">
                    <a href="#home" class="text-sm font-medium hover:text-primary-light transition-colors py-2">Inicio</a>
                    <a href="#products" class="text-sm font-medium hover:text-primary-light transition-colors py-2">Productos</a>
                    <a href="#promotions" class="text-sm font-medium hover:text-primary-light transition-colors py-2">Ofertas</a>
                    <a href="#about" class="text-sm font-medium hover:text-primary-light transition-colors py-2">Nosotros</a>
                    <a href="#testimonials" class="text-sm font-medium hover:text-primary-light transition-colors py-2">Opiniones</a>
                    <a href="{{ url('/login') }}" class="btn-glass px-6 py-2.5 rounded-xl font-medium text-sm flex items-center justify-center gap-2">
                        <i class="ri-login-box-line"></i>
                        <span>Ingresar</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section id="home" class="min-h-screen flex items-center pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto w-full">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8 text-center lg:text-left">
                    <div class="inline-block glass px-6 py-2 rounded-full text-sm font-medium mb-4">
                        &#127463;&#127476; Orgullosamente Boliviano
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                        Nutricion de
                        <span class="block bg-gradient-to-r from-primary-light via-white to-primary-light bg-clip-text text-transparent">
                            Clase Mundial
                        </span>
                    </h1>
                    <p class="text-xl text-white/80 leading-relaxed max-w-xl">
                        Mas de 30 a&ntilde;os elaborando lacteos premium que nutren a miles de familias bolivianas. Calidad, frescura y tradicion en cada producto.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/login') }}" class="glass-strong px-8 py-4 rounded-2xl font-semibold hover:scale-105 transition-transform flex items-center justify-center gap-3">
                            <span>Explorar Productos</span>
                            <i class="ri-arrow-right-line"></i>
                        </a>
                        <a href="#about" class="btn-glass px-8 py-4 rounded-2xl font-semibold hover:scale-105 transition-transform flex items-center justify-center gap-3">
                            <span>Conocer Mas</span>
                        </a>
                    </div>
                </div>
                <div class="relative animate-float">
                    <div class="glass-strong rounded-[3rem] p-8 shimmer-effect">
                        <img src="{{ asset('landing/landing_assets/header.png') }}" alt="Productos Pil Andina" class="w-full rounded-3xl">
                    </div>
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary-light/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-accent/20 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </section>
    <section id="products" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl lg:text-5xl font-black">Nuestros Productos Estrella</h2>
                <p class="text-xl text-white/70 max-w-2xl mx-auto">
                    Descubre lacteos frescos y nutritivos, elaborados con los mas altos estandares de calidad
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">20% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-1.png') }}" alt="Pilfrut Manzana" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Pilfrut Manzana 800ml</h3>
                    <p class="text-white/70 mb-4">Bebida lactea refrescante con sabor natural a manzana</p>
                    <div class="flex gap-1 mb-4">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">3.60</span>
                            <span class="text-white/50 line-through ml-2">4.50</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">
                            <i class="ri-shopping-cart-line mr-2"></i>A&ntilde;adir
                        </button>
                    </div>
                </div>
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">15% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-2.png') }}" alt="Dulce de Leche" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Dulce de Leche 500g</h3>
                    <p class="text-white/70 mb-4">Cremoso y delicioso, perfecto para tus postres</p>
                    <div class="flex gap-1 mb-4">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">15.30</span>
                            <span class="text-white/50 line-through ml-2">18</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">
                            <i class="ri-shopping-cart-line mr-2"></i>A&ntilde;adir
                        </button>
                    </div>
                </div>
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">10% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-3.png') }}" alt="Pura Vida Tumbo" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Pura Vida Frutss Tumbo 3L</h3>
                    <p class="text-white/70 mb-4">Bebida refrescante con vitamina C y sabor exotico</p>
                    <div class="flex gap-1 mb-4">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">13.50</span>
                            <span class="text-white/50 line-through ml-2">15</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">
                            <i class="ri-shopping-cart-line mr-2"></i>A&ntilde;adir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="promotions" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl lg:text-5xl font-black">Ofertas Irresistibles</h2>
                <p class="text-xl text-white/70 max-w-2xl mx-auto">
                    Aprovecha nuestros packs promocionales con descuentos exclusivos
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">25% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-1.png') }}" alt="Pack Pilfrut" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Pack Pilfrut Variado</h3>
                    <p class="text-white/70 mb-4">3 unidades de 800ml - Sabores variados</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">10.80</span>
                            <span class="text-white/50 line-through ml-2">13.50</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">Comprar</button>
                    </div>
                </div>
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">20% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-2.png') }}" alt="Combo Dulce" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Combo Dulce + Yogur</h3>
                    <p class="text-white/70 mb-4">La combinacion perfecta para tu familia</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">25.60</span>
                            <span class="text-white/50 line-through ml-2">32</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">Comprar</button>
                    </div>
                </div>
                <div class="glass-strong rounded-3xl p-6 product-card hover-lift">
                    <div class="discount-badge">15% Off</div>
                    <div class="product-img-wrapper mb-6">
                        <img src="{{ asset('landing/landing_assets/special-3.png') }}" alt="Pack Familiar" class="w-full h-56 object-cover rounded-2xl">
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Pack Familiar Pura Vida</h3>
                    <p class="text-white/70 mb-4">2 unidades de 3L - Rendidoras</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-bold">25.50</span>
                            <span class="text-white/50 line-through ml-2">30</span>
                            <span class="text-lg ml-1">Bs</span>
                        </div>
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">Comprar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative animate-float-delayed">
                    <div class="glass-strong rounded-[3rem] p-8">
                        <img src="{{ asset('landing/landing_assets/chef.png') }}" alt="Calidad PIL" class="w-full rounded-3xl">
                    </div>
                </div>
                <div class="space-y-6">
                    <h2 class="text-4xl lg:text-5xl font-black">Tradicion y Calidad Boliviana</h2>
                    <p class="text-xl text-white/80 leading-relaxed">
                        Desde hace mas de tres decadas, Pil Andina se ha consolidado como lider en la produccion de lacteos premium en Bolivia, combinando tecnicas artesanales con tecnologia de punta.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="ri-checkbox-circle-fill text-primary-light text-2xl mt-1"></i>
                            <span class="text-lg">Apoyo a productores y empleos locales</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-checkbox-circle-fill text-primary-light text-2xl mt-1"></i>
                            <span class="text-lg">Procesos certificados de calidad internacional</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-checkbox-circle-fill text-primary-light text-2xl mt-1"></i>
                            <span class="text-lg">Frescura garantizada en cada producto</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-checkbox-circle-fill text-primary-light text-2xl mt-1"></i>
                            <span class="text-lg">Compromiso con la sostenibilidad ambiental</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="testimonials" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-4xl lg:text-5xl font-black">Lo Que Dicen Nuestros Clientes</h2>
                <p class="text-xl text-white/70 max-w-2xl mx-auto">
                    Miles de familias bolivianas confian en nosotros cada dia
                </p>
            </div>
            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper pb-12">
                    <div class="swiper-slide">
                        <div class="glass-strong rounded-3xl p-8 h-full flex flex-col">
                            <p class="text-lg text-white/90 mb-6 flex-grow italic">
                                "Los productos Pilfrut son increibles. Mi familia los adora y siempre estan frescos. La calidad es excepcional."
                            </p>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('landing/landing_assets/client-1.jpg') }}" alt="Cliente" class="w-16 h-16 rounded-full">
                                <div>
                                    <h4 class="font-bold">David Lee</h4>
                                    <p class="text-white/60 text-sm">Cliente Frecuente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="glass-strong rounded-3xl p-8 h-full flex flex-col">
                            <p class="text-lg text-white/90 mb-6 flex-grow italic">
                                "El dulce de leche es simplemente perfecto. Lo uso en todas mis recetas y siempre queda delicioso."
                            </p>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('landing/landing_assets/client-2.jpg') }}" alt="Cliente" class="w-16 h-16 rounded-full">
                                <div>
                                    <h4 class="font-bold">Emily Johnson</h4>
                                    <p class="text-white/60 text-sm">Madre de Familia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="glass-strong rounded-3xl p-8 h-full flex flex-col">
                            <p class="text-lg text-white/90 mb-6 flex-grow italic">
                                "Pura Vida es mi bebida favorita despues del ejercicio. Refrescante y nutritiva."
                            </p>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('landing/landing_assets/client-3.jpg') }}" alt="Cliente" class="w-16 h-16 rounded-full">
                                <div>
                                    <h4 class="font-bold">Michael Thompson</h4>
                                    <p class="text-white/60 text-sm">Deportista</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="glass-strong rounded-3xl p-8 h-full flex flex-col">
                            <p class="text-lg text-white/90 mb-6 flex-grow italic">
                                "Calidad consistente en todos sus productos. Una marca en la que realmente se puede confiar."
                            </p>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('landing/landing_assets/client-1.jpg') }}" alt="Cliente" class="w-16 h-16 rounded-full">
                                <div>
                                    <h4 class="font-bold">Sara Gomez</h4>
                                    <p class="text-white/60 text-sm">Nutricionista</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <section class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass-strong rounded-3xl p-8 text-center hover-lift">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-light to-primary rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-shopping-cart-2-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Compra Facil</h3>
                    <p class="text-white/70 leading-relaxed mb-6">
                        Explora nuestro catalogo completo y a&ntilde;ade productos a tu carrito en segundos
                    </p>
                    <a href="/products" class="text-primary-light font-semibold hover:underline inline-flex items-center gap-2">
                        Explorar <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="glass-strong rounded-3xl p-8 text-center hover-lift">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-light to-primary rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-truck-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Entrega Rapida</h3>
                    <p class="text-white/70 leading-relaxed mb-6">
                        Recibe tus productos frescos en la puerta de tu casa con nuestra logistica eficiente
                    </p>
                    <a href="/order" class="text-primary-light font-semibold hover:underline inline-flex items-center gap-2">
                        Ordenar <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="glass-strong rounded-3xl p-8 text-center hover-lift">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-light to-primary rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-star-smile-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Califica y Gana</h3>
                    <p class="text-white/70 leading-relaxed mb-6">
                        Comparte tu experiencia y participa en sorteos exclusivos para clientes
                    </p>
                    <a href="/reviews" class="text-primary-light font-semibold hover:underline inline-flex items-center gap-2">
                        Participar <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="glass-strong rounded-[3rem] p-12 text-center space-y-8">
                <h2 class="text-4xl lg:text-5xl font-black">&iquest;Listo para Probar la Calidad?</h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Unete a miles de familias que disfrutan diariamente de nuestros productos lacteos premium
                </p>
                <a href="{{ url('/login') }}" class="inline-flex items-center gap-3 glass-strong px-10 py-5 rounded-2xl font-bold text-lg hover:scale-105 transition-transform">
                    <span>Comenzar Ahora</span>
                    <i class="ri-arrow-right-line text-xl"></i>
                </a>
            </div>
        </div>
    </section>
    <footer class="py-16 px-6 glass-nav mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
                <div class="space-y-4 lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-light to-primary rounded-2xl flex items-center justify-center">
                            <i class="fas fa-industry text-white text-xl"></i>
                        </div>
                        <a href="{{ url('/') }}" class="text-2xl font-bold">
                            Pil<span class="bg-gradient-to-r from-primary-light to-white bg-clip-text text-transparent">Andina</span>
                        </a>
                    </div>
                    <p class="text-white/70 leading-relaxed max-w-sm">
                        Lacteos frescos y nutritivos elaborados con orgullo boliviano. Comprometidos con la calidad y el bienestar de tu familia.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Productos</h4>
                    <ul class="space-y-2">
                        <li><a href="/products" class="text-white/70 hover:text-white transition-colors">Catalogo</a></li>
                        <li><a href="/new-arrivals" class="text-white/70 hover:text-white transition-colors">Novedades</a></li>
                        <li><a href="/best-sellers" class="text-white/70 hover:text-white transition-colors">Mas Vendidos</a></li>
                        <li><a href="/promotions" class="text-white/70 hover:text-white transition-colors">Ofertas</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Compa&ntilde;ia</h4>
                    <ul class="space-y-2">
                        <li><a href="/about" class="text-white/70 hover:text-white transition-colors">Nosotros</a></li>
                        <li><a href="/terms" class="text-white/70 hover:text-white transition-colors">Terminos</a></li>
                        <li><a href="/privacy" class="text-white/70 hover:text-white transition-colors">Privacidad</a></li>
                        <li><a href="/faq" class="text-white/70 hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contacto</h4>
                    <ul class="space-y-2">
                        <li><a href="/contact" class="text-white/70 hover:text-white transition-colors">Mensaje</a></li>
                        <li><a href="https://wa.me/123456789" class="text-white/70 hover:text-white transition-colors">WhatsApp</a></li>
                        <li><a href="https://facebook.com/pilandina" class="text-white/70 hover:text-white transition-colors">Facebook</a></li>
                        <li><a href="https://t.me/pilandina" class="text-white/70 hover:text-white transition-colors">Telegram</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-12">
                <div class="max-w-md mx-auto text-center space-y-4">
                    <h4 class="font-bold text-lg">Suscribete a Nuestro Boletin</h4>
                    <p class="text-white/70 text-sm">Recibe ofertas exclusivas y novedades</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="tu@email.com" class="flex-1 px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:outline-none focus:border-primary-light">
                        <button class="btn-glass px-6 py-3 rounded-xl font-medium">
                            Suscribir
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-white/60 text-sm">
                    &copy; {{ now()->year }} Pil Andina. Todos los derechos reservados. Hecho con &#10084;&#65039; en Bolivia
                </p>
            </div>
        </div>
    </footer>
    <button id="scrollTop" class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-to-br from-primary-light to-primary rounded-full flex items-center justify-center opacity-0 invisible transition-all hover:scale-110 z-40">
        <i class="ri-arrow-up-line text-2xl text-white"></i>
    </button>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const navLinks = document.getElementById('nav-links');
        menuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('hidden');
        });
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    navLinks.classList.add('hidden');
                }
            });
        });
        const scrollTopBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('opacity-0', 'invisible');
            } else {
                scrollTopBtn.classList.add('opacity-0', 'invisible');
            }
        });
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.animate-float, .animate-float-delayed');
            parallaxElements.forEach(el => {
                const speed = 0.05;
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        document.querySelectorAll('.product-card, .hover-lift').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            observer.observe(el);
        });
        document.querySelectorAll('.product-card').forEach((el, index) => {
            el.style.transitionDelay = `${index * 0.1}s`;
        });
    </script>
</body>
</html>

