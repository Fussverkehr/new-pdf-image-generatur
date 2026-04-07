<?php
namespace PTIG\Admin;

use PTIG\Generator\PdfThumbnailGenerator;
use WP_Query;

class AdminUi {

    public function __construct(private PdfThumbnailGenerator $generator) {}

    public function addGenerateButton(array $fields, $post): array {
        if (!str_starts_with($post->post_mime_type,'application/pdf')) return $fields;
        if (has_post_thumbnail($post->ID)) return $fields;

        $url = wp_nonce_url(
            admin_url('admin.php?action=ptig_generate&attachment_id='.$post->ID),
            'ptig_generate_'.$post->ID
        );

        $fields['ptig_generate'] = [
            'label' => __('Vorschaubild','ptig'),
            'input' => 'html',
            'html'  => '<a class="button" href="'.esc_url($url).'">Vorschaubild generieren</a>'
        ];
        return $fields;
    }

    public function handleGenerate(): void {
        $id = (int)($_GET['attachment_id'] ?? 0);
        check_admin_referer('ptig_generate_'.$id);
        $this->generator->generate($id);
        wp_safe_redirect(admin_url('post.php?post='.$id.'&action=edit'));
        exit;
    }

    public function addListAction(array $actions, $post): array {
        if (!str_starts_with($post->post_mime_type,'application/pdf')) return $actions;
        if (has_post_thumbnail($post->ID)) return $actions;

        $url = wp_nonce_url(
            admin_url('admin.php?action=ptig_generate&attachment_id='.$post->ID),
            'ptig_generate_'.$post->ID
        );

        $actions['ptig_generate'] = '<a href="'.esc_url($url).'">Vorschaubild generieren</a>';
        return $actions;
    }

    public function addBulkButton(): void {
        if (get_current_screen()->id !== 'upload') return;
        $url = wp_nonce_url(admin_url('admin.php?action=ptig_bulk_sync'),'ptig_bulk_sync');
        echo '<a class="button" style="margin-left:8px" href="'.esc_url($url).'">Medien‑Vorschaubilder synchronisieren</a>';
    }

    private function basename(string $file): string {
        return sanitize_file_name(pathinfo($file, PATHINFO_FILENAME));
    }

    private function findNewestMatchingImage(string $base): ?int {
        global $wpdb;
        $like = $wpdb->esc_like($base).'%';
        $sql = $wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
             JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type='attachment'
               AND p.post_mime_type LIKE 'image/%'
               AND pm.meta_key='_wp_attached_file'
               AND pm.meta_value LIKE %s
             ORDER BY p.post_date DESC",
            $like
        );
        $ids = $wpdb->get_col($sql);
        return $ids ? (int)$ids[0] : null;
    }

    public function handleBulkSync(): void {
        check_admin_referer('ptig_bulk_sync');
        if (!current_user_can('upload_files')) wp_die('Keine Berechtigung');

        $q = new WP_Query([
            'post_type'      => 'attachment',
            'post_mime_type' => ['application/pdf','video/%','audio/%'],
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
            'fields'         => 'ids'
        ]);

        foreach ($q->posts as $id) {
            if (has_post_thumbnail($id)) continue;
            $file = get_attached_file($id);
            if (!$file) continue;
            $img = $this->findNewestMatchingImage($this->basename($file));
            if ($img) set_post_thumbnail($id, $img);
        }

        wp_safe_redirect(admin_url('upload.php'));
        exit;
    }
}
