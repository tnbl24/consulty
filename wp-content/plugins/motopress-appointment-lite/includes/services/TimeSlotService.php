<?php

namespace MotoPress\Appointment\Services;

use MotoPress\Appointment\Entities\Service;
use MotoPress\Appointment\Structures\TimePeriod;
use MotoPress\Appointment\Structures\TimePeriods;
use DateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds time slots for the Appointment Form.
 *
 * @since 1.2.1
 */
class TimeSlotService {

	/**
	 * @since 1.2.1
	 * @var Service
	 */
	public $service = null;

	/**
	 * @since 1.2.2
	 * @var int[]
	 */
	public $employeeIds = array();

	/**
	 * @since 1.2.2
	 * @var int[]
	 */
	public $locationIds = array();

	/**
	 * @since 1.2.2
	 * @var array [Employee ID => [Location ID => ScheduleService]]
	 */
	public $schedulers = array();

	/**
	 * @since 1.2.1
	 *
	 * @param Service $service
	 * @param array $args Optional.
	 *     @param int|int[] $args['employees'] One or more allowed employee ID.
	 *         Leave empty to allow all employees of the service.
	 *     @param int|int[] $args['locations'] One or more allowed location ID.
	 *         Leave empty to allow all locations.
	 */
	public function __construct( $service, $args = array() ) {
		$this->service = $service;

		$employees = ! empty( $args['employees'] ) ? (array) $args['employees'] : array();
		$locations = ! empty( $args['locations'] ) ? (array) $args['locations'] : array();

		// Don't allow employees that does not perform this service
		if ( ! empty( $employees ) ) {
			$employees = array_intersect( $employees, $service->getEmployeeIds() );
		} else {
			$employees = $service->getEmployeeIds();
		}

		// Create ScheduleService for each employee and location
		$this->addEmployees( $employees, $locations );
	}

	/**
	 * @since 1.2.1
	 *
	 * @param int[] $employees
	 * @param int|int[] $locations Optional. One or more allowed location ID.
	 *     0 by default (all allowed).
	 */
	public function addEmployees( $employees, $locations = 0 ) {
		foreach ( $employees as $employeeId ) {
			$this->addEmployee( $employeeId, $locations );
		}
	}

	/**
	 * @since 1.2.1
	 *
	 * @param int $employeeId
	 * @param int|int[] $locationId Optional. One or more allowed location ID.
	 *     0 by default (all allowed).
	 */
	public function addEmployee( $employeeId, $locationId = 0 ) {
		$schedule = mpapp()->repositories()->schedule()->findByEmployee( $employeeId );

		if ( is_null( $schedule ) ) {
			return;
		}

		$schedulers = ScheduleService::splitScheduleByLocation( $schedule, $locationId );

		if ( empty( $schedulers ) ) {
			return;
		}

		// Add new schedulers
		foreach ( $schedulers as $locationId => $scheduler ) {
			$this->schedulers[ $employeeId ][ $locationId ] = $scheduler;
		}

		// Merge new IDs
		$this->employeeIds[] = $employeeId;
		$this->locationIds   = array_merge( $this->locationIds, array_keys( $schedulers ) );

		// Remove duplicate IDs
		$this->employeeIds = mpa_array_unique_reset( $this->employeeIds );
		$this->locationIds = mpa_array_unique_reset( $this->locationIds );
	}

	/**
	 * @since 1.15.2
	 *
	 * @return array|int[]
	 */
	protected function getUnblockedTimeSlotsBookingIds() {
		$searchArgs = array(
			'fields'      => 'ids',
			'post_status' => mpapp()->postTypes()->booking()->statuses()->getUnblockedTimeSlotsStatuses(),
		);

		return mpapp()->repositories()->booking()->findAll( $searchArgs );
	}

	/**
	 * @since 1.2.1
	 *
	 * @param DateTime|string $fromDate
	 * @param DateTime|string $toDate
	 */
	public function blockPeriod( $fromDate, $toDate ) {
		$searchArgs = array(
			// Don't query by 'service_id' or 'location_id' here. Block time
			// slots of this employee in each schedule.
			'employee_id'         => $this->employeeIds,
			'from_date'           => $fromDate,
			'to_date'             => $toDate,
			// Exclude bookings which don't block slots (every unbooked booking)
			'post_parent__not_in' => $this->getUnblockedTimeSlotsBookingIds(),
		);

		$reservations = mpapp()->repositories()->reservation()->findAll( $searchArgs );

		// Block all periods of reservation
		foreach ( $reservations as $reservation ) {
			$employeeId = $reservation->getEmployeeId();

			if ( ! isset( $this->schedulers[ $employeeId ] ) ) {
				continue;
			}

			// Block this period for all locations
			foreach ( $this->schedulers[ $employeeId ] as $scheduler ) {
				$scheduler->addReservation( $reservation->getDate(), $reservation->getBufferTime() );
			}
		}
	}

