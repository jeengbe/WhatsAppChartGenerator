<?php
include __DIR__ . "/wa.php";
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Chat Generator</title>
    <script src="ext/jquery.js"></script>
    <script src="ext/moment.js"></script>
    <script src="ext/chart.js"></script>
    <script src="js.js"></script>
    <base href="http://localhost/WhatsAppChatGenerator/">
    <link href="ext/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-column-reverse flex-md-row mt-4 mb-4">
            <div class="col-12 col-md-3">
                <ul id="contactList" class="list-group">
                    <li id="contactSearchO" class="list-group-item" style="padding: 0">
                        <input id="contactSearch" type="text" class="form-control" autofocus style="border: none; z-index: 2; position: relative" placeholder="Search.." aria-label="Search" value="<?php echo $_GET["s"] ?? ""; ?>">
                    </li>
                    <?php
                    foreach (WA::getContacts() as $con) {
                        echo "<li class=\"list-group-item\" style=\"border-radius: 0; cursor: pointer; user-select: none\" data-jid=\"{$con->getJid()}\">{$con->getDisplayName()}</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="col-12 col-md-9">
                <div id="stats" style="position: sticky; top: 0"></div>
            </div>
        </div>
    </div>
    <script>Chart.defaults.global.defaultFontFamily = 'sans-serif';<?php if (isset($_GET["s"])) { ?>$(document).ready(_ => sort("<?php echo $_GET["s"] ?? ""; ?>"));<?php } ?></script>
</body>

</html>