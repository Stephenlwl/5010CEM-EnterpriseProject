<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/mainpage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/publicDefault.css">
    <!-- Link to About Us CSS -->
    <link rel="stylesheet" href="css/aboutus.css">
    <body class="aboutus-page">

</head>
<body>
    <?php
        $IPATH = $_SERVER["DOCUMENT_ROOT"] . "/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH . "nav.php");
    ?>

    <!-- About Us Section -->
    <section class="about-section">
        <div class="container">
            <h1>---About Us---</h1>
            <p>Fuel Your Day with the Perfect Brew of Ideas.</p>
            <a href="#menu-section" class="btn left-aligned">Get Started</a>

        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu-section" class="menu-section"> <!-- Added id here -->
    <div class="container">
        <h1>Our Menu</h1>
        <p>It is more than just coffee; we provide an exquisite selection of refreshments, snacks, and delicacies designed to delight your palate and ensure you enjoy an exceptional experience with us.</p>
        <div class="menu-grid">
    <div class="menu-item espresso-nulla">
        <h2>Tiramisu</h2>
        <p></p>
    </div>
    <div class="menu-item choco-latte">
        <h2>Burnt Cheese Cake</h2>
        <p></p>
    </div>
    <div class="menu-item avocado-squash">
        <h2>Double Chocolate Cake</h2>
        <p></p>
    </div>
    <div class="menu-item ice-latte-berry">
        <h2>Americano</h2>
        <p></p>
    </div>
    <div class="menu-item french-fries">
        <h2>Mocha</h2>
        <p></p>
    </div>
    <div class="menu-item choco-cookies">
        <h2>Matcha Espresso</h2>
        <p></p>
    </div>
</div>

    </div>
</section>




    <!-- Our Team Section -->
<section class="our-team-section">
    <h2>Our Team</h2>
    <div class="team-images">
        <div class="team-image-left"></div>
        <div class="team-text">
            In every bean, a story's told, of warmth and love, both bold and bold. Come sip and stay, don’t rush away, where coffee and connections play.
            <div class="small-text">Rimberio Cafe</div> <!-- Small text below -->
            <div class="owner-text">Desmond. Woei Liang. Ee Leong. Owner.</div> <!-- Small text below -->
        </div>
        <div class="team-image-right"></div>
    </div>
</section>





    <!-- Mission Section -->
<!-- Mission Section -->
<section class="mission-section">
    <div class="slideshow-container">
        <div class="slide fade"></div>
        <div class="slide fade"></div>
        <div class="slide fade"></div>
        <div class="slide fade"></div>
        <div class="slide fade"></div>
    </div>
    <div class="text-container">
        <h1></h1>
        <div class="mission-text-container">
            <div class="mission-text">
                <h2>Welcome!</h2>
                <h3>We serve the richest coffee in the city!</h3>
            </div>
            <div class="mission-text">
                <h4>Our Mission</h4>
                <p>Our purpose is clear: to dedicate our passion and craft to helping customers lead a wholesome, vibrant, and fulfilling life. From the very beginning of Rimberio Cafe, we recognized the unique value we could bring through each carefully crafted cup. We’ve always believed that coffee is more than just a drink—it’s a way to inspire moments of connection, creativity, and well-being. This vision drives us to continually grow, sharing our thoughtfully brewed coffees with more local communities, where we hope to create spaces that nourish both the body and the soul.</p>
            </div>
            <div class="mission-text">
                <h5>Creating Experiences</h5>
                <p>At Rimberio Cafe, we strive to create a warm, welcoming, and inviting atmosphere where every guest feels at home. From the moment you walk through our doors, our dedicated and friendly staff is here to ensure your experience is nothing short of delightful. We’re passionate about more than just coffee; we’re committed to providing exceptional service, building connections, and making sure each visit leaves you with a sense of comfort and happiness. Whether you're here for a quick cup or to relax and unwind, our goal is always to serve you with care and send you off with a smile, eager to return for more.</p>
            </div>
        </div>
    </div>
</section>

    <script>
    let currentIndex = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlides() {
        slides.forEach((slide, index) => {
            slide.style.opacity = index === currentIndex ? '1' : '0';
        });
        currentIndex = (currentIndex + 1) % slides.length; // Loop back to the first slide
    }

    setInterval(showSlides, 8000); // Change slide every 8 seconds
    showSlides(); // Initial call to show the first slide
</script>

</body>
</html>
