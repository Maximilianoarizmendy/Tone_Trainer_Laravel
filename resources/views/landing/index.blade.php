<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tone Trainer - Plataforma de Bienestar | Entrenamiento y Nutrición</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Tone Trainer: plataforma integral de entrenamiento personalizado, nutrición profesional y seguimiento del progreso.">
    <meta name="theme-color" content="#FF4500">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-removebg-preview.png') }}">
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@400;500;700&family=Teko:wght@300..700&display=swap" rel="stylesheet">
    {{-- Icons --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Libraries --}}
    <link rel="stylesheet" href="{{ asset('lib/animate/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}">
    {{-- Bootstrap & Style --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

{{-- Navbar --}}
<div class="container-fluid header-top">
    <div class="nav-shaps-2"></div>
    <div class="container d-flex align-items-center">
        <div class="d-flex align-items-center h-100">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" style="height:125px;">
                <img src="{{ asset('img/logo-removebg-preview.png') }}" alt="Logo Tone Trainer" style="height:150px;margin-right:1px;object-fit:contain;">
                <h1 class="mb-0" style="color:#FF4500;font-weight:700;">Tone Trainer</h1>
            </a>
        </div>
        <div class="w-100 h-100">
            <div class="topbar px-0 py-2 d-none d-lg-block" style="height:45px;">
                <div class="row gx-0 align-items-center">
                    <div class="col-lg-8 text-center text-lg-center mb-lg-0">
                        <div class="d-flex flex-wrap">
                            <div class="pe-4"><a href="mailto:tone_trainer0@gmail.com" class="text-muted small"><i class="fas fa-envelope text-primary me-2"></i>tone_trainer0@gmail.com</a></div>
                            <div><span class="text-muted small"><i class="fa fa-clock text-primary me-2"></i>Lun - Sáb: 8:00 - 19:00</span></div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <div class="d-flex justify-content-end">
                            <div class="d-flex pe-3">
                                <a class="btn p-0 text-primary me-3" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn p-0 text-primary me-3" href="#"><i class="fab fa-instagram"></i></a>
                                <a class="btn p-0 text-primary me-0" href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-bar px-0 py-lg-0" style="height:80px;">
                <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav mx-0 mx-lg-auto">
                            <a href="{{ route('home') }}" class="nav-item nav-link active">Inicio</a>
                            <a href="#sobre-nosotros" class="nav-item nav-link">Acerca</a>
                            <a href="#servicios" class="nav-item nav-link">Servicios</a>
                            <a href="#equipo" class="nav-item nav-link">Equipo</a>
                            <div class="nav-btn ps-3">
                                @auth
                                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary py-2 px-4 ms-0 ms-lg-3">
                                        <span>Mi Dashboard</span>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4 ms-0 ms-lg-3">
                                        <span>Iniciar sesión</span>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

{{-- Hero Carousel --}}
<div class="header-carousel owl-carousel overflow-hidden bg-dark">
    <div class="header-carousel-item hero-section">
        <div class="hero-bg-half-1"></div>
        <div class="carousel-caption">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7 animated fadeInLeft">
                        <div class="text-sm-center text-md-start">
                            <h4 class="text-primary text-uppercase fw-bold mb-4">Bienvenido a Tone Trainer</h4>
                            <h1 class="display-1 text-white mb-4">Plataforma integral de entrenamiento, nutrición y seguimiento</h1>
                            <p class="mb-5 fs-5">Tone Trainer combina metodologías validadas por expertos en deporte y nutrición con tecnología que facilita el seguimiento de tu progreso.</p>
                            <div class="d-flex justify-content-center justify-content-md-start flex-shrink-0 mb-4">
                                <a class="btn btn-primary py-3 px-4 px-md-5 ms-2" href="#servicios"><span>Más información</span></a>
                                @guest
                                <a class="btn btn-dark py-3 px-4 px-md-5 me-2" href="{{ route('register') }}"><span>Registrarse gratis</span></a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-carousel-item hero-section">
        <div class="hero-bg-half-2"></div>
        <div class="carousel-caption">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7 animated fadeInLeft">
                        <div class="text-sm-center text-md-start">
                            <h4 class="text-primary text-uppercase fw-bold mb-4">Soluciones para tu rendimiento</h4>
                            <h1 class="display-2 text-white mb-4">Entrenamientos personalizados y análisis de resultados</h1>
                            <p class="mb-5 fs-5">Accede a planes adaptativos, seguimiento de métricas relevantes y soporte profesional en una sola plataforma.</p>
                            <div class="d-flex justify-content-center justify-content-md-start flex-shrink-0 mb-4">
                                <a class="btn btn-primary py-3 px-4 px-md-5 ms-2" href="#servicios"><span>Conocer servicios</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- About --}}
