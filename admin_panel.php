<?php
    session_start();
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
                            if (user['username'] != "<?= $_SESSION['username'] ?>") 
                                btnValidate = `<button class="btn btn-danger admin-btnValidate" onclick="invalidateUser('${user['username']}')">Invalidate</button>`;
                        } else {
                            validated = "<span class='label bg-red'>No</span>";
                            if (user['username'] != "<?= $_SESSION['username'] ?>") 
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