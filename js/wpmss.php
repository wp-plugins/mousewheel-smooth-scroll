jQuery(document).ready(function($) {
    MouseSmoothScroll();
});

function MouseSmoothScroll(){
	<?php if ( $_GET['enableAll'] == 0 ): ?>	
	if( jQuery.browser.mac ) return false;
	if( ! jQuery.browser.webkit ) return false;
	<?php endif ?>
	jQuery.srSmoothscroll({
		step: <?php echo $_GET['step'] ?>,
		speed: <?php echo $_GET['speed'] ?>,
		ease: '<?php echo $_GET['ease'] ?>',
		target: $('body'),
		container: $(window)
	});
}