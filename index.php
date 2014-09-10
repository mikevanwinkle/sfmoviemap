<?php
require_once __DIR__ .'/app.php';
try {
	App\Main::run();
} catch ( Exception $e ) {
	$error = array(
		'status' => '403',
		'message' => $e->getMessage(),
	);
	header("Content-type: application/json");
	echo json_encode( $error );
}
?>
