
</main><!-- End main content -->

  </div>

<div class="footer-separator"></div>
<?php $newsletter_opts = theme_newsletter_get_options(); ?>
<?php if ($newsletter_opts['enabled']) : ?>
<aside class="newsletter-section" aria-label="Newsletter signup">
  <div class="newsletter-card">
    <div class="newsletter-card__info">
      <span class="newsletter-card__icon" aria-hidden="true">
        <i class="ri-book-open-line"></i>
      </span>
      <div class="newsletter-card__text">
        <strong><?php echo esc_html($newsletter_opts['heading']); ?></strong>
        <span><?php echo esc_html($newsletter_opts['description']); ?></span>
      </div>
    </div>
    <form class="newsletter-card__form" action="<?php echo esc_url($newsletter_opts['form_action_url']); ?>" method="post" target="_blank" novalidate aria-label="Newsletter subscription form">
      <label for="newsletter-email" class="screen-reader-text">Email address</label>
      <input type="email" name="<?php echo esc_attr($newsletter_opts['email_field_name']); ?>" id="newsletter-email" placeholder="name@example.com" required aria-required="true">
      <?php if (!empty($newsletter_opts['honeypot_field_name'])) : ?>
        <div style="position:absolute;left:-5000px;" aria-hidden="true"><input type="text" name="<?php echo esc_attr($newsletter_opts['honeypot_field_name']); ?>" tabindex="-1" value=""></div>
      <?php endif; ?>
      <button type="submit"><?php echo esc_html($newsletter_opts['button_text']); ?></button>
    </form>
  </div>
</aside>
<?php endif; ?>




<footer class="row footer-cover" role="contentinfo">
  <div class="column" align="center">

    <nav class="footer-navigation" role="navigation" aria-label="Footer navigation">

      <?php
        if ( has_nav_menu( 'header-menu' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'header-menu',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'fallback_cb'    => false
            ) );
        } else {
            wp_list_pages( '&title_li=' );
        }
      ?>

    </nav>

  </div>

  <div class="footer-copyright">
    <small>&copy; Kristaps Bezbailis <?php echo date("Y");?></small>
  </div>

</footer>

<?php

  wp_footer(); // Prints scripts or data before the closing body tag on the front end

?>
</body>

</html>
