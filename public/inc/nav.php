<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 50px;
    background-color: #e0e0e0;
}

header nav {
    display: flex;
    flex-grow: 1; /* Ensures the navigation takes up available space */
    justify-content: space-between; /* Space between left nav and right header */
}

header nav .left-nav, header nav .right-nav {
    display: flex;
    gap: 20px; /* Adjusts space between individual menu items */
}

header nav ul {
    list-style: none;
    display: flex;
    gap: 20px; /* Space between items */
}

header nav ul li a {
    text-decoration: none;
    color: #000;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    flex-grow: 0;
    text-align: center; /* Center the logo */
    margin: 0 100px; /* Adjust spacing around the logo */
}

main {
    display: flex;
    height: 100vh;
}

.left-section {
    width: 50%;
    background: url('images/loginbackground.jpg') no-repeat center center;
    background-size: cover;
    padding: 50px;
    color: #fff;
}




.left-section .hero-text h1 {
    font-size: 48px;
    margin-bottom: 20px;
    color: black;
}

.left-section .hero-text p {
    font-size: 18px;
    line-height: 1.5;
}

.right-section {
    width: 50%;
    padding: 50px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
        </style>
    </head>
    <body>
    <header>
    <nav>
        <div class="left-nav">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Menu</a></li>
            </ul>
        </div>
        
        <div class="logo">
            Logo
        </div>
        
        <div class="right-nav">
            <ul>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Cart</a></li>
                <li><a href="#">User 1</a></li>
            </ul>
        </div>
    </nav>
</header>
    </body>
</html>