	/**
	 * @since 1.4.0
	 *
	 * @param array $cartItems Array of [service_id, employee_id, location_id,
	 *      date, time].
	 */
	public function blockCartItems( $cartItems ) {

		foreach ( $cartItems as $cartItem ) {
			$employeeId = $cartItem['employee_id'];

			if ( ! isset( $this->schedulers[ $employeeId ] ) ) {
				continue;
			}

			$service = ( $cartItem['service_id'] === $this->service->getId() ) ? $this->service : mpa_get_service( $cartItem['service_id'] );

			if ( is_null( $service ) ) {
				continue;
			}

			$timePeriod = mpa_add_buffer_time( $cartItem['time'], $service );

			// Block this period for all locations
			foreach ( $this->schedulers[ $employeeId ] as $scheduler ) {
				$scheduler->addReservation( $cartItem['date'], $timePeriod );
			}
		}
	}

	/**
	 * @param DateTime|string $fromDate
	 * @param DateTime|string $toDate
	 * @return array ['Y-m-d' => [Time period string => Array of [Employee ID, Location ID]]]
	 */
	public function getTimeSlotsForEntities( $fromDate, $toDate, $isReservationRulesOn = true ): array {

		$fromDate = mpa_parse_date( $fromDate );
		$toDate   = mpa_parse_date( $toDate );

		if ( false === $fromDate || false === $toDate ) {
			return array();
		}

		// Find available time slots for each employee/location
		$slots = array();

		// For each employee
		foreach ( $this->schedulers as $employeeId => $schedulers ) {

			// For each schedule
			foreach ( $schedulers as $locationId => $scheduler ) {

				$workingDays = $scheduler->getWorkingHoursForPeriod( $fromDate, $toDate );

				// For each day
				foreach ( $workingDays as $dateStr => $workingHours ) {

					$timeSlots = $this->getTimeSlotsForHours( $workingHours, $employeeId, $isReservationRulesOn );

					// For each time slot
					foreach ( array_keys( $timeSlots ) as $timeStr ) {

						$slots[ $dateStr ][ $timeStr ][] = array( $employeeId, $locationId );
					}
				}
			}
		}

		// Sort days in ascending order
		ksort( $slots );

		// Sort time periods in ascending order
		foreach ( $slots as &$daySlots ) {

			ksort( $daySlots );
		}

		unset( $daySlots );

		return $slots;
	}

	/**
	 * @param DateTime|string $fromDate
	 * @param DateTime|string $toDate
	 * @return array ['Y-m-d' => [Time period string => Time period]]
	 *
	 * @todo Return empty days instead of [] when $isSkipDatesWithoutTimeSlots=false and $fromDate=false or $toDate=false.
	 */
	public function getAvailableTimeSlotsForPeriod( $fromDate, $toDate, $isSkipDatesWithoutTimeSlots = true, $isReservationRulesOn = true ) {

		$fromDate = mpa_parse_date( $fromDate );
		$toDate   = mpa_parse_date( $toDate );

		if ( false === $fromDate || false === $toDate ) {

			return array();
		}

		// Find available time slots
		$availableTimeSlots = array();

		for ( $date = clone $fromDate; $date <= $toDate; $date->modify( '+1 day' ) ) {

			$timeSlots = $this->getAvailableTimeSlotsForDate( $date, $isReservationRulesOn );

			if ( ! empty( $timeSlots ) || ! $isSkipDatesWithoutTimeSlots ) {

				$availableTimeSlots[ mpa_format_date( $date, 'internal' ) ] = $timeSlots;
			}
		}

		return $availableTimeSlots;
	}

	/**
	 * @param DateTime $date
	 * @return TimePeriod[] [Time period string => Time period]
	 */
	public function getAvailableTimeSlotsForDate( $date, $isReservationRulesOn = true ): array {

		$timeSlots = array();

		foreach ( $this->schedulers as $employeeId => $schedulers ) {

			foreach ( $schedulers as $scheduler ) {

				$workingHours = $scheduler->getWorkingHoursForDate( $date );
				$workingSlots = $this->getTimeSlotsForHours( $workingHours, $employeeId, $isReservationRulesOn );

				$timeSlots += $workingSlots;
			}
		}

		return $timeSlots;
	}


	public function isAvailableTimeSlot( TimePeriod $timeSlot ): bool {

		$date               = $timeSlot->getStartTime();
		$time               = $timeSlot->toString( 'internal' );
		$availableTimeSlots = $this->getAvailableTimeSlotsForDate( $date, false );

		return array_key_exists( $time, $availableTimeSlots );
	}

	/**
	 * @param TimePeriod|TimePeriod[]|TimePeriods $workingHours
	 * @param int $serviceVariationEmployeeId = 0 for not variation
	 * @return TimePeriod[] [Time period string => Time period]
	 */
	private function getTimeSlotsForHours( $workingHours, int $serviceVariationEmployeeId = 0, $isReservationRulesOn = true ): array {

		$slotsArgs = array(
			// Use duration from available variations
			'duration'      => $this->service->getDuration( $serviceVariationEmployeeId ),
			'buffer_before' => $this->service->getBufferTimeBefore(),
			'buffer_after'  => $this->service->getBufferTimeAfter(),
		);

		if ( $isReservationRulesOn ) {

			$minTime = new DateTime( 'now', wp_timezone() );
			$minTime->modify( "+{$this->service->getTimeBeforeBooking()} minutes" );
			$slotsArgs['min_time'] = $minTime;

			if ( 0 < $this->service->getMaxAdvanceTimeBeforeReservation() ) {

				$maxTime = new DateTime( 'now', wp_timezone() );
				$maxTime->modify( "+{$this->service->getMaxAdvanceTimeBeforeReservation()} minutes" );
				$slotsArgs['max_time'] = $maxTime;
			}
		}

		return $this->findTimeSlots( $workingHours, $slotsArgs );
	}


