<!DOCTYPE html>

<html lang="en">

<head>
    <title tr="404_title"></title>
    <?php include_once("subtemplates/head.php"); ?>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card text-center shadow-lg">
            <div class="card-header">
                <h1 tr="404_title" class="card-title mb-0"></h1>
            </div>
            <div class="card-body">
                <p tr="404_description" class="card-text"></p>
                <a href="/" class="btn btn-primary w-100" tr="404_back">&nbsp;</a>
            </div>

        </div>
    </div>
    <div id="toaster"></div>
    <?php include_once("subtemplates/scripts.php"); ?>
</body>

</html>
