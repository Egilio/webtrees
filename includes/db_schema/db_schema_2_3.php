<?php
// Update the database schema from version 2 to version 3
// - create the wt_gedcom_chunk table to import gedcoms in
// blocks of data smaller than the max_allowed_packet restriction.
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

self::exec(
	"CREATE TABLE IF NOT EXISTS `##gedcom_chunk` (".
	" gedcom_chunk_id INTEGER AUTO_INCREMENT NOT NULL,".
	" gedcom_id       INTEGER                NOT NULL,".
	" chunk_data      MEDIUMBLOB             NOT NULL,".
	" imported        BOOLEAN                NOT NULL DEFAULT FALSE,".
	" PRIMARY KEY     (gedcom_chunk_id),".
	"         KEY ix1 (gedcom_id, imported),".
	" FOREIGN KEY fk1 (gedcom_id) REFERENCES `##gedcom` (gedcom_id)".
	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
);

try {
	self::exec(
		"ALTER TABLE `##gedcom` DROP import_gedcom, DROP import_offset"
	);
} catch (PDOException $ex) {
	// Perhaps we have already deleted these columns?
}

// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);
