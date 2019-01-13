<?php
    session_start();

    if (isset($_POST["action"]) && $_POST["action"] == "register") {
        /* Register a new "admin" user */

        if (!isset($_SESSION["type"]) || $_SESSION["type"] != "admin") {
            $error = "Only administrators can create other administrator accounts";

        } elseif (!isset($_POST["username"]) ||
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
            $type = 'admin';

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


    include "variables.php";
    $page_title = "Siret Map";
    $active_admin = "active";
    include "header.php";
?>

<div class="container">
    <h3 class="page-title">Admin Panel</h3>

    <?php if (!isset($_SESSION["token"])): ?>
        <p>Only administrators can access the admin panel.</p>
        <p>Please <a href="account.php">log in as an administrator <i class="fa fa-arrow-right"></i></a>.</p>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h5 class="title-small">User list</h5>
            </div>
            <div class="card-body">
                <table class="admin-userTable table table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>City</th>
                            <th>Email</th>
                            <th>Validated?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8">Please wait... Loading data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card admin-createAdminCard">
            <div class="card-header">
                <h5 class="title-small">Create new admin</h5>
            </div>
            <div class="card-body">
                <form class="form-createAdmin" action="admin_panel.php" method="POST">
                    <label for="username">Username <span class="red">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
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
                    <button class="btn btn-lg btn-primary btn-block mt-3" type="submit">Create</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function validateUser(username) {
        var postData = JSON.stringify({
            "token": "<?= $_SESSION['token'] ?>",
            "username": username
        });

        $.post("https://serene-cove-78266.herokuapp.com/validate_user", postData)
            .done(function(data) {
                if (data["error"]) {
                    alert(data["error"]);
                } else {
                    getUsers();
                    alert(data["message"]);
                }
            })
            .fail(function() {
                alert("Validate user: request failed. Please try again.");
            });
    }

    function invalidateUser(username) {
        var postData = JSON.stringify({
            "token": "<?= $_SESSION['token'] ?>",
            "username": username
        });

        $.post("https://serene-cove-78266.herokuapp.com/invalidate_user", postData)
            .done(function(data) {
                if (data["error"]) {
                    alert(data["error"]);
                } else {
                    getUsers();
                    alert(data["message"]);
                }
            })
            .fail(function() {
                alert("Invalidate user: request failed. Please try again.");
            });
    }

    function getUsers() {
        var postData = JSON.stringify({
            "token": "<?= $_SESSION['token'] ?>"
        });

        $.post("https://serene-cove-78266.herokuapp.com/get_users", postData)
            .done(function(data) {
                var tableBody = $(".admin-userTable > tbody");

                if (data["error"]) {
                    var errorMessage = "<tr><td colspan='8'>"+data["error"]+"</td></tr>";
                    tableBody.html(errorMessage);
                    return;
                } else {
                    var userData = "";

                    console.log(data);
                    for (var i = 0; i < data.length; i++) {
                        user = data[i];

                        var validated = "";
                        var btnValidate = "";
                        if (user['is_valid']) {
                            validated = "<span class='label bg-green'>Yes</span>";
                            if (user['type'] != "admin") 
                                btnValidate = `<button class="btn btn-danger admin-btnValidate" onclick="invalidateUser('${user['username']}')">Invalidate</button>`;
                        } else {
                            validated = "<span class='label bg-red'>No</span>";
                            if (user['type'] != "admin") 
                                btnValidate = `<button class="btn btn-primary admin-btnValidate" onclick="validateUser('${user['username']}')">Validate</button>`;
                        }

                        var userType = "";
                        if (user['type'] == "admin") {
                            userType = "<span class='label bg-purple'>Admin</span>";
                        } else {
                            userType = "<span class='label bg-light-blue'>Volunteer</span>";
                        }

                        userData += `
                            <tr>
                                <td>${user['username']}</td>
                                <td>${userType}</td>
                                <td>${user['name']}</td>
                                <td>${user['surname']}</td>
                                <td>${user['city']}</td>
                                <td>${user['email']}</td>
                                <td>${validated}</td>
                                <td>${btnValidate}</td>
                            </tr>
                        `;
                    }

                    tableBody.html(userData);
                }
            })
            .fail(function() {
                var tableBody = $(".admin-userTable > tbody");
                var errorMessage = "<tr><td colspan='8'>Get users: request failed. Please refresh the page to try again.</td></tr>";
                tableBody.html(errorMessage);
            });
    }

    getUsers();
</script>
<?php
    include "footer.php";
?>