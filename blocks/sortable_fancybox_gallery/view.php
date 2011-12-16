<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$html = Loader::helper('html');
$column_width = (100 / $displayColumns) . "%";
$rel = "fancybox{$controller->bID}"; //Avoid conflict with other js lightboxes, and isolate each block's prev/next nav to one gallery only.
$c = Page::getCurrentPage();
?>

<div class="sortable_fancybox_gallery_container">
<?php  foreach ($images as $img): ?>
	<div class="sortable_fancybox_gallery_image" style="height: <?php  echo $max_row_height; ?>px; width: <?php  echo $column_width; ?>;">
		<?php  if ($enableLightbox): ?>
			<a href="<?php  echo $img['full_src']; ?>" rel="<?php  echo $rel; ?>" title="<?php  echo $img['description']; ?>">
				<?php  echo $html->image($img['thumb_src'], $img['thumb_width'], $img['thumb_height'], array('alt' => $img['title'])); ?>
			</a>
		<?php  else: ?>
			<?php  echo $html->image($img['full_src'], $img['full_width'], $img['full_height'], array('alt' => $img['title'])); ?>
		<?php  endif; ?>
	</div>
<?php  endforeach; ?>
</div>

<?php  if (!$c->isEditMode() && $enableLightbox && count($images) > 0): /* fancybox init chokes if no applicable dom elements */ ?>
<script type="text/javascript">
$(document).ready(function(){
	$('a[rel="<?php  echo $rel; ?>"]').fancybox({
		'transitionIn' : '<?php  echo $lightboxTransitionEffect; ?>',
		'transitionOut' : '<?php  echo $lightboxTransitionEffect; ?>',
		'titlePosition' : '<?php  echo $lightboxTitlePosition; ?>'
	});
});
</script>
<?php  endif; ?>

<?php 
// Manually inject view.css upon initial block add.
// This is a workaround to force images to line up
// (initial block add happens via ajax,
// so it's too late for any addHeaderItem()'s).
if ($c->isEditMode()):
	$placeholder_id = "gallery_style_placeholder_{$controller->bID}";
	?>
	
	<div style="display: none;" id="<?php  echo $placeholder_id; ?>"></div>
	
	<script type="text/javascript">
	var gallery_has_style = false;
	$('.sortable_fancybox_gallery_container').each(function(index, element) {

		//Check for indication that view.css was loaded (check ALL instances on the page -- there may be another block on the page that was already loaded with the view.css)
		if ($(this).css('float') == 'left') {
			gallery_has_style = true;
		}
		
		//Load view.css if it doesn't appear to exist
		if (!gallery_has_style) {
			$.ajax({
				url: '<?php  echo $inline_view_css_url; ?>',
				success: function(css) {
					css = '<style>' + css + '</style>';
					$('#<?php  echo $placeholder_id; ?>').html(css);
				}
			});
		}

	});
	</script>

<?php  endif; ?>