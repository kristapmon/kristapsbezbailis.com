
  </div>

<div style="background: #fbfbfb">
<div id="test" class="row newsletter-signup">
		<div class="six columns newsletter-description">
				<h5><strong>&#128235; Joirn my mailing list</strong></h5>
				<p>This is a sample text for why you should be joining my mailing list. This is a sample text for why you should be joining my mailing list. This is a sample text for why you should be joining my mailing list.</p>
		</div>
		<div class="six columns newsletter-signup-box">
			
			<form action="https://kristapsbezbailis.us17.list-manage.com/subscribe/post?u=691797bab4dc502fcb95aa08c&amp;id=cf73f5d643" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
				<div id="mc_embed_signup_scroll">
	
					<div class="mc-field-group">
						<input class="required email newsletter-width" name="EMAIL" required type="email" value="" placeholder="my@email.com" id="mce-EMAIL" style="padding: 25px; font-size: 1.7rem;">
					</div>
				
				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>    
				
				<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_691797bab4dc502fcb95aa08c_cf73f5d643" tabindex="-1" value=""></div>
				
				<div class="clear"><input id="mc-embedded-subscribe" class="button-primary newsletter-width" style="margin-bottom: 0px; font-size: 1.7rem; height: 50px; font-weight: 100;" name="subscribe" type="submit" value="Subscribe"></div>

				</div>
			
			</form>

			<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
			
		</div>

</div>
</div>




<div class="row footer-cover">
  <div class="column" align="center">

    <div class="footer-navigation">

      <?php

        wp_list_pages( '&title_li=' ); // Gets the list of Pages and displays in the navigation

      ?>

    </div>

  </div>

  <div class=footer-copyright>
    © Kristaps Bezbailis <?php echo date("Y");?>
  </div>

</div>

<?php

  wp_footer(); // Prints scripts or data before the closing body tag on the front end

?>
</body>

</html>
