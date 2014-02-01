<!DOCTYPE html>

<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
require_once(dirname(__FILE__) . '/../library/UserAPI.php');
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
		<?php
		try {
			$API = new UserAPI($_REQUEST['request']);
			$response = $API->processAPI();
		} catch (Exception $e) {
			echo json_encode(array('error' => $e->getMessage()));
			exit;
		}

		$decoded_response = json_decode($response, true);

		if (is_array($decoded_response)) {
			echo "<pre>";
			var_export($decoded_response);
			echo "</pre>";
		} else {
			echo $decoded_response;
		}
		?>

    </body>
</html>
