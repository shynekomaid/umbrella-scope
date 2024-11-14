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
                <h1 tr="statuses" class="card-title mb-0"></h1>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-5">
                        <label tr="date_from_label" for="date_from" class="form-label"></label>
                        <input type="datetime-local" id="date_from" class="form-control" value="2022-12-01T00:00:00">
                    </div>
                    <div class="col-md-5">
                        <label tr="date_to_label" for="date_to" class="form-label"></label>
                        <input type="datetime-local" id="date_to" class="form-control" value="2022-12-31T23:59:59">
                    </div>
                    <div class="col-md-2">
                        <label tr="page_label" for="page" class="form-label"></label>
                        <input type="number" id="page" name="page" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-12">
                        <button id="get_lead" tr="receive" class="btn btn-primary w-100 mt-3"></button>
                    </div>
                </div>
            </div>

        </div>
        <div class="card text-center shadow-lg mt-4 d-none" id="tableCard">
            <div class="card-header">
                <h1 tr="result" class="card-title mb-0"></h1>
            </div>
            <div class="card-body">
                <div class="table-container overflow-auto" style="max-height: 360px;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col" tr="t_id"></th>
                                <th scope="col" tr="t_email"></th>
                                <th scope="col" tr="t_status"></th>
                                <th scope="col" tr="t_ftd"></th>
                            </tr>
                        </thead>
                        <tbody id="responseTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="toaster"></div>
        <?php include_once("subtemplates/scripts.php"); ?>
</body>

</html>
