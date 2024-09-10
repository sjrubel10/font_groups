<?php
$groups = json_decode(file_get_contents('font_groups.json'), true);
echo json_encode($groups);
?>
