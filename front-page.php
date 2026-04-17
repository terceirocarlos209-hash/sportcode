<?php
get_header();
sse_engine_notice();
?>
<main>
  <div class="container">
    <div class="main-layout">

      <!-- Coluna principal -->
      <div class="main-content">
        <?php get_template_part( 'widgets/live-now' ); ?>
        <?php get_template_part( 'widgets/next-matches' ); ?>
        <?php get_template_part( 'widgets/recent-matches' ); ?>

        <!-- Posts editoriais -->
        <?php if ( have_posts() ) : ?>
        <div class="card" style="margin-top:var(--space-8)">
          <div class="card-header">
            <h2 class="card-title">📰 Últimas Notícias</h2>
          </div>
          <div class="card-body">
            <?php while ( have_posts() ) : the_post(); ?>
              <article style="padding:var(--space-4) 0;border-bottom:1px solid var(--gray-100)">
                <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;margin-bottom:var(--space-2)">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <?php the_excerpt(); ?>
              </article>
            <?php endwhile; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <aside class="sidebar">
        <?php get_template_part( 'widgets/standings' ); ?>
      </aside>

    </div>
  </div>
</main>
<?php
get_footer();
