<?php
namespace PTIG;

use PTIG\Generator\PdfThumbnailGenerator;
use PTIG\Admin\AdminUi;

class Plugin {
    public static function init(): void {

        // Attachments may have featured images
        add_action('init', function () {
            add_post_type_support('attachment', 'thumbnail');
        });

        if (!extension_loaded('imagick')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>Imagick PHP extension ist erforderlich.</p></div>';
            });
            return;
        }

        $generator = new PdfThumbnailGenerator();
        $adminUi   = new AdminUi($generator);

        add_action('add_attachment', [$generator, 'autoGenerate']);

        add_action('admin_action_ptig_generate', [$adminUi, 'handleGenerate']);
        add_action('admin_action_ptig_bulk_sync', [$adminUi, 'handleBulkSync']);

        add_filter('attachment_fields_to_edit', [$adminUi, 'addGenerateButton'], 10, 2);
        add_filter('media_row_actions', [$adminUi, 'addListAction'], 10, 2);
        add_action('restrict_manage_posts', [$adminUi, 'addBulkButton']);

        // Stable grid icon support
        add_filter('wp_prepare_attachment_for_js', [self::class, 'useFeaturedImageAsIcon'], 10, 2);
    }

    public static function useFeaturedImageAsIcon(array $response, \WP_Post $attachment): array {
        $thumb_id = get_post_thumbnail_id($attachment->ID);
        if (!$thumb_id) return $response;

        $img = wp_get_attachment_image_src($thumb_id, 'thumbnail');
        if (!$img || empty($img[0])) return $response;

        $response['icon'] = $img[0];
        return $response;
    }
}
