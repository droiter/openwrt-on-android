var Gallery = {};
Gallery.images = [];
Gallery.currentAlbum = '';
Gallery.users = [];
Gallery.albumMap = {};
Gallery.imageMap = {};

Gallery.getAlbum = function (path) {
	if (!Gallery.albumMap[path]) {
		Gallery.albumMap[path] = new Album(path, [], [], OC.basename(path));
		if (path !== '') {
			var parent = OC.dirname(path);
			if (parent === path) {
				parent = '';
			}
			Gallery.getAlbum(parent).subAlbums.push(Gallery.albumMap[path]);
		}
	}
	return Gallery.albumMap[path];
};

// fill the albums from Gallery.images
Gallery.fillAlbums = function () {
	var sortFunction = function (a, b) {
		return a.path.toLowerCase().localeCompare(b.path.toLowerCase());
	};
	var token = $('#gallery').data('token');
	var album, image;
	return $.getJSON(OC.filePath('gallery', 'ajax', 'getimages.php'), {token: token}).then(function (data) {
		Gallery.users = data.users;
		Gallery.displayNames = data.displayNames;
		Gallery.images = data.images;

		for (var i = 0; i < Gallery.images.length; i++) {
			var parts = Gallery.images[i].split('/');
			parts.shift();
			var path = parts.join('/');
			image = new GalleryImage(Gallery.images[i], path);
			var dir = OC.dirname(Gallery.images[i]);
			parts = dir.split('/');
			parts.shift();
			dir = parts.join('/');
			album = Gallery.getAlbum(dir);
			album.images.push(image);
			Gallery.imageMap[image.path] = image;
		}

		for (path in Gallery.albumMap) {
			Gallery.albumMap[path].images.sort(sortFunction);
			Gallery.albumMap[path].subAlbums.sort(sortFunction);
		}
	});
};

Gallery.getAlbumInfo = function (album) {
	if (album === $('#gallery').data('token')) {
		return [];
	}
	if (!Gallery.getAlbumInfo.cache[album]) {
		var def = new $.Deferred();
		Gallery.getAlbumInfo.cache[album] = def;
		$.getJSON(OC.filePath('gallery', 'ajax', 'gallery.php'), {gallery: album}, function (data) {
			def.resolve(data);
		});
	}
	return Gallery.getAlbumInfo.cache[album];
};
Gallery.getAlbumInfo.cache = {};
Gallery.getImage = function (image) {
	return OC.filePath('gallery', 'ajax', 'image.php') + '?file=' + encodeURIComponent(image);
};
Gallery.share = function (event) {
	if (!OC.Share.droppedDown) {
		event.preventDefault();
		event.stopPropagation();

		(function () {
			var target = OC.Share.showLink;
			OC.Share.showLink = function () {
				var r = target.apply(this, arguments);
				$('#linkText').val($('#linkText').val().replace('service=files', 'service=gallery'));
				return r;
			};
		})();

		Gallery.getAlbumInfo(Gallery.currentAlbum).then(function (info) {
			$('a.share').data('item', info.fileid).data('link', true)
				.data('possible-permissions', info.permissions).
				click();
			if (!$('#linkCheckbox').is(':checked')) {
				$('#linkText').hide();
			}
		});
	}
};
Gallery.view = {};
Gallery.view.element = null;
Gallery.view.clear = function () {
	Gallery.view.element.empty();
	Gallery.showLoading();
};
Gallery.view.cache = {};


Gallery.view.viewAlbum = function (albumPath) {
	var i, crumbs, path;
	albumPath = albumPath || '';
	if (!Gallery.albumMap[albumPath]) {
		return;
	}

	Gallery.view.clear();
	if (albumPath !== Gallery.currentAlbum) {
		Gallery.view.loadVisibleRows.loading = false;
	}
	Gallery.currentAlbum = albumPath;

	if (albumPath === '' || $('#gallery').data('token')) {
		$('button.share').hide();
	} else {
		$('button.share').show();
	}

	OC.Breadcrumb.clear();
	var albumName = $('#content').data('albumname');
	if (!albumName) {
		albumName = t('gallery', 'Pictures');
	}
	OC.Breadcrumb.push(albumName, '#').click(function () {
		Gallery.view.viewAlbum('');
	});
	path = '';
	crumbs = albumPath.split('/');
	for (i = 0; i < crumbs.length; i++) {
		if (crumbs[i]) {
			if (path) {
				path += '/' + crumbs[i];
			} else {
				path += crumbs[i];
			}
			Gallery.view.pushBreadCrumb(crumbs[i], path);
		}
	}

	Gallery.getAlbumInfo(Gallery.currentAlbum); //preload album info

	Gallery.albumMap[albumPath].viewedItems = 0;
	setTimeout(function () {
		Gallery.view.loadVisibleRows.activeIndex = 0;
		Gallery.view.loadVisibleRows(Gallery.albumMap[Gallery.currentAlbum], Gallery.currentAlbum);
	}, 0);
};

