<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class SortableFancyboxGallery extends Object {
//We're extending "Object", not "Model", because we don't want an ActiveRecord-style object
	
	private $bID;
	
	function __construct($bID) {
		$this->bID = $bID;
	}
	
	public function getPermittedImages() {
		return $this->getPermittedFiles(true);
	}
	
	public function getPermittedFiles($imagesOnly = false) {
		$fileIDs = $this->getSortedFileIDs($imagesOnly);
		$files = array();
		foreach ($fileIDs as $fID) {
			$f = File::getByID($fID);
			$fp = new Permissions($f);
			if($fp->canRead()) {
				$files[] = $f;
			}
		}
		return $files;
	}
	
	private function getSortedFileIDs($imagesOnly) {
		$db = Loader::db();
		
		//Filesets have their own display order as of Concrete v5.4.1,
		// so when possible we want to sort by that AFTER sorting by our own "position" property
		// (that is, we fall back to fileset display order only for files that don't exist in our own Properties table).
		$fsHasDisplayOrder = version_compare(APP_VERSION, '5.4.1', '>=');
		
		//Tricky query to account for the fact that a user can add or remove images from a fileset AFTER they've sorted them,
		// so there could be fileset records without corresponding position records ("unsorted files" placed at the end of the sort order),
		// and there could be position records without corresponding fileset records ("leftover/orphaned records" that will be ignored).
		// Do it as a union of two sets:
		// set 1 is the valid fileset files that are already assigned to the block in the positions table,
		// set 2 is the valid fileset files in the block's fileset which don't exist in the positions table
		$sql = "SELECT fsf.fID, p.position" . ($fsHasDisplayOrder ? ", fsf.fsDisplayOrder" : "")
			 . " FROM FileSetFiles fsf INNER JOIN (" 
			 . " btSortableFancyboxGallery b INNER JOIN btSortableFancyboxGalleryPositions p ON b.bID=p.bID"
			 . " ) ON fsf.fsID=b.fsID AND fsf.fID=p.fID"
			 . " WHERE b.bID = {$this->bID}" 
			 . ($imagesOnly ? " AND EXISTS (SELECT * FROM FileVersions fv WHERE fv.fID = fsf.fID AND fv.fvIsApproved = 1 AND fv.fvType = ".FileType::T_IMAGE.")" : '')
			 . " UNION SELECT fsf.fID, NULL as position" . ($fsHasDisplayOrder ? ", fsf.fsDisplayOrder" : "")
			 . " FROM FileSetFiles fsf LEFT JOIN btSortableFancyboxGallery b ON fsf.fsID=b.fsID"
			 . " WHERE b.bID = {$this->bID}"
			 . " AND NOT EXISTS(SELECT * FROM btSortableFancyboxGalleryPositions p WHERE p.bID = {$this->bID} AND p.fID = fsf.fID)"
			 . ($imagesOnly ? " AND EXISTS (SELECT * FROM FileVersions fv WHERE fv.fID = fsf.fID AND fv.fvIsApproved = 1 AND fv.fvType = ".FileType::T_IMAGE.")" : '')
			 . " ORDER BY (position IS NULL), position" . ($fsHasDisplayOrder ? ", fsDisplayOrder" : "") . ", fID"; //"(p.position IS NULL)" puts files with no position record at the end of the sort order
		return $db->GetCol($sql);
	}
	
	//$sortedFileIDs must be a one-dimensional array of fID's.
	// Files not included in the array will be removed from the table (for that bID)
	public function setPositions($sortedFileIDs) {
		$db = Loader::db();
		
		//Remove existing records from database
		$sql = "DELETE FROM btSortableFancyboxGalleryPositions WHERE bID = ?";
		$params = array($this->bID);
		$db->Execute($sql, $params);
		
		//Set position numbers for each file
		$values = array();
		$position = 1;
		foreach ($sortedFileIDs as $fID) {
			$values[] = "({$this->bID}, {$fID}, {$position})";
			$position++;
		}

		//Add new records to database
		if ($values) {
			$sql = "INSERT INTO btSortableFancyboxGalleryPositions VALUES " . implode(',', $values);
			$db->Execute($sql);
		}
	}
	
	//Utility function -- code is here for organizational purposes, not because it needs to be in this class
	public static function getUnsortedPermittedFilesetImages($fsID) {
		Loader::model('file_set');
		Loader::model('file_list');

		$fsHasDisplayOrder = version_compare(APP_VERSION, '5.4.1', '>=');

		$fs = FileSet::getByID($fsID);
		$fl = new FileList();		
		$fl->filterBySet($fs);
		$fl->filterByType(FileType::T_IMAGE);
		if ($fsHasDisplayOrder) {
			$fl->sortByFileSetDisplayOrder();
		}
		$all_files = $fl->get();
		$permitted_files = array();
		foreach ($all_files as $f) {
			$fp = new Permissions($f);
			if ($fp->canRead()) {
				$permitted_files[] = $f;
			}
		}
		return $permitted_files;	
	}

}
