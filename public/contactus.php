<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/publicDefault.css">
    <!-- Link to Contact Us CSS -->
    <link rel="stylesheet" href="css/contactus.css">
</head>
<body>
    <!-- Header (Included via PHP) -->
    <?php
        $IPATH = $_SERVER["DOCUMENT_ROOT"] . "/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH . "nav.php");
    ?>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contact Info</h3>
                <p><strong>Email:</strong> RimberioCafe@email.com</p>
                <p><strong>Phone:</strong> 60 11-2424 8888</p>
                <p><strong>Address:</strong> Batu Kawan, Kuala Lumpur</p>
                <h3>Opening Hours</h3>
                <p>Monday - Friday: 8:00 AM - 7:00 PM</p>
                <p>Saturday: 8:00 AM - 7:00 PM</p>
                <p>Sunday: Closed</p>
            </div>

            <div class="contact-form">
                <h3>Send us a message</h3>
                <form>
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" required>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Map Section -->
<section class="map-section">
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d47434.62323616929!2d112.73109163417168!3d-7.3143723170104025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fbfc845d708b%3A0x3403e532bf7c4c17!2sRimberio%20Food%20%26%20Drink!5e0!3m2!1sen!2smy!4v1729435112300!5m2!1sen!2smy" 
        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>



    <!-- Footer -->
    <?php include($IPATH . "footer.html"); ?>
</body>
</html>
