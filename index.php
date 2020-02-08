<?php
if (@!$_GET['page']) {$_GET['page'] = "f1";}
include("frontend/{$_GET['page']}.php");
?>