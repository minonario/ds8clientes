<?php
/**
 * The template for displaying all pages, single posts and attachments.
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<?php 
  //var_dump($GLOBALS['bodyd']);
  $data = $GLOBALS['bodyd'];
?>

<div class="headerView">
  <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAQACgMBEQACEQEDEQH/xAGiAAABBQEBAQEBAQAAAAAAAAAAAQIDBAUGBwgJCgsQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+gEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoLEQACAQIEBAMEBwUEBAABAncAAQIDEQQFITEGEkFRB2FxEyIygQgUQpGhscEJIzNS8BVictEKFiQ04SXxFxgZGiYnKCkqNTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqCg4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2dri4+Tl5ufo6ery8/T19vf4+fr/2gAMAwEAAhEDEQA/APrv4Zf8E8/gFF+zR4EvpJfGd1N4a+GPwjfTnm1Dw5FPs+K1uPGviKKTULLwraaootNZubpdKntb62vF0+5kstVudWjSAw+3POsyy+p7LCYl0qdZv937OlOFJQqPm9jCcJRg6zSlWk1KcpXcZRu0/n8jwWXYvBZTRxGX4efsMPjYuup4uGJrunVpKjOvWp4qE3KjTqujT9l7KLpQpxqRm6cZL+XL4h/FL+w/H/jnRLLwB8Pns9H8YeJtLtGurDxHcXLW2n61e2kDXFw/ijfPOYoUM0z/ADSyFnbljX17xkpe86VG8veelTd6v/l4fkNfO6tKtWprB4CSp1akFKdPESm1CbinKTxN5SaV5Serd2f/2Q==" alt="imgBlur">
  <img class="segunda" src="<?php echo get_template_directory_uri()?>/_next/static/images/Header Dictamen Providencia y Prospecto-50b74b517fc116a5b908782ff3ef3a7f.jpg.webp" alt="">
</div>


<?php 
 
  $dataa = preg_replace('/(?:\r\n|\r|\n)/i', '<br>', $data['content']['rendered']);
  $pattern = "#<iframe[^>]+>.*?</iframe>#is";
  $dataa = preg_replace($pattern, "", $dataa);
  
  # '/<a[^>]+><span[^>]+>*?Ver Providencia<\/span><\/a>/is';
  # '/<a[^>]+>.*?Providencia<\/a>/im';
  # '/<a[^>]+>*? Ver Providencia<\/a>/im';
  
  $pattern_uno  = '/<a[^>]+><span[^>]+>*?Ver Dictamen<\/span><\/a>/is';
  $pattern_dos  = '/<a[^>]+><span[^>]+>*?Ver Prospecto<\/span><\/a>/is';
  $pattern_tres = '/<a[^>]+><span[^>]+>*?Ver Providencia<\/span><\/a>/is';
  
  $pattern_uno_alt  = '/<a[^>]+>*?Ver Dictamen<\/a>/is';
  $pattern_dos_alt  = '/<a[^>]+>*?Ver Prospecto<\/a>/is';
  $pattern_tres_alt = '/<a[^>]+>*?Ver Providencia<\/a>/is';
  
  $matches = array();
  $resone = preg_match($pattern_uno, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/dictamen/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_uno, $temp, $dataa);
  }
  $resone = preg_match($pattern_dos, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/prospecto/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_dos, $temp, $dataa);
  }
  $resone = preg_match($pattern_tres, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/providencia/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_tres, $temp, $dataa);
  }
  //ALT
  $resone = preg_match($pattern_uno_alt, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/dictamen/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_uno_alt, $temp, $dataa);
  }
  $resone = preg_match($pattern_dos_alt, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/prospecto/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_dos_alt, $temp, $dataa);
  }
  $resone = preg_match($pattern_tres_alt, $dataa,$matches);
  if ($resone){
    $temp = preg_replace('/finanzasdigital.com\//', parse_url( get_site_url(), PHP_URL_HOST )."/providencia/", $matches[0]);
    $temp = preg_replace('/target="_blank"/', '', $temp);
    $dataa = preg_replace($pattern_tres_alt, $temp, $dataa);
  }
  
  $content = substr($data['content']['rendered'], strrpos($data['content']['rendered'], '<iframe'));
  $datec = wp_date('F j, Y', rest_parse_date($data['date']));
    
?>
<section class="dictamen">
  <div data-test="container" class="container">
    <div data-test="row" class="row title">
      <h1><?php echo $data['title']['rendered'] ?></h1> 
    </div>
    
    <div data-test="row" class="row">
    <div data-test="row" class="row">
      <div data-test="col" class="col-sm-6 col-content">
        <span class="date"><?php echo ucfirst($datec); ?></span>
        <div class="contentHtml">
          <?php echo $dataa; ?>
        </div>
      </div> 
        <div data-test="col" class="col-sm-6 col-scribd">
          <div class="w-100 h-100 d-flex align-items-stretch">
            <?php echo $content ?>
        </div>
      </div>
    
                    
    </div>
    
</div><!-- END .si-container -->
</section>

<?php
get_footer();
