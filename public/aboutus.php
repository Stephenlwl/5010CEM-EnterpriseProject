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
    <link rel="stylesheet" href="css/publicDefault.css">
    <!-- Link to About Us CSS -->
    <link rel="stylesheet" href="css/aboutus.css">
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
        <h2>Ice Latte Berry</h2>
        <p></p>
    </div>
    <div class="menu-item french-fries">
        <h2>French Fries</h2>
        <p></p>
    </div>
    <div class="menu-item choco-cookies">
        <h2>Choco Cookies</h2>
        <p></p>
    </div>
</div>

    </div>
</section>




    <!-- Our Team Section -->
    <section class="our-team-section">
    <h2>Our Team</h2>
    <div class="team-images">
        <div class="team-image-left">
        </div>
        <div class="team-image-right">
        </div>
    </div>
</section>


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
        <h1>Our Mission</h1>
        <p>We strive to serve high-quality coffee and food while supporting sustainability and the local community.</p>
    </div>
</section>


    <!-- Temporary Extra Section for Testing Scroll -->
    <section class="extra-section">
        <div class="container">
            <h1>Testing Scroll Section</h1>
            <p>This is a temporary section with extra content. You can remove it once scrolling works.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris.</p>
            <p>Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>
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
