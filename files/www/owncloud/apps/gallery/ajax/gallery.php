<?php
/**
 * Copyright (c) 2012 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('gallery');

$gallery = $_GET['gallery'];

$meta = \OC\Files\Filesystem::getFileInfo($gallery);
$data = array();
$data['fileid'] = $meta['fileid'];
$data['permissions'] = $meta['permissions'];

OCP\JSON::setContentTypeHeader();
echo json_encode($data);
