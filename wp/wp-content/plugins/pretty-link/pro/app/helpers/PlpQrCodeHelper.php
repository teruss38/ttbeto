<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpQrCodeHelper {

  /**
   * Check if GD library is available
   *
   * @return bool
   */
  public static function is_gd_available() {
    return extension_loaded('gd') && function_exists('imagecreatefrompng');
  }

  /**
   * Generate QR code with optional logo overlay
   *
   * @param string $url The URL to encode
   * @param int $attachment_id Logo attachment ID (0 for no logo)
   * @param int $logo_size_percent Logo size as percentage of QR width (10-30)
   * @param bool $white_background Add white background behind logo
   * @return resource|false GD image resource or false on failure
   */
  public static function generate_qr_with_logo($url, $attachment_id = 0, $logo_size_percent = 20, $white_background = true) {
    // Determine error correction level based on logo presence
    $error_level = ($attachment_id > 0) ? QR_ECLEVEL_H : QR_ECLEVEL_L;

    // Generate QR code to temporary file
    $temp_qr = tempnam(sys_get_temp_dir(), 'qr_');

    try {
      // Generate base QR code (size 20 = ~1000px, good for overlay work)
      QRcode::png($url, $temp_qr, $error_level, 20, 2);

      // If no logo, just return the base QR
      if($attachment_id <= 0 || !self::is_gd_available()) {
        // Load QR image and return resource
        $qr_image = imagecreatefrompng($temp_qr);
        @unlink($temp_qr);
        return $qr_image ? $qr_image : false;
      }

      // Load QR code image
      $qr_image = imagecreatefrompng($temp_qr);
      if(!$qr_image) {
        @unlink($temp_qr);
        return false;
      }

      // Get logo file path
      $logo_path = get_attached_file($attachment_id);
      if(!$logo_path || !file_exists($logo_path)) {
        // Return QR without logo if logo file missing
        @unlink($temp_qr);
        return $qr_image;
      }

      // Load logo based on file type
      $logo_image = self::load_image($logo_path);
      if(!$logo_image) {
        // Return QR without logo if logo load fails
        @unlink($temp_qr);
        return $qr_image;
      }

      // Apply logo overlay
      $result_image = self::overlay_logo($qr_image, $logo_image, $logo_size_percent, $white_background);

      // Cleanup
      imagedestroy($logo_image);
      @unlink($temp_qr);

      return $result_image;

    } catch(Exception $e) {
      @unlink($temp_qr);
      // Clean up any GD resources that may have been created
      if(isset($qr_image) && is_resource($qr_image)) {
        imagedestroy($qr_image);
      }
      if(isset($logo_image) && is_resource($logo_image)) {
        imagedestroy($logo_image);
      }
      return false;
    }
  }

  /**
   * Load image from file path based on type
   *
   * @param string $path Image file path
   * @return resource|false GD image resource
   */
  private static function load_image($path) {
    $info = @getimagesize($path);
    if(!$info) {
      return false;
    }

    $mime = $info['mime'];

    switch($mime) {
      case 'image/jpeg':
        return @imagecreatefromjpeg($path);
      case 'image/png':
        return @imagecreatefrompng($path);
      case 'image/gif':
        return @imagecreatefromgif($path);
      case 'image/webp':
        if ( function_exists('imagecreatefromwebp') ) {
          return @imagecreatefromwebp($path);
        }
        return false;
      default:
        return false;
    }
  }

  /**
   * Overlay logo on QR code
   *
   * @param resource $qr_image QR code GD image
   * @param resource $logo_image Logo GD image
   * @param int $logo_size_percent Size percentage (10-30)
   * @param bool $white_background Add white background
   * @return resource Modified QR image (same resource as input)
   */
  private static function overlay_logo($qr_image, $logo_image, $logo_size_percent, $white_background) {
    // Get dimensions
    $qr_width = imagesx($qr_image);
    $qr_height = imagesy($qr_image);

    $logo_src_width = imagesx($logo_image);
    $logo_src_height = imagesy($logo_image);

    // Calculate logo target size
    $logo_percent = max(10, min(30, $logo_size_percent)) / 100;
    $logo_width = (int)($qr_width * $logo_percent);

    // Maintain aspect ratio (with division by zero protection)
    if($logo_src_width > 0) {
      $logo_height = (int)($logo_src_height * ($logo_width / $logo_src_width));
    } else {
      // Fallback to square if width is invalid
      $logo_height = $logo_width;
    }

    // Calculate center position
    $logo_x = (int)(($qr_width - $logo_width) / 2);
    $logo_y = (int)(($qr_height - $logo_height) / 2);

    // Add white background if requested
    if($white_background) {
      $white = imagecolorallocate($qr_image, 255, 255, 255);

      // Add 10% padding around logo
      $bg_padding = (int)($logo_width * 0.1);
      $bg_x = $logo_x - $bg_padding;
      $bg_y = $logo_y - $bg_padding;
      $bg_width = $logo_width + ($bg_padding * 2);
      $bg_height = $logo_height + ($bg_padding * 2);

      imagefilledrectangle($qr_image, $bg_x, $bg_y, $bg_x + $bg_width, $bg_y + $bg_height, $white);
    }

    // Resize and copy logo onto QR code
    imagecopyresampled(
      $qr_image,         // Destination
      $logo_image,       // Source
      $logo_x,           // Dest X
      $logo_y,           // Dest Y
      0,                 // Source X
      0,                 // Source Y
      $logo_width,       // Dest width
      $logo_height,      // Dest height
      $logo_src_width,   // Source width
      $logo_src_height   // Source height
    );

    return $qr_image;
  }

  /**
   * Output QR image to browser
   *
   * @param resource $image GD image resource
   * @param string $filename Download filename (optional)
   */
  public static function output_png($image, $filename = null) {
    if($filename) {
      // Sanitize filename to prevent header injection
      $filename = sanitize_file_name($filename);
      $filename = wp_basename($filename);
      $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

      header("HTTP/1.1 200 OK");
      header("Content-Disposition: attachment;filename=\"" . $filename . "\"");
      header("Content-Transfer-Encoding: binary");
    }

    header("Content-Type: image/png");
    imagepng($image);
    imagedestroy($image);
  }
}
