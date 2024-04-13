<div id="bookit-dashboard-app">
	<h2><?php echo esc_html( $page ); ?></h2>
	<bookit-staff
		:addons="<?php echo esc_attr( json_encode( $addons, JSON_UNESCAPED_UNICODE ) ); ?>"
		:answer="<?php echo esc_attr( json_encode( $answer, JSON_UNESCAPED_UNICODE ) ); ?>"
		:staff="<?php echo esc_attr( json_encode( $staff, JSON_UNESCAPED_UNICODE ) ); ?>"
		:services="<?php echo esc_attr( json_encode( $services, JSON_UNESCAPED_UNICODE ) ); ?>"
		:wp_users="<?php echo esc_attr( json_encode( $wp_users, JSON_UNESCAPED_UNICODE ) ); ?>"
	></bookit-staff>
</div>
