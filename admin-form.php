<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Endpoints API</title>

    <!-- Add Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Gérer les Endpoints API</h2>

        <!-- Onglets Bootstrap -->
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="true">Liste des Endpoints</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="add-tab" data-toggle="tab" href="#add" role="tab" aria-controls="add" aria-selected="false">Ajouter un Endpoint</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">Modifier un Endpoint</a>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="myTabsContent">
            <!-- Onglet 1 - Liste des Endpoints -->
            <div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
                <h3>Liste des Endpoints</h3>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>URL</th>
                            <th>Méthode</th>
                            <th>Shortcode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'custom_endpoints';
                        $endpoints = $wpdb->get_results("SELECT * FROM $table_name");

                        foreach ($endpoints as $endpoint) {
                            echo "<tr>";
                            echo "<td>{$endpoint->name}</td>";
                            echo "<td>{$endpoint->url}</td>";
                            echo "<td>{$endpoint->method}</td>";
                            echo "<td>[custom_endpoint id={$endpoint->id}]</td>";
                            echo "<td><a href='?page=my-plugin-endpoints&edit={$endpoint->id}'>Modifier</a> | <a href='?page=my-plugin-endpoints&delete={$endpoint->id}'>Supprimer</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Onglet 2 - Ajouter un Endpoint -->
            <div class="tab-pane fade" id="add" role="tabpanel" aria-labelledby="add-tab">
                <h3>Ajouter un Endpoint</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="name">Nom de l'Endpoint</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Enter the endpoint name">
                    </div>
                    <div class="form-group">
                        <label for="url">URL de l'Endpoint</label>
                        <input type="text" class="form-control" id="url" name="url" required placeholder="Enter the endpoint URL">
                    </div>
                    <div class="form-group">
                        <label for="method">Méthode</label>
                        <select class="form-control" id="method" name="method" required>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PATCH">PATCH</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="redirect_url">URL de redirection (si la méthode n'est pas GET)</label>
                        <input type="text" class="form-control" id="redirect_url" name="redirect_url" placeholder="Enter the redirect URL">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Ajouter Endpoint</button>
                </form>
            </div>

            <!-- Onglet 3 - Modifier un Endpoint -->
            <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                <h3>Modifier un Endpoint</h3>
                <?php
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    $endpoint = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
                    if ($endpoint) {
                ?>
                <form method="post">
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    <div class="form-group">
                        <label for="name">Nom de l'Endpoint</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $endpoint->name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="url">URL de l'Endpoint</label>
                        <input type="text" class="form-control" id="url" name="url" value="<?php echo $endpoint->url; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="method">Méthode</label>
                        <select class="form-control" id="method" name="method" required>
                            <option value="GET" <?php selected($endpoint->method, 'GET'); ?>>GET</option>
                            <option value="POST" <?php selected($endpoint->method, 'POST'); ?>>POST</option>
                            <option value="PATCH" <?php selected($endpoint->method, 'PATCH'); ?>>PATCH</option>
                            <option value="PUT" <?php selected($endpoint->method, 'PUT'); ?>>PUT</option>
                            <option value="DELETE" <?php selected($endpoint->method, 'DELETE'); ?>>DELETE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="redirect_url">URL de redirection (si la méthode n'est pas GET)</label>
                        <input type="text" class="form-control" id="redirect_url" name="redirect_url" value="<?php echo $endpoint->redirect_url; ?>">
                    </div>
                    <button type="submit" name="update_endpoint" class="btn btn-primary">Modifier Endpoint</button>
                </form>
                <?php
                    } else {
                        echo 'Endpoint non trouvé.';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS CDN (optional if you need Bootstrap JavaScript functionality) -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const editLinks = document.querySelectorAll(".edit-link");
    editLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const endpointId = this.getAttribute("data-id");
            const editTabLink = document.querySelector('[data-toggle="tab"][href="#edit"]');
            editTabLink.click();
            
            // Chargez les détails de l'endpoint via une requête AJAX
            const detailsContainer = document.getElementById("edit-endpoint-details");
            detailsContainer.innerHTML = "Chargement en cours...";

            // Effectuez une requête AJAX pour obtenir les détails de l'endpoint
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "URL_DE_VOTRE_API_POUR_OBTENIR_LES_DONNÉES_DE_L_ENDPOINT/" + endpointId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    detailsContainer.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    });
});

    </script>
</body>
</html>
