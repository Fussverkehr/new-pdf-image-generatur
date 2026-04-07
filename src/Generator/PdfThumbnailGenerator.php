<?php
namespace PTIG\Generator;

use Imagick;

class PdfThumbnailGenerator {

    public function autoGenerate(int $attachmentId): void {
        $mime = get_post_mime_type($attachmentId);
        if (!str_starts_with($mime, 'application/pdf')) return;
        if (has_post_thumbnail($attachmentId)) return;
        $this->generate($attachmentId);
    }

    public function generate(int $attachmentId): ?int {
        $pdf = get_attached_file($attachmentId);
        if (!$pdf || !file_exists($pdf)) return null;

        $im = new Imagick();
        $im->setResolution(150,150);
        $im->readImage($pdf.'[0]');

        if ($im->getImageColorspace() === Imagick::COLORSPACE_CMYK) {
            $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        }
        if ($im->getImageAlphaChannel()) {
            $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        }

        $im->setImageBackgroundColor('white');
        $im = $im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

        $im->setImageFormat('jpeg');
        $im->setImageCompressionQuality(85);
        $im->thumbnailImage(800,800,true);

        $path = pathinfo($pdf);
        $filename = wp_unique_filename($path['dirname'], $path['filename'].'-preview.jpg');
        $full = $path['dirname'].'/'.$filename;
        $im->writeImage($full);
        $im->clear();

        $imgId = wp_insert_attachment([
            'post_mime_type' => 'image/jpeg',
            'post_title'     => get_the_title($attachmentId),
            'post_status'    => 'inherit'
        ], $full, $attachmentId);

        require_once ABSPATH.'wp-admin/includes/image.php';
        wp_update_attachment_metadata($imgId, wp_generate_attachment_metadata($imgId,$full));
        set_post_thumbnail($attachmentId, $imgId);
        return $imgId;
    }
}