	/**
	 * @param TimePeriod|TimePeriod[]|TimePeriods $timePeriod
	 * @param array $args
	 *     @param int      $args['duration']      Required. Service duration (minutes).
	 *     @param int      $args['time_step']     Optional. Length of the time slot step (from the settings by default).
	 *     @param int      $args['buffer_before'] Optional. Buffer time before the service (in minutes). 0 by default.
	 *     @param int      $args['buffer_after']  Optional. Buffer time after the service (in minutes). 0 by default.
	 *     @param DateTime $args['min_time']      Optional. No limitations by default.
	 *     @param DateTime $args['max_time']      Optional. No limitations by default.
	 *     @param string   $args['alignment']     Optional. 'hour'|'none'. Alignment from the settings by default.
	 * @return TimePeriod[] [Time period string => Time period]
	 */
	private function findTimeSlots( $timePeriod, $args ): array {

		if ( $timePeriod instanceof TimePeriods ) {
			$timePeriod = $timePeriod->periods;
		}

		if ( is_array( $timePeriod ) ) {

			$slotsPerPeriod = array();

			foreach ( $timePeriod as $singleTimePeriod ) {

				$resultTimeSlots = $this->findTimeSlots( $singleTimePeriod, $args );

				// array_merge() resets numeric indexes
				$slotsPerPeriod = array_replace( $slotsPerPeriod, $resultTimeSlots );
			}

			return $slotsPerPeriod;
		}

		// Check required args
		if ( ! isset( $args['duration'] ) ) {
			return array();
		}

		// Add defaults args
		$args += array(
			'time_step'     => mpapp()->settings()->getTimeStep(),
			'buffer_before' => 0,
			'buffer_after'  => 0,
			'min_time'      => $timePeriod->startTime,
			'max_time'      => $timePeriod->endTime,
			'alignment'     => mpapp()->settings()->getTimeStepAlignment(),
		);

		$startTime = $timePeriod->startTime > $args['min_time'] ? clone $timePeriod->startTime : clone $args['min_time'];
		$endTime   = $timePeriod->endTime < $args['max_time'] ? clone $timePeriod->endTime : clone $args['max_time'];

		// Apply buffer time
		$startTime->modify( "+{$args['buffer_before']} minutes" );
		$endTime->modify( "-{$args['buffer_after']} minutes" );

		// Re-align time
		if ( 'hour' === $args['alignment'] ) {

			$startTimeInMinutes = (int) $startTime->format( 'i' );

			// check if there is a new time step
			if ( 0 < $startTimeInMinutes % $args['time_step'] ) {

				// How much we need to add to get to the next step
				$nextStepOffsetInMinutes = $args['time_step'] - ( $startTimeInMinutes % $args['time_step'] );

				// Re-align time
				switch ( $args['alignment'] ) {
					case 'hour':
						$nextStepInMinutes = $startTimeInMinutes + $nextStepOffsetInMinutes;

						if ( $nextStepInMinutes > 60 ) {
							// Go back to XX:00
							$nextStepOffsetInMinutes -= $nextStepInMinutes % 60;
						}

						break;
				}

				$startTime = clone $startTime;
				$startTime->modify( "+{$nextStepOffsetInMinutes} minutes" );
			}

			$hourMinutes = (int) $startTime->format( 'i' );

			$hourMinutes -= $args['time_step'];
		}

		$startMinutes = ( $startTime->getTimestamp() + $startTime->getOffset() ) / 60;
		$endMinutes   = ( $endTime->getTimestamp() + $endTime->getOffset() ) / 60;

		// Build slots
		$resultTimeSlots = array();

		for ( $timestamp = $startMinutes; ; $timestamp += $args['time_step'] ) {
			// Align timestamp
			switch ( $args['alignment'] ) {
				case 'hour':
					$hourMinutes += $args['time_step'];

					if ( $hourMinutes >= 60 ) {
						// Go back to XX:00
						$timestamp  -= $hourMinutes % 60;
						$hourMinutes = 0;
					}

					break;
			}

			if ( $timestamp + $args['duration'] > $endMinutes ) {
				// No enough time for one more slot
				break;
			}

			$newTimeSlot = new TimePeriod(
				mpa_format_minutes( $timestamp, 'internal' ),
				mpa_format_minutes( $timestamp + $args['duration'], 'internal' )
			);

			$resultTimeSlots[ $newTimeSlot->toString( 'internal' ) ] = $newTimeSlot;
		}

		return $resultTimeSlots;
	}
}
