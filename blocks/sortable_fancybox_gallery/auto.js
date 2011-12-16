var last_selected_fsid = 0;

function ccmValidateBlockForm() {
	if ($('select#fsID').val() == '0') {
		ccm_addError('You must choose a file set.');
	}
	
	if (missingRequiredNumber('displayColumns')) {
		ccm_addError('You must enter a number of display columns.');
	}
	
	if (missingRequiredNumber('fullWidth')) {
		ccm_addError('You must enter a numeric maximum image width.');
	}
	
	if (missingRequiredNumber('fullHeight')) {
		ccm_addError('You must enter a numeric maximum image height.');
	}
	
	if (missingRequiredNumber('thumbWidth')) {
		if ($('input#enableLightbox').attr('checked')) {
			ccm_addError('You must enter a numeric thumbnail width.');
		} else {
			$('input#thumbWidth').val('0');
		}
	}

	if (missingRequiredNumber('thumbHeight')) {
		if ($('input#enableLightbox').attr('checked')) {
			ccm_addError('You must enter a numeric thumbnail height.');
		} else {
			$('input#thumbHeight').val('0');
		}
	}
 
	return false;
}

function missingRequiredNumber(id) {
	var value = $('input#'+id).val();
	return ( value == '' || isNaN(value) );
}

$(document).ready(function() {
	
	refreshLightboxSettings($('input#enableLightbox').attr('checked'));
	$('input#enableLightbox').change(function() {
		refreshLightboxSettings($('input#enableLightbox').attr('checked'));
	});
	
	$('select#fsID').change(function() {
		if (this.value == -1) {
			$('#sortableThumbnails').html('');
			openFileManager();
		} else {
			displayThumbnails(this.value);
			last_selected_fsid = this.value;
		}
		toggleDragndropInstructions();
	});
	
	$('a#fileManagerLink').click(function() {
		openFileManager();
		return false;
	});

	$('#sortableThumbnails').sortable({
		containment: $('#thumbnailsContainer'),
		cursor: 'move',
		revert: 100,
		tolerance: 'pointer',
		update: serializeSortOrder
	});
});

function refreshLightboxSettings(enabled) {
	var labelStyle = enabled ? 'color: black' : 'color: gray';
	$('.lightbox-setting label').attr('style', labelStyle);
	if (enabled) {
		$('.lightbox-setting input').removeAttr('disabled');
		$('.lightbox-setting select').removeAttr('disabled');
	} else {
		$('.lightbox-setting input').attr('disabled', 'disabled');
		$('.lightbox-setting select').attr('disabled', 'disabled');
	}
}

function refreshFilesetList(select_value) {
	var select = $('select#fsID');
	var value = (select_value == undefined) ? select.val() : select_value;
	last_selected_fsid = value;
	
	$.ajax({
		url: SG_GET_FILESETS_URL,
		dataType: 'html',
		success: function(response) {
			select.html(response);
			select.val(value);
			select.append('<option value="0">------</option><option value="-1">GO TO FILE MANAGER...</option>');
		}
	});
}

function openFileManager() {
	$.fn.dialog.open({
		width: '90%',
		height: '70%',
		modal: false,
		href: CCM_TOOLS_PATH + "/files/search_dialog",
		title: ccmi18n_filemanager.title,
		onClose: function () {
			refreshFilesetList(last_selected_fsid);
			displayThumbnails(last_selected_fsid);
		}
	});
}

function displayThumbnails(fsID) {
	var bID = SG_BLOCK_ID ? SG_BLOCK_ID : 0;
	var area = $('#sortableThumbnails');
	var indicator = $('#thumbnailLoadingIndicator');
	fsID = (fsID < 0) ? 0 : fsID;
	
	area.html('');
	indicator.show();
	
	$.ajax({
		url: SG_GET_THUMBNAILS_URL,
		data: { fsID: fsID, bID: bID },
		dataType: 'html',
		success: function(response) {
			indicator.hide();
			area.html(response);
			serializeSortOrder();
		}
	});
}

function toggleDragndropInstructions() {
	if ($('select#fsID').val() > 0) {
		$('#dragndropInstructions').show();
	} else {
		$('#dragndropInstructions').hide();
	}
}

function serializeSortOrder() {
	var ids = $('#sortableThumbnails').sortable('toArray').toString();
	$('#sortedFileIDs').val(ids);
}