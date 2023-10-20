<?php
/*
Plugin Name: Mon Plugin API
Description: Un plugin pour gérer les endpoints API depuis le panneau d'administration.
Version: 1.0
*/

// Activation du plugin
function activate_my_plugin() {
    // Créez la table personnalisée lors de l'activation du plugin
    create_custom_table();
}
register_activation_hook(__FILE__, 'activate_my_plugin');

// Désactivation du plugin
function deactivate_my_plugin() {
    // Vous pouvez effectuer des actions de nettoyage lors de la désactivation ici
}
register_deactivation_hook(__FILE__, 'deactivate_my_plugin');

// Création de la table personnalisée pour stocker les endpoints
function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_endpoints';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        url varchar(255) NOT NULL,
        method varchar(10) NOT NULL,
        redirect_url varchar(255) DEFAULT NULL,
        shortcode varchar(50) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Ajoutez une page d'administration pour gérer les endpoints
function my_plugin_menu() {
    add_menu_page('Gérer les Endpoints API', 'Endpoints API', 'manage_options', 'my-plugin-endpoints', 'my_plugin_page');
}
add_action('admin_menu', 'my_plugin_menu');

// Générateur de shortcode
function generate_shortcode($endpoint_id) {
    return "[custom_endpoint id={$endpoint_id}]";
}

// Page d'administration pour gérer les endpoints
function my_plugin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
    }

    // Traitement de la suppression d'un endpoint
    if (isset($_GET['delete'])) {
        $delete_id = intval($_GET['delete']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_endpoints';
        $wpdb->delete($table_name, array('id' => $delete_id));
    }

    // Traitement de la modification d'un endpoint
    if (isset($_POST['update_endpoint'])) {
        $edit_id = intval($_POST['edit_id']);
        $name = sanitize_text_field($_POST['name']);
        $url = esc_url_raw($_POST['url']);
        $method = sanitize_text_field($_POST['method']);
        $redirect_url = esc_url_raw($_POST['redirect_url']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_endpoints';
        $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'url' => $url,
                'method' => $method,
                'redirect_url' => $redirect_url,
            ),
            array('id' => $edit_id)
        );
    }

    // Traitement de l'ajout d'un endpoint
    if (isset($_POST['submit'])) {
        $name = sanitize_text_field($_POST['name']);
        $url = esc_url_raw($_POST['url']);
        $method = sanitize_text_field($_POST['method']);
        $redirect_url = esc_url_raw($_POST['redirect_url']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_endpoints';
        $wpdb->insert($table_name, array(
            'name' => $name,
            'url' => $url,
            'method' => $method,
            'redirect_url' => $redirect_url,
            'shortcode' => generate_shortcode($wpdb->insert_id),
        ));
    }

    // Affichage de la page d'administration
    include plugin_dir_path(__FILE__) . 'admin-form.php';
}

add_action('admin_menu', 'my_plugin_menu');

error_log('Activation du plugin');
create_custom_table();
