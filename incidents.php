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
                    <!-- TODO: Mark as column -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6">
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

    function populateIncidentsTable() {
        var tableBody = $("#incidents-table tbody");
        /* Get incidents from server */
        $.get("https://serene-cove-78266.herokuapp.com/get_incidents")
            .done(function(data) {
                /* Clear table contents */
                tableBody.html("");

                /* Create new rows */
                for (var i = 0; i < data.length; i++) {
                    var date = new Date(data[i]["recorded_date"]["date"]);
                    var row = $(`
                        <tr>
                            <td>${data[i]["name"]}</td>
                            <td>${data[i]["location"]}</td>
                            <td>${data[i]["details"]}</td>
                            <td>${date.toLocaleDateString("en-GB")}</td>
                            <td>${data[i]["reporter"]}</td>
                            <td>${data[i]["status"]}</td>
                        </tr>`
                    );
                    $(tableBody).append(row);
                }
            });
    }

    populateIncidentsTable();
</script>
<?php
    include "footer.php";
?>