<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

foreach ($images as $file) {
	$fv = $file->getRecentVersion();
	$img = $fv->getThumbnail(1, true);
	$name = htmlspecialchars($fv->getTitle(), ENT_QUOTES, 'UTF-8');
	?>
	<li id="<?php  echo $file->fID; ?>">
	<?php  echo $img; ?>
	<span class="thumbnail_title"><?php  echo $name; ?></span>
	</li>
<?php  } ?>
