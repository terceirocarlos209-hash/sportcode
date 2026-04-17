<?php
get_header();
?>
<main class="container" style="padding: 48px 0;">
  <h2 style="font-family:var(--font-display);font-size:1.5rem;color:var(--gray-800);">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_title( '<h3>', '</h3>' );
            the_excerpt();
        endwhile;
    else :
        echo '<p>Nenhum conteúdo encontrado.</p>';
    endif;
    ?>
  </h2>
</main>
<?php
get_footer();
