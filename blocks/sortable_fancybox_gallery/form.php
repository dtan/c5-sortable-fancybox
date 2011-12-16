<style>
	#dragndropInstructions { display: inline; padding-left: 5px; font-style: italic; }
	#thumbnailsContainer { width: 785px; height: 330px; margin-top: 5px; border: 1px solid black; background: url(<?php  echo $this->getBlockURL(); ?>/images/crosshatch.png) repeat left top; }
	#thumbnailLoadingIndicator { position: absolute; top: 155px; left: 125px; z-index: 1; }
	#sortableThumbnails { list-style-type: none; margin: 0; padding: 0; }
	#sortableThumbnails li { margin: 5px; padding: 0; float: left; cursor: move; width: 60px; height: 60px; text-align: center; }
	#sortableThumbnails .thumbnail_title { font-size: 8px; }
	#galleryOptions label { display: inline; }
	#galleryOptions input { width: 25px; }
	.galleryOptionField { display: inline; margin-right: 15px; }
</style>

<table id="galleryOptions" border="0">
	<tr>
		<td align="right"><?php  echo $form->label('displayColumns', 'Display Columns:'); ?></td>
		<td align="left"><span class="galleryOptionField"><?php  echo $form->text('displayColumns', $displayColumns); ?></span></td>
		<td align="right"><?php  echo $form->label('fullWidth', 'Max Image Width:'); ?></td>
		<td align="left"><span class="galleryOptionField"><?php  echo $form->text('fullWidth', $fullWidth); ?> px</span></td>
		<td align="right"><?php  echo $form->label('fullHeight', 'Max Image Height:'); ?></td>
		<td align="left"><span class="galleryOptionField"><?php  echo $form->text('fullHeight', $fullHeight); ?> px</span></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><span class="galleryOptionField"><?php  echo $form->checkbox('enableLightbox', 1, $enableLightbox, array('style' => 'margin-right: 0;')); ?><?php  echo $form->label('enableLightbox', 'Enable Lightbox?'); ?></span></td>
		<td align="right"><span class="lightbox-setting"><?php  echo $form->label('thumbWidth', 'Thumbnail Width:'); ?></span></td>
		<td align="left"><span class="lightbox-setting galleryOptionField"><?php  echo $form->text('thumbWidth', $thumbWidth); ?> px</span></td>
		<td align="right"><span class="lightbox-setting"><?php  echo $form->label('thumbHeight', 'Thumbnail Height:'); ?></span></td>
		<td align="left"><span class="lightbox-setting galleryOptionField"><?php  echo $form->text('thumbHeight', $thumbHeight); ?> px</span></td>
	</tr>	
	<tr>
		<td align="left" colspan="2">&nbsp;</td>
		<td align="right"><span class="lightbox-setting"><?php  echo $form->label('lightboxTransitionEffect', 'Transition Effect:'); ?></span></td>
		<td align="left"><span class="lightbox-setting galleryOptionField"><?php  echo $form->select('lightboxTransitionEffect', array('fade' => 'Fade', 'elastic' => 'Elastic', 'none' => 'None'), $lightboxTransitionEffect); ?></span></td>
		<td align="right"><span class="lightbox-setting"><?php  echo $form->label('lightboxTitlePosition', 'Title Position:'); ?></span></td>
		<td align="left"><span class="lightbox-setting galleryOptionField"><?php  echo $form->select('lightboxTitlePosition', array('outside' => 'Outside', 'inside' => 'Inside', 'over' => 'Over'), $lightboxTitlePosition); ?></span></td>
	</tr>	
</table>

<hr />

<strong>File Set:</strong>
<select id="fsID" name="fsID">
	<option value="0">Loading...</option>
</select>
<span id="dragndropInstructions" style="display: none;">Drag and drop images to place them in order.</span>

<div id="thumbnailsContainer">
	<ul id="sortableThumbnails"></ul>
</div>

<img id="thumbnailLoadingIndicator" style="display: none;" src="<?php  echo $this->getBlockURL(); ?>/images/spinner.gif" width="32" height="32" alt="Loading..." />

<input type="hidden" id="sortedFileIDs" name="sortedFileIDs" value="" />

<script>
var SG_GET_FILESETS_URL = '<?php  echo $get_filesets_url; ?>';
var SG_GET_THUMBNAILS_URL = '<?php  echo $get_thumbnails_url; ?>';
var SG_BLOCK_ID = '<?php  echo $this->controller->bID; ?>';

refreshFilesetList(<?php  echo $fsID; ?>);
displayThumbnails(<?php  echo $fsID; ?>);
</script>
