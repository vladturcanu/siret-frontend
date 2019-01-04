<?php
    session_start();
    $page_title = "Generate Sensor Data";
    $active_map = "active";
    $active_incidents = "";
    $active_account = "";
    include "header.php";
?>

<div class="container">
    <h3 class="page-title">Sensor tokens</h3>
    <table class="table custom-table table-striped">
        <thead>
            <tr>
                <th scope="col">Localitate</th>
                <th scope="col">Parametru</th>
                <th scope="col">Token</th>
                <th scope="col">Generated</th>
            </tr>
        </thead>
        <tr>
            <td>Roman</td>
            <td>PH</td>
            <td>H2PTEioeYu4CWVoJ5gLX</td>
            <td id="data-ph-roman"></td>
        </tr>
        <tr>
            <td>Roman</td>
            <td>Turbiditate</td>
            <td>C6AkvkRLNzpfqHofTdNI</td>
            <td id="data-turbiditate-roman"></td>
        </tr>
        <tr>
            <td>Harlesti</td>
            <td>PH</td>
            <td>Dkdo0yKhWHkLfw2klJPi</td>
            <td id="data-ph-harlesti"></td>
        </tr>
        <tr>
            <td>Harlesti</td>
            <td>Turbiditate</td>
            <td>CoeD4MI99ThZjRxWAzSx</td>
            <td id="data-turbiditate-harlesti"></td>
        </tr>
        <tr>
            <td>Serbesti</td>
            <td>PH</td>
            <td>yoa7Csieon1gSJ3x5kah</td>
            <td id="data-ph-serbesti"></td>
        </tr>
        <tr>
            <td>Serbesti</td>
            <td>Turbiditate</td>
            <td>Oxolfv4eYF63sAmrVf9u</td>
            <td id="data-turbiditate-serbesti"></td>
        </tr>
        <tr>
            <td>Prajesti</td>
            <td>PH</td>
            <td>ghFI3xmzP0gWB8M3AXXI</td>
            <td id="data-ph-prajesti"></td>
        </tr>
        <tr>
            <td>Prajesti</td>
            <td>Turbiditate</td>
            <td>nbWYDkUitwzwEa46mHKT</td>
            <td id="data-turbiditate-prajesti"></td>
        </tr>
        <tr>
            <td>Dospinesti</td>
            <td>PH</td>
            <td>btsH20cXuJTYKJeCKWD8</td>
            <td id="data-ph-dospinesti"></td>
        </tr>
        <tr>
            <td>Dospinesti</td>
            <td>Turbiditate</td>
            <td>gIXNMzSC8kyfVtDCTC04</td>
            <td id="data-turbiditate-dospinesti"></td>
        </tr>
        <tr>
            <td>Radomiresti</td>
            <td>PH</td>
            <td>z8vxJ422iLouPfj6fSwJ</td>
            <td id="data-ph-radomiresti"></td>
        </tr>
        <tr>
            <td>Radomiresti</td>
            <td>Turbiditate</td>
            <td>fOASKPWXSw0NEQbR4Ia2</td>
            <td id="data-turbiditate-radomiresti"></td>
        </tr>
        <tr>
            <td>Bacau</td>
            <td>PH</td>
            <td>T0GgcxngXhknKLdibnjS</td>
            <td id="data-ph-bacau"></td>
        </tr>
        <tr>
            <td>Bacau</td>
            <td>Turbiditate</td>
            <td>8nVqnoRiwtZZWy48VGMp</td>
            <td id="data-turbiditate-bacau"></td>
        </tr>
    </table>
    <a href='#' id="btn-start-generating" class="btn btn-primary" onclick="startGenerating()">Start generating</a>
    <a href='#' id="btn-stop-generating" class="btn btn-primary hidden" onclick="stopGenerating()">Stop generating</a>
</div>

<script>
    var generateInterval;

    var sensors = {
        "roman": {
            "ph": {
                "token": "H2PTEioeYu4CWVoJ5gLX",
                "data": []
            },
            "turbiditate": {
                "token": "C6AkvkRLNzpfqHofTdNI",
                "data": []
            }
        },
        "harlesti": {
            "ph": {
                "token": "Dkdo0yKhWHkLfw2klJPi",
                "data": []
            },
            "turbiditate": {
                "token": "CoeD4MI99ThZjRxWAzSx",
                "data": []
            }
        },
        "serbesti": {
            "ph": {
                "token": "yoa7Csieon1gSJ3x5kah",
                "data": []
            },
            "turbiditate": {
                "token": "Oxolfv4eYF63sAmrVf9u",
                "data": []
            }
        },
        "prajesti": {
            "ph": {
                "token": "ghFI3xmzP0gWB8M3AXXI",
                "data": []
            },
            "turbiditate": {
                "token": "nbWYDkUitwzwEa46mHKT",
                "data": []
            }
        },
        "dospinesti": {
            "ph": {
                "token": "btsH20cXuJTYKJeCKWD8",
                "data": []
            },
            "turbiditate": {
                "token": "gIXNMzSC8kyfVtDCTC04",
                "data": []
            }
        },
        "radomiresti": {
            "ph": {
                "token": "z8vxJ422iLouPfj6fSwJ",
                "data": []
            },
            "turbiditate": {
                "token": "fOASKPWXSw0NEQbR4Ia2",
                "data": []
            }
        },
        "bacau": {
            "ph": {
                "token": "T0GgcxngXhknKLdibnjS",
                "data": []
            },
            "turbiditate": {
                "token": "8nVqnoRiwtZZWy48VGMp",
                "data": []
            }
        },
    };

    function generateValues() {
        for (var location in sensors){
            if (sensors.hasOwnProperty(location)) {
                var ph = Math.floor(Math.random() * 10) + 1;
                var turbiditate = Math.floor(Math.random() * 12) + 1;

                var postPH = {
                    "token": sensors[location]["ph"]["token"],
                    "value": ph
                };
                var postTurbiditate = {
                    "token": sensors[location]["turbiditate"]["token"],
                    "value": turbiditate
                };

                postPH = JSON.stringify(postPH);
                postTurbiditate = JSON.stringify(postTurbiditate);

                $.post("https://serene-cove-78266.herokuapp.com/add_data", postPH)
                    .done(function(data) {
                        if (data["error"]) {
                            alert(data["error"]);
                        }
                    });

                $.post("https://serene-cove-78266.herokuapp.com/add_data", postTurbiditate)
                    .done(function(data) {
                        if (data["error"]) {
                            alert(data["error"]);
                        }
                    });
                
                sensors[location]["ph"]["data"].push(ph);
                sensors[location]["turbiditate"]["data"].push(turbiditate);

                $("#data-ph-"+location).html(sensors[location]["ph"]["data"].join(", "));
                $("#data-turbiditate-"+location).html(sensors[location]["turbiditate"]["data"].join(", "));
            }
        }
    }

    function startGenerating() {
        generateInterval = setInterval(generateValues, 2000);
        
        $("#btn-start-generating").addClass("hidden");
        $("#btn-stop-generating").removeClass("hidden");
    }

    function stopGenerating() {
        clearInterval(generateInterval);
        
        $("#btn-start-generating").removeClass("hidden");
        $("#btn-stop-generating").addClass("hidden");
    }

</script>
<?php
    include "footer.php";
?>