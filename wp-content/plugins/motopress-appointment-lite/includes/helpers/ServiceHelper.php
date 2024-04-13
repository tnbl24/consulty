<?php

declare( strict_types=1 );

namespace MotoPress\Appointment\Helpers;

use MotoPress\Appointment\Entities\Booking;
use MotoPress\Appointment\Entities\Reservation;
use MotoPress\Appointment\Services\TimeSlotService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ServiceHelper {

	// this is helper with static functions only
	private function __construct() {
	}

	/**
	 * @param array $args get_posts() search parameters
	 *
	 * @return array
	 * @protected
	 */
	public static function excludeBookingReservationsHandler( $args ) {
		global $excludeBooking;

		if ( ! $excludeBooking instanceof Booking ) {
			return $args;
		}

		$excludeBookingId = $excludeBooking->getId();

		if ( isset( $args['post_parent__not_in'][ $excludeBookingId ] ) ) {
			return $args;
		}

		$args['post_parent__not_in'][ $excludeBookingId ] = $excludeBookingId;

		return $args;
	}

	protected static function addExcludeCurrentBookingReservations( Booking $booking ) {
		global $excludeBooking;
		$excludeBooking = $booking;

		$postType = mpapp()->postTypes()->reservation()->getPostType();
		add_filter(
			"{$postType}_repository_get_posts_query_args",
			array(
				__CLASS__,
				'excludeBookingReservationsHandler',
			)
		);
	}

	protected static function removeExcludeCurrentBookingReservations() {
		$postType = mpapp()->postTypes()->reservation()->getPostType();
		remove_filter(
			"{$postType}_repository_get_posts_query_args",
			array(
				__CLASS__,
				'excludeBookingReservationsHandler',
			)
		);
	}

	/**
	 * @return bool
	 */
	protected static function isStillAvailableTimeSlot( Reservation $reservation ) {
		$service = $reservation->getService();

		if ( null === $service ) {
			// we can not check service availability because did not find it
			return false;
		}

		$date = $reservation->getDate();

		$args = array(
			'employees' => array( $reservation->getEmployeeId() ),
			'locations' => array( $reservation->getLocationId() ),
		);

		$timeSlotService = new TimeSlotService( $service, $args );

		// Block reserved periods
		$timeSlotService->blockPeriod( $date, $date );

		return $timeSlotService->isAvailableTimeSlot( $reservation->getServiceTime() );
	}

	/**
	 * Check are time slots available for each booking reservation if we do not take into
	 * account its own reservations (maybe some other booking was made before for
	 * the same time slots). We can check service availability for booking during its modification
	 * or before booking completion.
	 *
	 * @return bool
	 */
	public static function isTimeSlotsAvailableForBooking( Booking $booking ) {

		$reservations = $booking->getReservations();

		self::addExcludeCurrentBookingReservations( $booking );

		foreach ( $reservations as $reservation ) {

			if ( ! self::isStillAvailableTimeSlot( $reservation ) ) {
				self::removeExcludeCurrentBookingReservations();

				return false;
			}

		}

		self::removeExcludeCurrentBookingReservations();

		return true;
	}
}