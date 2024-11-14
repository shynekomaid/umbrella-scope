<!DOCTYPE html>

<html lang="en">

<head>
    <title tr="add_lead_title"></title>
    <?php include_once("subtemplates/head.php"); ?>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/add_lead" tr="add_lead"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/get_lead" tr="lead_list"></a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="card text-center shadow-lg">
            <div class="card-header">
                <h1 tr="404_title" class="card-title"></h1>
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
