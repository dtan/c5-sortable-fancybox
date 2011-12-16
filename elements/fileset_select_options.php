<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('file_set');
$fileSets = FileSet::getMySets();
?>

<option value="0">--Choose Fileset--</option>
<?php  foreach ($fileSets as $fs): ?>
	<option value="<?php  echo $fs->fsID; ?>"><?php  echo htmlspecialchars($fs->fsName, ENT_QUOTES, 'UTF-8'); ?></option>
<?php  endforeach ?>
