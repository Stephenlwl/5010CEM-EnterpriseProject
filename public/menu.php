
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MENU</title>
    <link rel="stylesheet" href="css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        main {
            flex: 1;
        }

        .col-sm-8 {
            padding-bottom: 100px;
        }

        iframe {
            width: 100%;
            border: none;
        }
    </style>

</head>

<body>
<?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH."nav.php"); 
    ?>
    
    <main>
        <div class="container mt-5">
            <div class="row">
                <!--Account Settings Sidebar -->
                <div class="col-sm-3">
                    <h3>Welcome to our menu</h3>
                    <div class="profile-settings-frame">
                        <h3>MENU</h3>
                        <hr>

                        <button class="btn btn-secondary d-lg-none mb-3" type="button" data-bs-toggle="collapse"
                            data-bs-target="#profile-nav" aria-expanded="false" aria-controls="profile-nav">
                            Menu
                        </button>

                        <div id="profile-nav" class="collapse d-lg-block">
                            <ul class="nav nav-pills flex-column">
                                <label for="profile">Coffee</label>
                                <li class="nav-item">
                                    <a class="nav-link" href="menu/coffeeMenu.php" target="profile-iframe">coffee</a>
                                </li>
                                <label for="profile">Desserts</label>
                                <li class="nav-item">
                                    <a class="nav-link" href="menu/dessertMenu.php" target="profile-iframe">Desserts</a>
                                </li>
                                <label for="profile">Goodies</label>
                                <li class="nav-item">
                                    <a class="nav-link" href="" target="profile-iframe">Goodies</a>
                                <label for="profile">Favorite List</label>
                                <li class="nav-item">
                                    <a class="nav-link" href="" target="profile-iframe">Favorites</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--dynamic right screen -->
                <div class="col-sm-9 d-flex" style="height: 100vh;">
                    <iframe width="100%" height="100%" src="userProfileSettings/editProfile.php" frameborder="0" name="profile-iframe" style="flex-grow: 1;"></iframe>
                </div>
            </div>
        </div>
    </main>
<!-- footer -->
<?php include($IPATH."footer.html"); ?>

</body>

</html>