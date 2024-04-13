<?php

namespace MotoPress\Appointment\Emails\TemplateParts;

use MotoPress\Appointment\Emails\Tags\InterfaceTags;
use MotoPress\Appointment\Helpers\EmailTagsHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1.0
 */
class CustomerReservationDetailsTemplatePart extends AbstractTemplatePart {

	/**
	 * @return string
	 *
	 * @since 1.1.0
	 */
	public function getName() {
		return 'customer_reservation_details';
	}

	/**
	 * @return string
	 *
	 * @since 1.1.0
	 */
	public function getLabel() {
		return esc_html__( 'Customer Reservation Details', 'motopress-appointment' );
	}

	/**
	 * @return string
	 *
	 * @since 1.1.0
	 */
	public function getDescription() {
		return esc_html__( 'Used for {reservations_details} tag in customer emails.', 'motopress-appointment' );
	}

	/**
	 * @return string Rendered default template.
	 *
	 * @since 1.10.2
	 */
	public function renderDefaultTemplate() {
		return mpa_render_template(
			'emails/template-parts/customer-reservation-details.php',
			'emails/template-parts/reservation-details.php'
		);
	}

	/**
	 * @since 1.15.2
	 */
	protected function initTags(): InterfaceTags {
		return EmailTagsHelper::ReservationDetailsTemplatePartTags();
	}
}
