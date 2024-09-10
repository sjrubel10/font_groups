<?php
$fonts = array_diff(scandir('uploads'), array('..', '.'));
echo json_encode($fonts);
?>
