<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));

	class SortableFancyboxGalleryBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "Displays images in a fileset (with an optional lightbox), and allows you to change their display order via drag-and-drop.";
		protected $btName = "Sortable Fancybox Gallery";
		protected $btTable = 'btSortableFancyboxGallery';
		protected $btInterfaceWidth = "800";
		protected $btInterfaceHeight = "480";

		public function on_page_view() {
			if ($this->enableLightbox) {
				$html = Loader::helper('html');				
				$bv = new BlockView();
				$bv->setBlockObject($this->getBlockObject());
				$this->addHeaderItem($html->css($bv->getBlockURL() . '/fancybox/jquery.fancybox-1.3.1.css'));
				$this->addHeaderItem($html->javascript($bv->getBlockURL() . '/fancybox/jquery.fancybox-1.3.1.pack.js'));
			}
		}
		
		public function view() {
			Loader::model('sortable_fancybox_gallery', 'sortable_fancybox_gallery');
			$sg = new SortableFancyboxGallery($this->bID);
			$files = $sg->getPermittedImages();

			$ih = Loader::helper('image');

			$images = array();
			$max_row_height = 0;
			foreach ($files as $file) {
				$image = array();
				//$image['src'] = $file->getRelativePath();
				// $size = @getimagesize($file->getPath());
				// $image['width'] = $size[0];
				// $image['height'] = $size[1];
			
				$fv = $file->getRecentVersion();
				$image['title'] = htmlspecialchars($fv->getTitle(), ENT_QUOTES, 'UTF-8');
				$image['description'] = htmlspecialchars($fv->getDescription(), ENT_QUOTES, 'UTF-8');
				
				$full = $ih->getThumbnail($file, $this->fullWidth, $this->fullHeight);
				$image['full_src'] = $full->src;
				$image['full_width'] = $full->width;
				$image['full_height'] = $full->height;

				if ($this->enableLightbox) {
					$thumb = $ih->getThumbnail($file, $this->thumbWidth, $this->thumbHeight);
					$image['thumb_src'] = $thumb->src;
					$image['thumb_width'] = $thumb->width;
					$image['thumb_height'] = $thumb->height;
					$max_row_height = ($thumb->height > $max_row_height) ? $thumb->height : $max_row_height;
				} else {
					$image['thumb_src'] = '';
					$image['thumb_width'] = 0;
					$image['thumb_height'] = 0;
					$max_row_height = ($full->height > $max_row_height) ? $full->height : $max_row_height;
				}
				
				$images[] = $image;
			}

			$this->set('images', $images);
			$this->set('max_row_height', $max_row_height);
			
			//For "initial block add" css workaround:
			$html = Loader::helper('html');				
			$bv = new BlockView();
			$bv->setBlockObject($this->getBlockObject());
			$css_output_object = $html->css($bv->getBlockURL() . '/view.css'); //Pick up theme overrides
			$this->set('inline_view_css_url', $css_output_object->file);
		}
		
		function add() {
			$this->set('fsID', 0);
			$this->set_tools_urls();

			//Defaults for new blocks
			$this->set('enableLightbox', '1');
			$this->set('thumbWidth', '150');
			$this->set('thumbHeight', '150');
			$this->set('fullWidth', '800');
			$this->set('fullHeight', '600');
			$this->set('displayColumns', '3');
			$this->set('lightboxTransitionEffect', 'fade');
			$this->set('lightboxTitlePosition', 'outside');
		}
		
		function edit() {
			$this->set_tools_urls();
		}
		
		private function set_tools_urls() {
			//Can't use the $this->action() method from add or edit forms, so we have to use tools files to respond to ajax calls.
			//We need to get the tools files' urls to the auto.js file, but we can't send values there directly,
			// so instead we send them to the add/edit form, which in turn outputs them as javascript variables
			// (which are then available to code in the auto.js file)
			$th = Loader::helper('concrete/urls'); 
			$this->set('get_filesets_url', $th->getToolsURL('get_fileset_select_options', 'sortable_fancybox_gallery'));
			$this->set('get_thumbnails_url', $th->getToolsURL('get_thumbnail_items', 'sortable_fancybox_gallery'));
		}			
		
		public function save($args) {

			//checkboxes are weird in C5 -- must be handled in this way.
			$args['enableLightbox'] = isset($args['enableLightbox']) ? 1 : 0;

			parent::save($args);
			
			//Save child records (parent::save only saves the primary block record)
			Loader::model('sortable_fancybox_gallery', 'sortable_fancybox_gallery');
			$sg = new SortableFancyboxGallery($this->bID);
			$sortedFileIDs = empty($args['sortedFileIDs']) ? array() : explode(',', $args['sortedFileIDs']); //explode returns array with 1 element (whose value is an empty string) when passed an empty string. We don't want that, so explicitly check for empty string.
			$sg->setPositions($sortedFileIDs);
		}
		
	}

?>
