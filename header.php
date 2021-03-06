<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $page_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- End Bootstrap CDN -->

    <!-- Font Awesome 5.6.3 -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <!-- End Font Awesome -->

    <link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />

    <!--PlotLy-->
    <!-- <script src="plotly-latest.min.js"></script> -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <!-- Arcgis -->
    <link rel="stylesheet" href="https://js.arcgis.com/3.27/dijit/themes/claro/claro.css">
    <link rel="stylesheet" href="https://js.arcgis.com/3.27/esri/css/esri.css">
    <script src="https://js.arcgis.com/3.27/" data-dojo-config="async:true"></script>

    <script src="js/main.js"></script>

    <style>
    #map {
        height: 100%;
        width: 100%
    }
    </style>
    
</head>
<body>
    <div class="topbar">
        <div class="container">
            <a href="index.php">
                <div class="logo">
                    <h3>Siret</h3>
                </div>
            </a>
            <nav class="navbar justify-content-center">
                <a class="nav-link <?= $active_map ?>" href="index.php">Map</a>
                <a class="nav-link <?= $active_incidents ?>" href="incidents.php">Incidents</a>
                <a class="nav-link <?= $active_account ?>" href="account.php">Account</a>

                <?php if (isset($_SESSION["token"]) && $_SESSION["type"] == "admin"): ?>
                    <a class="nav-link <?= $active_admin ?>" href="admin_panel.php">Admin Panel</a>
                <?php endif; ?>

                <?php if (!isset($_SESSION["token"])): ?>
                    <a class="nav-link <?= $active_register ?>" href="register.php">Register</a>
                <?php endif; ?>
            </nav>
            <div class="clearfix"></div>
        </div>
    </div>