Gallery.view.loadVisibleRows = function (album, path) {
	if (Gallery.view.loadVisibleRows.loading && Gallery.view.loadVisibleRows.loading.state() !== 'resolved') {
		return Gallery.view.loadVisibleRows.loading;
	}
	// load 2 windows worth of rows
	var scroll = $('#content-wrapper').scrollTop() + $(window).scrollTop();
	var targetHeight = ($(window).height() * 2) + scroll;
	var showRows = function (album) {
		if (!(album.viewedItems < album.subAlbums.length + album.images.length)) {
			Gallery.view.loadVisibleRows.loading = null;
			return;
		}
		return album.getNextRow(Gallery.view.element.width()).then(function (row) {
			return row.getDom().then(function (dom) {
				if (Gallery.currentAlbum !== path) {
					Gallery.view.loadVisibleRows.loading = null;
					return; //throw away the row if the user has navigated away in the meantime
				}
				if (Gallery.view.element.length == 1) {
					Gallery.showNormal();
				}
				Gallery.view.element.append(dom);
				if (album.viewedItems < album.subAlbums.length + album.images.length && Gallery.view.element.height() < targetHeight) {
					return showRows(album);
				} else {
					Gallery.view.loadVisibleRows.loading = null;
				}
			}, function () {
				Gallery.view.loadVisibleRows.loading = null;
			});
		});
	};
	if (Gallery.view.element.height() < targetHeight) {
		Gallery.view.loadVisibleRows.loading = true;
		Gallery.view.loadVisibleRows.loading = showRows(album);
		return Gallery.view.loadVisibleRows.loading;
	}
};
Gallery.view.loadVisibleRows.loading = false;

Gallery.view.pushBreadCrumb = function (text, path) {
	OC.Breadcrumb.push(text, '#' + path).click(function () {
		Gallery.view.viewAlbum(path);
	});
};

Gallery.showEmpty = function () {
	$('#controls').addClass('hidden');
	$('#emptycontent').removeClass('hidden');
	$('#loading').addClass('hidden');
};

Gallery.showLoading = function () {
	$('#emptycontent').addClass('hidden');
	$('#controls').removeClass('hidden');
	$('#content').addClass('icon-loading');
};

Gallery.showNormal = function () {
	$('#emptycontent').addClass('hidden');
	$('#controls').removeClass('hidden');
	$('#content').removeClass('icon-loading');
};

$(document).ready(function () {
	Gallery.showLoading();

	Gallery.view.element = $('#gallery');
	Gallery.fillAlbums().then(function () {
		if(Gallery.images.length === 0) {
			Gallery.showEmpty();
		}
		OC.Breadcrumb.container = $('#breadcrumbs');
		window.onhashchange();
		$('button.share').click(Gallery.share);
	});

	Gallery.view.element.on('click', 'a.image', function (event) {
		event.preventDefault();
		var path = $(this).data('path');
		var album = Gallery.albumMap[Gallery.currentAlbum];
		if (location.hash !== encodeURI(path)) {
			location.hash = encodeURI(path);
			Thumbnail.paused = true;
			var images = album.images.map(function (image) {
				return Gallery.getImage(image.src);
			});
			var clickedImage = Gallery.imageMap[path];
			var i = images.indexOf(Gallery.getImage(clickedImage.src));
			Slideshow.start(images, i);
		}
	});

	$('#openAsFileListButton').click(function (event) {
		window.location.href = window.location.href.replace('service=gallery', 'service=files');
	});

	jQuery.fn.slideShow.onstop = function () {
		$('#content').show();
		Thumbnail.paused = false;
		location.hash = encodeURI(Gallery.currentAlbum);
		Thumbnail.concurrent = 3;
	};

	$(window).scroll(function () {
		Gallery.view.loadVisibleRows(Gallery.albumMap[Gallery.currentAlbum], Gallery.currentAlbum);
	});
	$('#content-wrapper').scroll(function () {
		Gallery.view.loadVisibleRows(Gallery.albumMap[Gallery.currentAlbum], Gallery.currentAlbum);
	});

	$(window).resize(_.throttle(function () {
		Gallery.view.viewAlbum(Gallery.currentAlbum);
	}, 500));
});

window.onhashchange = function () {
	var album = decodeURI(location.hash).substr(1);
	if (!Gallery.imageMap[album]) {
		Slideshow.end();
		album = decodeURIComponent(album);
		if (Gallery.currentAlbum !== album || album == '') {
			Gallery.view.viewAlbum(album);
		}
	} else {
		Gallery.view.viewAlbum(OC.dirname(album));
		$('#gallery').find('a.image[data-path="' + album + '"]').click();
	}
};
