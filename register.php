<?php
    session_start();

    if (isset($_POST["action"]) && $_POST["action"] == "register") {
        /* Register a new "volunteer" user */
        if (!isset($_POST["username"]) ||
                !isset($_POST["password"]) ||
                !isset($_POST["name"]) ||
                !isset($_POST["surname"]) ||
                !isset($_POST["email"])) {
            $error = "Please fill in all of the required fields";

        } else {
            if (isset($_POST["city"])) {
                $city = $_POST["city"];
            } else {
                $city = "";
            }
            $username = $_POST["username"];
            $password = $_POST["password"];
            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $email = $_POST["email"];
            $type = 'volunteer';

            $url = 'https://serene-cove-78266.herokuapp.com/signup';
            $data = array(
                'username' => $username,
                'password' => $password,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'city' => $city,
                'type' => $type,
            );

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data)
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) {
                $error = "Connection refused by server. Please try again.";
            } else {
                $res = json_decode($result, TRUE);

                if (isset($res["error"])) {
                    $error = $res["error"];
                } elseif (isset($res["message"])) {
                    $message = $res["message"];
                }
            }
        }
    }
?>

<?php
    include "variables.php";
    $page_title = "Register";
    $active_register = "active";
    include "header.php";
?>

<?php if (isset($_SESSION["token"])): ?>
    <div class="container">
        <div class="account-details">
            <p>You are already logged in.</p>
            <a href="account.php?logout" class="btn btn-primary mt-2">Logout</a>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <div class="center-content text-center">
            <form class="form-signin" action="register.php" method="POST">
                <img class="form-logo mb-3" src="img/round_logo.png">
                <h1 class="h3 mb-3 font-weight-normal">Register a new volunteer account</h1>

                <?php if (isset($error)): ?>
                    <p class="error-msg"><?= $error ?></p>
                <?php elseif (isset($message)): ?>
                    <p class="success-msg"><?= $message ?></p>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="text-left">
                            <label for="username">Username <span class="red">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
                            <label for="password">Password <span class="red">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                            <label for="name">Name <span class="red">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Name" required>
                            <label for="surname">Surname <span class="red">*</span></label>
                            <input type="text" id="surname" name="surname" class="form-control" placeholder="Surname" required>
                            <label for="email">Email <span class="red">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control" placeholder="City">
                            <input type="hidden" name="action" value="register">
                        </div>
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block mt-3" type="submit">Sign up</button>
                <p class="mt-5 mb-3 text-muted">&copy; 2019</p>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
    include "footer.php";
?>