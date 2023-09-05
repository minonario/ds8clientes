
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<?php
// Definimos la variable $curauth donde almacenaremos al info del usuario
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
?>

<div class="headerView">
  <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAQACgMBEQACEQEDEQH/xAGiAAABBQEBAQEBAQAAAAAAAAAAAQIDBAUGBwgJCgsQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+gEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoLEQACAQIEBAMEBwUEBAABAncAAQIDEQQFITEGEkFRB2FxEyIygQgUQpGhscEJIzNS8BVictEKFiQ04SXxFxgZGiYnKCkqNTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqCg4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2dri4+Tl5ufo6ery8/T19vf4+fr/2gAMAwEAAhEDEQA/APrj9lfxVp+laZJp0Pgvw68mqWus6vPqEer/ABC0qUz6T4RivIIbrS9A8c6P4a1u2ub9rq+1NfEuha1NqlzfXRv554vIig76rqV8MsTVrVZyvRfI5J0uaUqtKU+WUW1KUYJytJJzcpNXZ52b044PPqeEwkYYelVi/aypUqKrTb9liW/bSpyqwk5tQ56Uqc1SjGmpJc3N89TfFvxVezS3ssOiJLdyPdSpbaTFa26SXDGV1gtbd47e2hVnIiggjjhhQLHEioqqPn6mOx9Oc6ccbiFGE5Qik4K0YtxSsoJKyWySXZH7DhcgyOrhsPUllWE5qlClOX8Z+9OnGT1lWlJ6veUpSe7bep//2Q==" alt="imgBlur">
  <img class="segunda" src="<?php echo get_template_directory_uri()?>/_next/static/images/Header Calificacion-70d5e422435a02ccc17cb1ad59b2a68b.jpg.webp" alt="">
</div>

<div class="container-profile-client">
  
  <div class="ProfileContainer">
              <div class="row">
                <div class="col-img col-sm-3">
                  <?php echo get_avatar( $curauth->user_email , '250 ', '', $curauth->data->display_name,array('class' => 'profileLogo')); ?>
                </div>
                <div class="col-name col-sm-9">
                  <div class="row">
                    <span class="nameClient">
                      <?php echo $curauth->first_name;?>
                    </span>
                  </div>
                  <div class="row">
                    <?php if (!empty(get_user_meta($curauth->ID, 'instagram', true))) : ?>
                    <a href="<?php echo get_user_meta($curauth->ID, 'instagram', true) ?>" target="_blank" class="btn btn-secondary btn-lg active webSocial" role="button" aria-pressed="true">
                      <i class="fab fa-instagram fa-sm"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty(get_user_meta($curauth->ID, 'twitter', true))) : ?>
                    <a href="<?php echo get_user_meta($curauth->ID, 'twitter', true) ?>" target="_blank" class="btn btn-secondary btn-lg active webSocial" role="button" aria-pressed="true">
                      <i class="fab fa-twitter fa-sm"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty(get_user_meta($curauth->ID, 'facebook', true))) : ?>
                    <a href="<?php echo get_user_meta($curauth->ID, 'facebook', true) ?>" target="_blank" class="btn btn-secondary btn-lg active webSocial" role="button" aria-pressed="true">
                      <i class="fab fa-facebook-f fa-sm"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty(get_user_meta($curauth->ID, 'linkedin', true))) : ?>
                    <a href="<?php echo get_user_meta($curauth->ID, 'linkedin', true) ?>" target="_blank" class="btn btn-secondary btn-lg active webSocial" role="button" aria-pressed="true">
                      <i class="fab fa-linkedin fa-sm"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($curauth->user_url)) : ?>
                    <a href="<?php echo esc_attr($curauth->user_url); ?>" target="_blank" class="btn btn-secondary btn-lg active webSocial" role="button" aria-pressed="true">
                      <i class="fa fa-home fa-sm"></i>
                    </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="row">
                <section class="description">
                  <p><?php echo $curauth->description ?></p>
                </section>
              </div>
  </div>

<?php if ( have_posts() ) : ?>
  <div class="emisionesContainer">
    <section id="emisor" className="emisiones">
    <h3> Emisiones </h3>  
    <?php
      while ( have_posts() ) : the_post(); ?>
    
      <div data-test="card" class="card cardEmision">
        <div data-test="card-body" class="card-body">
           <h4 data-test="card-title" class="card-title"><?php the_title(); ?></h4>
           <div class="btns">
             <?php    
             $idDictamen = get_field('id_dictamen');
             $idProvidencia = get_field('id_providencia');
             $idProspecto = get_field('id_prospecto');
             ?>
             <button <?php echo(!empty($idDictamen) ?'data-link="/dictamen'.$idDictamen.'"' : '');?> class="btn-default btn Ripple-parent btn-estadistica <?php echo (empty($idDictamen) ? 'hiddenv' : '')?>" data-placement="top" data-toggle="tooltip" title="Dictamen">

             </button>
             <button <?php echo(!empty($idProvidencia) ?'data-link="/providencia'.$idProvidencia.'"' : '');?> class="btn-default btn Ripple-parent btn-estadistica <?php echo (empty($idProvidencia) ? 'hiddenv' : '')?>" data-placement="top" data-toggle="tooltip" title="Providencia">

             </button>
             <button <?php echo(!empty($idProspecto) ?'data-link="/prospecto'.$idProspecto.'"' : '');?> class="btn-default btn Ripple-parent btn-estadistica <?php echo (empty($idProspecto) ? 'hiddenv' : '')?>" data-placement="top" data-toggle="tooltip" title="Prospecto">

             </button>
           </div>
        </div>
        <div class="card-footer text-muted">
          <?php echo ucfirst(get_the_date('F j, Y'))?>
          <br />
          <?php echo (get_field('monto') ? '<span>Monto: '.get_field('monto').'</span>' : ''); ?>
        </div>
     </div>
    <?php endwhile; 
          wp_reset_postdata();
    ?>

    <?php 
    
      $pages = paginate_links( array(
          'mid_size'  => 6,
          'prev_text' => __( '«', 'textdomain' ),
          'next_text' => "»",
          'type' => 'array'
      ) ); ?>
    
    <div data-test="row" class="row row-pagination">
        <div data-test="col" class="col">
          <ul class="pagination mb-5">
    <?php
    if ($pages !== null) {
    foreach($pages as $page):
      
      if (preg_match('/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/s', $page, $match)){
        ?>
            <li data-test="page-item" class="page-item">
              <a data-test="page-link" aria-label="Previous" class="page-link page-link next-prev active" href="<?php echo $match[1]?>">
                <span aria-hidden="true">«</span>
              </a>
            </li>
      <?php  
      }elseif (preg_match('/<a class="next page-numbers" href="(.*?)">(.*?)<\/a>/s', $page, $match)){
        ?>
            <li data-test="page-item" class="page-item">
              <a data-test="page-link" aria-label="Next" class="page-link page-link next-prev active" href="<?php echo $match[1]?>">
                <span aria-hidden="true">»</span>
              </a>
            </li>
      <?php  
      }elseif(strpos($page, 'current') !== false) {
      ?>
          <li class="active page-item page-link"><?php echo $page ?></li> 
      <?php
      }else { 
        $regex = '/(?<=\sclass=")page-numbers(?="[\s>])/';
        $page = preg_replace($regex, 'page-numbers page-link', $page);
        ?>
          <li class="page-item page-link"><?php echo $page ?></li> 
      <?php
      }
      ?>
      
      <?php
    endforeach;
    }
    ?>
          </ul>
        </div>
    </div>
    
    </section>
    <?php
    endif; 
    ?>

</div>


<?php
get_footer();