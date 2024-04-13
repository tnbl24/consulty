<?php

namespace MotoPress\Appointment\Metaboxes\Service;

use MotoPress\Appointment\Entities\Service;
use MotoPress\Appointment\Metaboxes\FieldsMetabox;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ServiceSettingsMetabox extends FieldsMetabox {

	const COLOR_PALETTE = array(
		array( '#FBF8CC', '#CDE8CB', '#C4EFF0', '#FDE4CF', '#DDF2EB' ),
		array( '#CDF9D1', '#E4D5FD', '#C9DAF4', '#FAF99C', '#C5FFF3' ),
		array( '#FFBFBC', '#E8F1DE', '#FCF5DC', '#F5E7C5', '#F2E6F0' ),
		array( '#B7C68B', '#E6CBA8', '#D9E3B1', '#FDDB93', '#DEE0E4' ),
		array( '#F6D3ED', '#F4F0CB', '#D8CCAE', '#DDF2FD', '#EEEEE4' ),
	);


	protected function theName(): string {
		return 'service_settings_metabox';
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function theFields() {

		return array(
			'price'               => array(
				'type'  => 'price',
				'label' => __( 'Price', 'motopress-appointment' ),
			),
			'duration'            => array(
				'type'    => 'duration',
				'label'   => __( 'Duration', 'motopress-appointment' ),
				'default' => mpapp()->settings()->getTimeStep(),
				'size'    => 'mild',
			),
			'buffer_time_before'  => array(
				'type'        => 'duration',
				'label'       => __( 'Buffer Time Before', 'motopress-appointment' ),
				'description' => __(
					'Time needed to get prepared for the appointment, when another booking for the same service and employee cannot be made.',
					'motopress-appointment'
				),
				'size'        => 'mild',
			),
			'buffer_time_after'   => array(
				'type'        => 'duration',
				'label'       => __( 'Buffer Time After', 'motopress-appointment' ),
				'description' => __(
					'Time after the appointment (rest, cleanup, etc.), when another booking for the same service and employee cannot be made.',
					'motopress-appointment'
				),
				'size'        => 'mild',
			),
			'time_before_booking' => array(
				'type'        => 'time-period',
				'label'       => __( 'Time Before Booking', 'motopress-appointment' ),
				'description' => __(
					'Minimum period before the appointment when customers can submit a booking request.',
					'motopress-appointment'
				),
			),
			'max_advance_time_before_reservation' => array(
				'type'        => 'time-period',
				'label'       => __( 'Advanced Scheduling Window', 'motopress-appointment' ),
				'description' => __(
					'The maximum amount of time in advance before the appointment that is allowed for making a booking.',
					'motopress-appointment'
				),
			),
			'min_capacity'        => array(
				'type'        => 'number',
				'label'       => __( 'Minimum Capacity', 'motopress-appointment' ),
				'description' => __(
					'Here you can set the minimum number of persons per one booking of this service.',
					'motopress-appointment'
				),
				'min'         => 1,
				'default'     => 1,
				'size'        => 'small',
			),
			'max_capacity'        => array(
				'type'        => 'number',
				'label'       => __( 'Maximum Capacity', 'motopress-appointment' ),
				'description' => __(
					'Here you can set the maximum number of persons per one appointment.',
					'motopress-appointment'
				),
				'min'         => 1,
				'default'     => 1,
				'size'        => 'small',
			),
			'multiply_price'      => array(
				'type'    => 'checkbox',
				'label'   => __( 'Multiply Price', 'motopress-appointment' ),
				'label2'  => __( 'Multiply price by the number of people.', 'motopress-appointment' ),
				'default' => false,
			),
			'color'               => array(
				'type'                      => 'color-picker',
				'label'                     => __( 'Color', 'motopress-appointment' ),
				'default'                   => Service::DEFAULT_COLOR,
				'colorpicker_type'          => 'flat',
				'palette'                   => self::COLOR_PALETTE,
				'toggle_palette_only'       => true,
				'show_palette_only'         => true,
				'show_palette'              => true,
				'hide_after_palette_select' => true,
			),
		);
	}


	public function getLabel(): string {
		return esc_html__( 'Service Settings', 'motopress-appointment' );
	}
}
