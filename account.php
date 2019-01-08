<?php
    session_start();

    if (isset($_GET["logout"])) {
        session_unset();
    } else if (isset($_POST["username"]) &&
            isset($_POST["password"])) {            

        /* Log the user in */
        $username = $_POST["username"];
        $password = $_POST["password"];
        $url = 'https://serene-cove-78266.herokuapp.com/login';
        $data = array('username' => $username, 'password' => $password);

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
            } else {
                $_SESSION["username"] = $res["username"];
                $_SESSION["token"] = $res["token"];
                $_SESSION["type"] = $res["type"];
                $_SESSION["is_valid"] = $res["is_valid"];
            }
        }

    }
?>

<?php
    include "variables.php";
    $page_title = "Your Account";
    $active_account = "active";
    include "header.php";
?>

<?php if (isset($_SESSION["token"])): ?>
    <?php
        /* Get user data */
        $url = 'https://serene-cove-78266.herokuapp.com/get_user_data';
        $data = array('username' => $_SESSION['username'], 'token' => $_SESSION['token']);

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
            }

            $is_valid = "";
            if ($res["is_valid"]) {
                $is_valid = "<span class='label bg-green'>Yes</span>";
            } else {
                $is_valid = "<span class='label bg-red'>No</span>";
            }

            if ($res['type'] == "admin") {
                $res['type'] = "<span class='label bg-purple'>Admin</span>";
            } else {
                $res['type'] = "<span class='label bg-light-blue'>Volunteer</span>";
            }
        }
    ?>
    <div class="container">
        <div class="account-details">
            <h2 class="page-title">My Account</h2>
            
            <?php if (isset($error)): ?>
                <p class="error-msg"><?= $error ?></p>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <table>
                            <tbody>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?= $res["name"] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Surame:</strong></td>
                                    <td><?= $res["surname"] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td><?= $res["city"] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?= $res["email"] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Account type:</strong></td>
                                    <td><?= $res["type"] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Validated:</strong></td>
                                    <td><?= $is_valid ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <a href="account.php?logout" class="btn btn-primary mt-2">Logout</a>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <div class="center-content text-center">
            <form class="form-signin" action="account.php" method="POST">
                <img class="form-logo mb-3" src="img/round_logo.png">
                <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>

                <?php if (isset($error)): ?>
                    <p class="error-msg"><?= $error ?></p>
                <?php endif; ?>

                <label for="username" class="sr-only">Email address</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <div class="checkbox mb-3 mt-3">
                    <label>
                    <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                <p class="mt-5 mb-3 text-muted">&copy; 2019</p>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
    include "footer.php";
?>