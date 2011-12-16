<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class SortableFancyboxGalleryPackage extends Package {

	protected $pkgHandle = 'sortable_fancybox_gallery';
	protected $appVersionRequired = '5.3.0';
	protected $pkgVersion = '1.14'; 
	
	public function getPackageName() {
		return t("Sortable Fancybox Gallery"); 
	}	
	
	public function getPackageDescription() {
		return t("Displays images in a fileset (with an optional lightbox), and allows you to change their display order via drag-and-drop.");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// install block
		BlockType::installBlockTypeFromPackage('sortable_fancybox_gallery', $pkg);
	}
	
	public function uninstall() {
		parent::uninstall();
		$db = Loader::db();
		$db->Execute('DROP TABLE btSortableFancyboxGallery, btSortableFancyboxGalleryPositions');
	}


}