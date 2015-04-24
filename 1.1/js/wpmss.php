jQuery(document).ready(function($) {
    MouseSmoothScroll();
});

function MouseSmoothScroll(){
	<?php if ( $_GET['enableAll'] == 0 ): ?>
	isMac = /(mac)/.exec( window.navigator.userAgent.toLowerCase() );
	if( isMac != null && isMac.length ) return false;
	<?php endif ?>
	jQuery.srSmoothscroll({
		step: <?php echo $_GET['step'] ?>,
		speed: <?php echo $_GET['speed'] ?>,
		ease: '<?php echo $_GET['ease'] ?>',
		target: jQuery('body'),
		container: jQuery(window)
	});
}