<div id="sobre-nosotros" class="container-fluid about pt-5">
    <div class="container pt-5">
        <div class="row g-5">
            <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="about-content h-100">
                    <h4 class="text-primary">Acerca de Tone Trainer</h4>
                    <h1 class="display-4 text-white mb-4">Combinamos experiencia humana y tecnología para maximizar tu bienestar.</h1>
                    <p class="mb-4">Ofrecemos planes de entrenamiento y nutrición basados en evidencia, seguimiento de progreso y herramientas analíticas que facilitan la toma de decisiones.</p>
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-6">
                            @guest
                            <a href="{{ route('register') }}" class="btn btn-primary py-3 px-5"><span>Registrarse gratis</span></a>
                            @else
                            <a href="{{ route('dashboard.index') }}" class="btn btn-primary py-3 px-5"><span>Ir al Dashboard</span></a>
                            @endguest
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex flex-shrink-0 ps-4">
                                <div class="d-flex flex-column ms-3">
                                    <span>Asesoría telefónica</span>
                                    <a href="tel:+573014085511"><span class="text-white">Tel: +57 301 408 5511</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInUp" data-wow-delay="0.2s">
                <div class="about-img h-100">
                    <div class="about-img-inner d-flex h-100">
                        <img src="{{ asset('img/about-2.png') }}" class="img-fluid w-100" style="object-fit:cover;" alt="Tone Trainer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Services --}}
<div id="servicios" class="container-fluid courses overflow-hidden py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width:800px;">
            <h4 class="text-primary">Servicios destacados</h4>
            <h1 class="display-4 text-white mb-4">Programas y sesiones disponibles</h1>
            <p class="text-white mb-0">Entrenamiento general, fuerza, resistencia y planes personalizados.</p>
        </div>
        <div class="row gy-4 gx-0 justify-content-center">
            @foreach([['icon-1.png','Planes personalizados','Entrenamientos diseñados según tu nivel, objetivos y disponibilidad.'],['icon-2.png','Programa de fuerza','Entrenamiento estructurado para incrementar fuerza y resistencia.'],['icon-6.png','Plan de nutrición','Planes alimentarios ajustados a objetivos de salud y rendimiento.']] as [$icon, $title, $desc])
            <div class="col-md-6 col-lg-4 wow fadeInUp">
                <div class="courses-item">
                    <div class="courses-item-inner p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="courses-icon-img p-3">
                                <img src="{{ asset('img/' . $icon) }}" class="img-fluid" alt="{{ $title }}">
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="d-inline-block h4 mb-3">{{ $title }}</a>
                        <p class="mb-4">{{ $desc }}</p>
                        <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4"><span>Más info</span></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Team --}}
<div id="equipo" class="container-fluid team py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width:800px;">
            <h4 class="text-primary">Nuestro equipo</h4>
            <h1 class="display-4 mb-4">Conoce al equipo multidisciplinario</h1>
            <p class="mb-0">Entrenadores, nutricionistas e ingenieros que trabajan para ofrecer soluciones seguras y efectivas.</p>
        </div>
        <div class="row gy-5 gy-lg-4 gx-4">
            @foreach([['team-1.jpg','María López','Nutricionista clínica'],['team-2.jpg','Andrés Pérez','Entrenador personal'],['team-3.jpg','Sofía Torres','Ingeniera de Software'],['team-4.jpg','Carlos Ruiz','Coach de bienestar']] as [$img, $name, $role])
            <div class="col-md-6 col-lg-3 wow fadeInUp">
                <div class="team-item">
                    <div class="team-img">
                        <img src="{{ asset('img/' . $img) }}" class="img-fluid w-100" alt="{{ $name }}">
                    </div>
                    <div class="team-content"><h4>{{ $name }}</h4><p class="mb-0">{{ $role }}</p></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="container-fluid explore py-5 wow zoomIn" data-wow-delay="0.2s">
    <div class="container py-5 text-center">
        <h1 class="display-1 text-white mb-4">Descubre Tone Trainer</h1>
        @guest
        <a class="btn btn-primary py-3 px-4 px-md-5" href="{{ route('register') }}"><span>Comenzar gratis →</span></a>
        @else
        <a class="btn btn-primary py-3 px-4 px-md-5" href="{{ route('dashboard.index') }}"><span>Ir a mi Dashboard →</span></a>
        @endguest
    </div>
</div>

{{-- Footer --}}
<div class="container-fluid footer py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6 col-lg-4">
                <h4 class="text-white mb-4">Tone Trainer</h4>
                <p>Plataforma integral de entrenamiento personalizado, nutrición profesional y seguimiento del progreso.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-primary btn-sm-square"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <h4 class="text-white mb-4">Acceso Rápido</h4>
                <a class="btn btn-link" href="{{ route('login') }}">Iniciar Sesión</a><br>
                <a class="btn btn-link" href="{{ route('register') }}">Registrarse</a><br>
                <a class="btn btn-link" href="#servicios">Servicios</a>
            </div>
            <div class="col-md-6 col-lg-4">
                <h4 class="text-white mb-4">Contacto</h4>
                <p><i class="fa fa-envelope text-primary me-2"></i>tone_trainer0@gmail.com</p>
                <p><i class="fa fa-phone text-primary me-2"></i>+57 301 408 5511</p>
                <p><i class="fa fa-clock text-primary me-2"></i>Lun - Sáb: 8:00 - 19:00</p>
            </div>
        </div>
    </div>
    <div class="container text-center border-top border-secondary pt-4 mt-4">
        <p class="mb-0">© {{ date('Y') }} Tone Trainer. Todos los derechos reservados.</p>
    </div>
</div>

{{-- Scripts --}}
<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('lib/wow/wow.min.js') }}"></script>
<script src="{{ asset('lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
