<?php
    session_start();
    include "variables.php";
    $page_title = "Incidents";
    $active_incidents = "active";
    include "header.php";
?>

<div id="add-incident-modal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="title-add-incident" class="modal-title">Add incident</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="modal-err" class="error-msg hidden"></p>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Conducta sparta">
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control" id="location" placeholder="Roman">
        </div>
        <div class="form-group">
            <label for="details">Details</label>
            <textarea class="form-control" id="details" placeholder="A fost descoperita o conducta sparta care deverseaza deseuri in apropierea localitatii Roman"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="btn-add-incident" type="button" class="btn btn-primary" onclick="addIncident()">Add incident</button>
      </div>
    </div>
  </div>
</div>

<div class="container">
    <div class="incidents">
        <h2 class="page-title">Incidents</h2>
        
        <?php if (isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <table id="incidents-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Details</th>
                    <th>Date</th>
                    <th>Reporter</th>
                    <th>Status</th>

                    <?php if (isset($_SESSION["type"]) && $_SESSION["type"] == "admin"): ?>
                        <?php $colspan = 8 ?>
                        <!-- "Mark as" column -->
                        <th></th>
                        <!-- "Delete" column -->
                        <th></th>
                    <?php else: ?>
                        <?php $colspan = 6 ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $colspan ?>">
                        Please wait... Loading data.
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if (isset($_SESSION["type"]) && ($_SESSION["type"] == "admin" || $_SESSION["type"] == "volunteer")): ?>
            <a href="#" id="link-add-incident" class="btn btn-primary" data-toggle="modal" data-target="#add-incident-modal" onclick="checkIsValid(event);">Add Incident</a>
        <?php endif; ?>
    </div>
</div>

<script>
    function checkIsValid(event) {
        var isValid = "<?= $_SESSION['is_valid'] ?>";

        if (isValid == "false") {
            alert("Your account must be validated by an admin before you can report incidents.");    
            event.stopPropagation();
        }
    }

    function clearIncidentModal() {
        $("#add-incident-modal input").val("");
        $("#add-incident-modal textarea").val("");
    }

    function addIncident() {
        var name = $("#name").val();
        var location = $("#location").val();
        var details = $("#details").val();

        if (!name || !location || !details) {
            $("#modal-err").removeClass("hidden");
            $("#modal-err").html("Please supply incident name, location and details.");
            return;
        }

        $("#modal-err").addClass("hidden");

        var postData = JSON.stringify({
            "name": name,
            "location": location,
            "details": details,
            "token": "<?= $_SESSION['token'] ?>"
        });

        $.post("https://serene-cove-78266.herokuapp.com/add_incident", postData)
            .done(function(data) {
                if (data["error"]) {
                    $("#modal-err").removeClass("hidden");
                    $("#modal-err").html(data["error"]);
                    return;
                } else {
                    $("#modal-err").addClass("hidden");
                    $("#add-incident-modal").modal('hide');
                    clearIncidentModal();
                    populateIncidentsTable();
                }
            })
            .fail(function(data) {
                if (data["error"]) {
                    $("#modal-err").removeClass("hidden");
                    $("#modal-err").html(data["error"]);
                    return;
                } else {
                    $("#modal-err").removeClass("hidden");
                    $("#modal-err").html("Error communicating to the server");
                    return;
                }
            });
    }

    function markIncidentAs(incidentId, status) {
        if (!incidentId) {
            alert("No incident id supplied");
            return;
        } else if (!status) {
            alert("No incident status supplied");
            return;
        }

        var postData = JSON.stringify({
            "id": incidentId,
            "status": status,
            "token": "<?= $_SESSION['token'] ?>"
        });

        $.post("https://serene-cove-78266.herokuapp.com/mark_incident", postData)
            .done(function(data) {
                if (data["error"]) {
                    alert(data["error"]);
                    return;
                } else {
                    populateIncidentsTable();
                    alert(data["message"]);
                }
            })
            .fail(function(data) {
                alert("Connection to the server could not be established. Please try again.")
            });
    }

    function populateIncidentsTable() {
        var accountType = "<?= $_SESSION['type'] ?>";
        var tableBody = $("#incidents-table tbody");
        /* Get incidents from server */
        $.get("https://serene-cove-78266.herokuapp.com/get_incidents")
            .done(function(data) {
                /* Clear table contents */
                tableBody.html("");

                /* Create new rows */
                for (var i = 0; i < data.length; i++) {
                    var date = new Date(data[i]["recorded_date"]["date"]);

                    var markAsDropdown = "";

                    if (accountType == "admin") {
                        markAsDropdown = `
                            <div class="dropdown">
                                <button class="btn btn-primary incidents-btnMarkAs dropdown-toggle" type="button" id="dropdownMenuButton-${data[i]['id']}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Mark as
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-${data[i]['id']}">
                                    <a class="dropdown-item" href="#" onclick="markIncidentAs(${data[i]['id']}, 'reported');">Reported</a>
                                    <a class="dropdown-item" href="#" onclick="markIncidentAs(${data[i]['id']}, 'verified');">Verified</a>
                                    <a class="dropdown-item" href="#" onclick="markIncidentAs(${data[i]['id']}, 'solved');">Solved</a>
                                </div>
                            </div>
                        `;
                    }

                    var statusPretty = "";
                    switch (data[i]["status"]) {
                        case "reported":
                            statusPretty = '<span class="label bg-grey">Reported</span>';
                            break;
                        case "verified":
                            statusPretty = '<span class="label bg-blue">Verified</span>';
                            break;
                        case "solved":
                            statusPretty = '<span class="label bg-green">Solved</span>';
                            break;
                        default:
                            statusPretty = data[i]["status"];
                            break;
                    }

                    var btnDelete = `<button class="btn btn-danger incidents-btnMarkAs" type="button">Delete</button>`;

                    var row = `
                        <tr>
                            <td>${data[i]["name"]}</td>
                            <td>${data[i]["location"]}</td>
                            <td>${data[i]["details"]}</td>
                            <td>${date.toLocaleDateString("en-GB")}</td>
                            <td>${data[i]["reporter"]}</td>
                            <td>${statusPretty}</td>`;

                    if (accountType == "admin") {
                        row += `
                            <td>${markAsDropdown}</td>
                            <td>${btnDelete}</td>`;
                    }

                    row += `
                        </tr>`;

                    htmlRow = $(row);
                    $(tableBody).append(htmlRow);
                }
            });
    }

    populateIncidentsTable();
</script>
<?php
    include "footer.php";
?>