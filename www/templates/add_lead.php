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
                        <a class="nav-link active" aria-current="page" href="/add_lead" tr="add_lead"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/get_lead" tr="lead_list"></a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card text-center shadow-lg">
                    <div class="card-header">
                        <h1 class="mb-0" tr="add_lead" class="card-title"></h1>
                    </div>
                    <div class="card-body">
                        <form id="add_lead_form">
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="" required class="form-control" id="firstName">
                                <label for="firstName" tr="add_lead_first_name"></label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="" required class="form-control" id="lastName">
                                <label for="lastName" tr="add_lead_last_name"></label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="tel" placeholder="" required class="form-control" id="phone">
                                <label for="phone" tr="add_lead_phone"></label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="email" placeholder="" required class="form-control" id="email">
                                <label for="email" tr="add_lead_email"></label>
                            </div>
                            <button id="add_lead" tr="submit" class="btn btn-primary w-100"></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toaster"></div>
    <?php include_once("subtemplates/scripts.php"); ?>
</body>

</html>
