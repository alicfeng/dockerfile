<?php
$dir = 'files';
$response = scan($dir);
function scan($dir){
	$files = [];
	if(file_exists($dir)){
		foreach(scandir($dir) as $f) {
			if(!$f || $f[0] == '.') {
				continue;
			}
			if(is_dir($dir . '/' . $f)) {
				$files[] = [
					'name' => $f,
					'type' => 'folder',
					'path' => $dir . '/' . $f,
					'items' => scan($dir . '/' . $f)
				];
			}
			else {
				$files[] = [
					'name' => $f,
					'type' => 'file',
					'path' => $dir . '/' . $f,
					'size' => filesize($dir . '/' . $f)
				];
			}
		}

	}
	return $files;
}
header('Content-type: application/json');
echo json_encode([
	'name' => 'files',
	'type' => 'folder',
	'path' => $dir,
	'items' => $response
]);
