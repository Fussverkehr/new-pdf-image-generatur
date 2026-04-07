<?php
/**
 * Plugin Name: PDF Thumbnail Image Generator
 * Description: Erstellt Vorschaubilder für PDFs, Videos und Audios. Beitragsbilder werden als Icons genutzt. Enthält Bulk-Synchronisation mit Dateinamen-Matching (neueste Datei gewinnt).
 * Version: 2.2.0
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/src/Generator/PdfThumbnailGenerator.php';
require_once __DIR__ . '/src/Admin/AdminUi.php';
require_once __DIR__ . '/src/Plugin.php';

add_action('plugins_loaded', function () {
    \PTIG\Plugin::init();
});
