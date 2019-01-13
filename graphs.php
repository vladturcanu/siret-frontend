<?php
    /* We get to this page through a link in the Map, when showing details of a sensor. */
    session_start();
    $page_title = "Siret Map";
    $active_map = "";
    $active_incidents = "";
    $active_account = "";
    include "header.php";
?>

<?php
    if (isset($_GET["parameter"]) &&
        isset($_GET["location"]) &&
        $_GET["parameter"] != "" &&
        $_GET["location"] != "") 
    {
        $parameter = $_GET["parameter"];
        $location = $_GET["location"];

        if ($parameter == "ph") {
            $display_parameter = "<span class='label bg-blue'>pH</span>";
        } else {
            $display_parameter = "<span class='label bg-green'>turbidity</span>";
        }
    } else {
        $error = "Please specify in your GET request the location of the sensor and the measured parameter.";
    }
?>
       
<?php if (isset($error)): ?>

    <p class="error-msg"><?= $error ?></p>

<?php else: ?>
    <div class="container">
        <h3 class="page-title">Measured <?= $display_parameter; ?> values near <span class="blue"><?= ucfirst($location); ?></span></h3>
    </div>
    <div id="please-wait">Please wait. Fetching data...</div>
    <div id="graph">
    </div>

    <script>
        function sortAxesByDate(axisPoint1, axisPoint2) {
            /* This is a comparison function that will result in dates being sorted in ASCENDING order. */
            var date1 = axisPoint1["x"];
            var date2 = axisPoint2["x"];

            var date1_value = Date.parse(date1);
            var date2_value = Date.parse(date2);

            if (date1_value > date2_value) return 1;
            if (date1_value < date2_value) return -1;
            return 0;
        };

        function plotData() {
            var parameter = "<?= $parameter ?>";
            var location = "<?= $location ?>";

            var postData = JSON.stringify({
                "parameter": parameter,
                "location": location
            });

            /* Fetch data */
            $.post("https://serene-cove-78266.herokuapp.com/get_sensor_data", postData)
                .done(function(data) {
                    if (data["error"]) {
                        $("#please-wait").html("<p class='error-msg'>"+data["error"]+"</p>");
                    } else {
                        /* Arrange data for graph */
                        var graphData = {
                            x: [],
                            y: [],
                            type: 'scatter'
                        };

                        var layout = {
                            xaxis: {
                                title: "Measurement Date"
                            },
                            yaxis: {
                                title: "Value"
                            }
                        }

                        /* Put data in axes array, then sort it by date ascending */
                        var axes = [];
                        for (var i = 0; i < data.length; i++) {
                            var dataPoint = data[i];

                            axes.push({
                                "x": dataPoint['timestamp']['date'],
                                "y": dataPoint['value']
                            });
                        }

                        axes.sort(sortAxesByDate);

                        for (var i = 0; i < axes.length; i++) {
                            graphData["x"].push(axes[i]["x"]);
                            graphData["y"].push(axes[i]["y"]);
                        }

                        var plotlyData = [graphData];
                        Plotly.newPlot('graph', [graphData], layout);

                        $("#please-wait").html("");
                    }
                })
                .fail(function() {
                    $("#please-wait").html("<p class='error-msg'>Could not establish connection with the server. Please refresh the page.</p>");
                });
        }

        plotData();
    </script>

<?php endif; ?>

<?php
    include "footer.php";
?>