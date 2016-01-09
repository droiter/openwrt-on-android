/* global OC */
$(document).ready(function () {
	var button;

	if ($('#body-login').length > 0) {
		return true; //deactivate on login page
	}

	function onFileListUpdated() {
		var hasImages = !!$("#fileList").find("tr[data-mime^='image']:first").length;

		button.toggleClass('hidden', !hasImages);
	}
	if ($('#filesApp').val() && $('#isPublic').val()) {

		$('#fileList').on('updated', onFileListUpdated);

		// toggle for opening shared file list as picture view
		// TODO find a way to not need to use inline CSS
		button = $('<div class="button hidden"'
			+'style="position: absolute; right: 0; top: 0; font-weight: normal;">'
				+'<img class="svg" src="' + OC.filePath('core', 'img/actions', 'toggle-pictures.svg') + '"'
				+'alt="' + t('gallery', 'Picture view') + '"'
				+'style="vertical-align: text-top; '
				+'-ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=50); '
				+'filter: alpha(opacity=50); opacity: .5;" />'
			+'</div>');

		button.click( function (event) {
			window.location.href = window.location.href.replace('service=files', 'service=gallery').replace('dir=', 'path=');
		});

		$('#controls').append(button);
	}
});
