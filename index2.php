<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/cwu/CSS/index-mejorado.css">
    <title>CWU - Celebrate with US</title>
</head>
<body>
    <!-- Vídeo de fondo -->
    <video src="./CSS/Img/confeti.mp4" autoplay loop muted class="video-fondo"></video>
 
    <!-- Header -->
    <?php include('./inc/header.php') ?>
 
    <!-- Sección Hero / Banner Principal -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Bienvenido a Celebrate with US</h1>
            <p class="hero-subtitle">Planifica, organiza y celebra momentos especiales con tu comunidad</p>
            <div class="hero-buttons">
                <a href="#opciones" class="btn btn-primary-hero">Explorar secciones</a>
                <?php if (!$clienteLogueado): ?>
                    <a href="/cwu/Vistas/RegistroView.php" class="btn btn-secondary-hero">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
 
    <!-- Sección de opciones principales -->
    <section id="opciones" class="opciones-section">
        <div class="container-fluid">
            <h2 class="section-title">Explora nuestras secciones</h2>
            
            <div class="cards-container">
                <!-- Tarjeta 1: La Clínica -->
                <div class="card-opcion">
                    <a href="./Vistas/Sala1View.php" class="card-link">
                        <div class="card-image">
                            <img src="./CSS/Img/SalaClinica.png" alt="La Clínica" class="card-img">
                            <div class="card-overlay"></div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">La Clínica</h3>
                            <p class="card-description">Servicios de salud y bienestar especializados</p>
                            <span class="card-arrow">Explorar →</span>
                        </div>
                    </a>
                </div>
 
                <!-- Tarjeta 2: ¡Feliz Cumpleaños! -->
                <div class="card-opcion">
                    <a href="./Vistas/CumpleView.php" class="card-link">
                        <div class="card-image">
                            <img src="./CSS/Img/feliz.png" alt="¡Feliz Cumpleaños!" class="card-img">
                            <div class="card-overlay"></div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">¡Feliz Cumpleaños!</h3>
                            <p class="card-description">Planes y celebraciones para tu día especial</p>
                            <span class="card-arrow">Explorar →</span>
                        </div>
                    </a>
                </div>
 
                <!-- Tarjeta 3: La Biblioteca -->
                <div class="card-opcion">
                    <a href="./Vistas/Sala2View.php" class="card-link">
                        <div class="card-image">
                            <img src="./CSS/Img/SalaLibreria.png" alt="La Biblioteca" class="card-img">
                            <div class="card-overlay"></div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">La Biblioteca</h3>
                            <p class="card-description">Ideas, recursos e inspiración para tus eventos</p>
                            <span class="card-arrow">Explorar →</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Sección de características (opcional) -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">¿Por qué elegirnos?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎉</div>
                    <h4>Fácil de usar</h4>
                    <p>Interfaz intuitiva para organizar tus celebraciones sin complicaciones</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h4>Colaborativo</h4>
                    <p>Invita amigos y colabora en la organización de eventos</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💡</div>
                    <h4>Ideas inspiradoras</h4>
                    <p>Accede a nuestra biblioteca con miles de ideas y recursos</p>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Footer -->
    <?php include('./inc/footer.php') ?>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NJTJE+1ZVTWqn2jBJersWQinfLJ" crossorigin="anonymous"></script>
</body>
</html>