
</main><!-- End main content -->

  </div>

<aside class="newsletter-section" style="background: #fbfbfb" aria-label="Newsletter signup">
<div id="test" class="row newsletter-signup">
		<div class="six columns newsletter-description">
				<h5><strong>&#128235; Join my mailing list</strong></h5>
				<p>This is a sample text for why you should be joining my mailing list. This is a sample text for why you should be joining my mailing list. This is a sample text for why you should be joining my mailing list.</p>
		</div>
		<div class="six columns newsletter-signup-box">
			
			<form action="https://kristapsbezbailis.us17.list-manage.com/subscribe/post?u=691797bab4dc502fcb95aa08c&amp;id=cf73f5d643" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate aria-label="Newsletter subscription form">
				<div id="mc_embed_signup_scroll">
	
					<div class="mc-field-group">
						<label for="mce-EMAIL" class="screen-reader-text">Email address</label>
						<input class="required email newsletter-width" name="EMAIL" required type="email" value="" placeholder="my@email.com" id="mce-EMAIL" style="padding: 25px; font-size: 1.7rem;" aria-required="true">
					</div>
				
				<div id="mce-responses" class="clear" aria-live="polite">
					<div class="response" id="mce-error-response" style="display:none" role="alert"></div>
					<div class="response" id="mce-success-response" style="display:none" role="status"></div>
				</div>    
				
				<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_691797bab4dc502fcb95aa08c_cf73f5d643" tabindex="-1" value=""></div>
				
				<div class="clear"><input id="mc-embedded-subscribe" class="button-primary newsletter-width" style="margin-bottom: 0px; font-size: 1.7rem; height: 50px; font-weight: 100;" name="subscribe" type="submit" value="Subscribe"></div>

				</div>
			
			</form>

			<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
			
		</div>

</div>
</aside>




<footer class="row footer-cover" role="contentinfo">
  <div class="column" align="center">

    <nav class="footer-navigation" role="navigation" aria-label="Footer navigation">

      <?php

        wp_list_pages( '&title_li=' ); // Gets the list of Pages and displays in the navigation

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
