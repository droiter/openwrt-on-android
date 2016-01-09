function Album(path, subAlbums, images, name) {
	this.path = path;
	this.subAlbums = subAlbums;
	this.images = images;
	this.viewedItems = 0;
	this.name = name;
	this.domDef = null;
}

Album.prototype.getThumbnail = function () {
	if (this.images.length) {
		return this.images[0].getThumbnail(true);
	} else {
		return this.subAlbums[0].getThumbnail();
	}
};

Album.prototype.getThumbnailWidth = function () {
	return this.getThumbnail().then(function (img) {
		return img.width;
	});
};

/**
 *@param {array} image
 * @param {number} targetHeight
 * @param {number} calcWidth
 * @param {object} a
 * @returns {a}
 */
Album.prototype.getOneImage = function(image, targetHeight, calcWidth, a) {
	var gm = new GalleryImage(image.src, 1);
	gm.getThumbnail(1).then(function(img) {
		img= img;
		a.append(img);
		img.height = targetHeight / 2;
		img.width = calcWidth;
	});

	return;
};

/**
 *@param {array} images
 * @param {number} targetHeight
 * @param {number} ratio
 * @param {object} a
 * @returns {a}
 */
Album.prototype.getFourImages = function(images, targetHeight, ratio, a) {

	var calcWidth = (targetHeight * ratio) / 2;
	var iImagesCount = images.length;
	if (iImagesCount > 4) {
		iImagesCount = 4;
	}

	a.width(calcWidth * 2);
	a.height(targetHeight - 1);

	for (var i = 0; i < iImagesCount; i++) {
		this.getOneImage(images[i], targetHeight, calcWidth, a);
	}

	return;
};

Album.prototype.getDom = function(targetHeight) {
	var album = this;

	return this.getThumbnail().then(function(img) {
		var a = $('<a/>').addClass('album').attr('href', '#' + encodeURI(album.path));

		a.append($('<label/>').text(album.name));
		var ratio = Math.round(img.ratio * 100) / 100;
		var calcWidth = (targetHeight * ratio) / 2;

		a.width(calcWidth * 2);
		a.height(targetHeight - 1);

		if (album.images.length > 1) {
			album.getFourImages(album.images, targetHeight, ratio, a);
		} else {
			if (album.images.length === 0 && album.subAlbums[0].images.length > 1) {
				album.getFourImages(album.subAlbums[0].images, targetHeight, ratio, a);
			} else {
				a.append(img);
				img.height = targetHeight;
				img.width = targetHeight * ratio;
			}

		}

		return a;
	});
};


/**
 *
 * @param {number} width
 * @returns {$.Deferred<Row>}
 */
Album.prototype.getNextRow = function (width) {
	/**
	 * Add images to the row until it's full
	 *
	 * @param {Album} album
	 * @param {Row} row
	 * @param {GalleryImage[]} images
	 * @returns {$.Deferred<Row>}
	 */
	var addImages = function (album, row, images) {
		// pre-load thumbnails in parallel
		for (var i = 0; i < 3 ;i++){
			if (images[album.viewedItems + i]) {
				images[album.viewedItems + i].getThumbnail();
			}
		}
		var image = images[album.viewedItems];
		return row.addImage(image).then(function (more) {
			album.viewedItems++;
			if (more && album.viewedItems < images.length) {
				return addImages(album, row, images);
			} else {
				return row;
			}
		});
	};
	var items = this.subAlbums.concat(this.images);
	var row = new Row(width);
	return addImages(this, row, items);
};

function Row(targetWidth) {
	this.targetWidth = targetWidth;
	this.items = [];
	this.width = 8; // 4px margin to start with
}

/**
 * @param {GalleryImage} image
 * @return {$.Deferred<bool>} true if more images can be added to the row
 */
Row.prototype.addImage = function (image) {
	var row = this;
	var def = new $.Deferred();
	image.getThumbnailWidth().then(function (width) {
		row.items.push(image);
		row.width += width + 4; // add 4px for the margin
		def.resolve(!row.isFull());
	}, function () {
		console.log('Error getting thumbnail for ' + image);
		def.resolve(true);
	});
	return def;
};

Row.prototype.getDom = function () {
	var scaleRation = (this.width > this.targetWidth) ? this.targetWidth / this.width : 1;
	var targetHeight = 200 * scaleRation;
	var row = $('<div/>').addClass('row');
	/**
	 * @param row
	 * @param {GalleryImage[]} items
	 * @param i
	 * @returns {*}
	 */
	var addImageToDom = function (row, items, i) {
		return items[i].getDom(targetHeight).then(function (itemDom) {
			i++;
			row.append(itemDom);
			if (i < items.length) {
				return addImageToDom(row, items, i);
			} else {
				return row;
			}
		});
	};
	return addImageToDom(row, this.items, 0);
};

/**
 * @returns {boolean}
 */
Row.prototype.isFull = function () {
	return this.width > this.targetWidth;
};

function GalleryImage(src, path) {
	this.src = src;
	this.path = path;
	this.thumbnail = null;
	this.domDef = null;
	this.domHeigth = null;
}

GalleryImage.prototype.getThumbnail = function (square) {
	return Thumbnail.get(this.src, square).queue();
};

GalleryImage.prototype.getThumbnailWidth = function () {
	return this.getThumbnail().then(function (img) {
		return img.width;
	});
};

GalleryImage.prototype.getDom = function (targetHeight) {
	var image = this;
	if (this.domDef === null || this.domHeigth !== targetHeight) {
		this.domHeigth = targetHeight;
		this.domDef = this.getThumbnail().then(function (img) {
			var a = $('<a/>').addClass('image').attr('href', '#' + encodeURI(image.src)).attr('data-path', image.path);
			img.height = targetHeight;
			img.width = targetHeight * img.ratio;
			console.log(targetHeight * img.ratio);
			img.setAttribute('width', 'auto');
			a.append(img);
			return a;
		});
	}
	return this.domDef;
};
