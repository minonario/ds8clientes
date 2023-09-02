<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>

<div data-test="row" class="xpostsContent">
<?php if ($users) : ?>
<div data-test="row" class="row postsContent">
  <?php
  foreach ($users as $user) :
    $ID = $user->ID;
    ?>
    <div data-test="col" class="col-6 col-sm-6 col-md-4 col-lg-3">
      <div data-test="card" class="card cardClientes" data-cliente="<?php echo get_author_posts_url($ID); ?>">
        <div class="view" data-test="view">
          <div class="Ripple-parent" style="touch-action: unset;">
            <?php echo get_avatar($ID, '75', 'mystery', $user->display_name, array('class' => 'img-fluid d-block')); ?>
            <div data-test="mask" class="mask rgba-white-slight"></div>
            <div data-test="waves" class="Ripple" style="top: 0px; left: 0px; width: 0px; height: 0px;"></div>
          </div>
        </div>
      </div>
    </div>
    <?php
  endforeach;
  ?>
</div>
<?php endif; ?>

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if ($paged == 1) {
  $offset = 0;
} else {
  $offset = ($paged - 1) * $perpage;
}
$total_user = $total_users; //$users->total_users;
$total_pages = ceil($total_user / $perpage);

$base = trailingslashit( get_bloginfo( 'url' ) ).'emisores/'; //get_permalink(); TO-DO get by page ID
//$base = add_query_arg('orderc', isset($_REQUEST['orderc']) ? $_REQUEST['orderc'] : '',$base);

$paginate_args = [
  'base' => $base . '%_%', //get_pagenum_link(1).'%_%'
  'format' => 'page/%#%/',
  'current' => max( 1, get_query_var('paged') ),
  'total' => $total_pages,
    'prev_text' => '',
    'next_text' => '',
    'type' => 'array',
];

$pages = paginate_links($paginate_args);
?>
<div data-test="row" class="row row-pagination" data-perpage="<?php echo $perpage; ?>">
    <div data-test="col" class="col">
      <ul class="pagination mb-5">
<?php
if ($pages !== null) :
foreach($pages as $page):

  if (preg_match('/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/s', $page, $match)):
    ?>
        <li data-test="page-item" class="page-item">
          <a data-test="page-link" aria-label="Previous" class="page-link page-link next-prev active" href="<?php echo $match[1]?>">
            <span aria-hidden="true">«</span>
          </a>
        </li>
  <?php  
  elseif (preg_match('/<a class="next page-numbers" href="(.*?)">(.*?)<\/a>/s', $page, $match)):
    ?>
        <li data-test="page-item" class="page-item">
          <a data-test="page-link" aria-label="Next" class="page-link page-link next-prev active" href="<?php echo $match[1]?>">
            <span aria-hidden="true">»</span>
          </a>
        </li>
  <?php  
  elseif(strpos($page, 'current') !== false):
  ?>
        <li class="active page-item page-link"><?php echo $page ?></li> 
  <?php
  else: 
    $regex = '/(?<=\sclass=")page-numbers(?="[\s>])/';
    $page = preg_replace($regex, 'page-numbers page-link', $page);
    ?>
        <li class="page-item page-link"><?php echo $page ?></li> 
  <?php
  endif
  ?>

  <?php
endforeach;
endif;
?>
      </ul>
    </div>
  </div>
</div>