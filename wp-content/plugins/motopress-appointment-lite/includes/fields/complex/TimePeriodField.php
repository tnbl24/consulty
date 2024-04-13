<?php

namespace MotoPress\Appointment\Fields\Complex;

use MotoPress\Appointment\Fields\AbstractField;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Time period saved to the database in minutes.
 */
class TimePeriodField extends AbstractField {

	const TYPE = 'time-period';

	/**
	 * @var int
	 */
	protected $minYears = 0;

	/**
	 * @var int
	 */
	protected $maxYears = 5;

	/**
	 * @var int
	 */
	protected $minMonths = 0;

	/**
	 * @var int
	 */
	protected $maxMonths = 12;

	/**
	 * @var int
	 */
	protected $minDays = 0;

	/**
	 * @var int
	 */
	protected $maxDays = 31;

	/**
	 * @var int
	 */
	protected $minHours = 0;

	/**
	 * @var int
	 */
	protected $maxHours = 23;

	/**
	 * @var int
	 */
	protected $minMinutes = 0;

	/**
	 * @var int
	 */
	protected $maxMinutes = 59;

	/**
	 * @var int
	 */
	protected $minutesStep = 15;

	/**
	 * @param string $inputName Prefixed name.
	 * @param array $args
	 * @param mixed $value Optional. Null by default.
	 */
	public function __construct( $inputName, $args, $value = null ) {

		if ( empty( $value ) ) {

			$value = array(
				'years'   => $this->minYears,
				'months'  => $this->minMonths,
				'days'    => $this->minDays,
				'hours'   => $this->minHours,
				'minutes' => $this->minMinutes,
			);
		}

		parent::__construct( $inputName, $args, $value );
	}

	/**
	 * @return array
	 */
	protected function mapFields() {

		return array_merge(
			parent::mapFields(),
			array(
				// Parameter name => Field name
				'minYears'   => 'minYears',
				'maxYears'   => 'maxYears',
				'minMonths'  => 'minMonths',
				'maxMonths'  => 'maxMonths',
				'minDays'    => 'minDays',
				'maxDays'    => 'maxDays',
				'minHours'   => 'minHours',
				'maxHours'   => 'maxHours',
				'minMinutes' => 'minMinutes',
				'maxMinutes' => 'maxMinutes',
			)
		);
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function validateValue( $value ) {

		$validatedValues = array(
			'years'   => isset( $value['years'] ) ? absint( $value['years'] ) : $this->minYears,
			'months'  => isset( $value['months'] ) ? absint( $value['months'] ) : $this->minMonths,
			'days'    => isset( $value['days'] ) ? absint( $value['days'] ) : $this->minDays,
			'hours'   => isset( $value['hours'] ) ? absint( $value['hours'] ) : $this->minHours,
			'minutes' => isset( $value['minutes'] ) ? absint( $value['minutes'] ) : $this->minMinutes,
		);

		$validatedValues['years']   = max( $this->minYears, min( $validatedValues['years'], $this->maxYears ) );
		$validatedValues['months']  = max( $this->minMonths, min( $validatedValues['months'], $this->maxMonths ) );
		$validatedValues['days']    = max( $this->minDays, min( $validatedValues['days'], $this->maxDays ) );
		$validatedValues['hours']   = max( $this->minHours, min( $validatedValues['hours'], $this->maxHours ) );
		$validatedValues['minutes'] = max( $this->minMinutes, min( $validatedValues['minutes'], $this->maxMinutes ) );

		return $validatedValues;
	}

	/**
	 * @param string $context Optional. 'internal' by default. Variants:
	 *     'internal' - for internal use (in the functions of the plugin);
	 *     'save'     - prepare the value for the database.
	 * @return mixed
	 */
	public function getValue( $context = 'internal' ) {

		$value = $this->value;

		// we save time period in minutes
		if ( 'save' === $context ) {

			$value = 0;

			if ( isset( $this->value['years'] ) ) {

				$value += absint( $this->value['years'] ) * 365 * 24 * 60;
			}

			if ( isset( $this->value['months'] ) ) {

				$value += absint( $this->value['months'] ) * 30 * 24 * 60;
			}

			if ( isset( $this->value['days'] ) ) {

				$value += absint( $this->value['days'] ) * 24 * 60;
			}

			if ( isset( $this->value['hours'] ) ) {

				$value += absint( $this->value['hours'] ) * 60;
			}

			if ( isset( $this->value['minutes'] ) ) {

				$value += absint( $this->value['minutes'] );
			}
		}

		return $value;
	}


	/**
	 * @param mixed $value
	 * @param mixed $validate Optional. False by default.
	 */
	public function setValue( $value, $validate = false ) {

		if ( ! is_array( $value ) ) {

			$rest    = $value;
			$years   = intdiv( $rest, 525600 /** 365 * 24 * 60 */ );
			$rest    = $rest - $years * 525600;
			$months  = intdiv( $rest, 43200 /** 30 * 24 * 60 */ );
			$rest    = $rest - $months * 43200;
			$days    = intdiv( $rest, 1440 /** 24 * 60 */ );
			$rest    = $rest - $days * 1440;
			$hours   = intdiv( $rest, 60 );
			$minutes = $rest - $hours * 60;

			$value = array(
				'years'   => $years,
				'months'  => $months,
				'days'    => $days,
				'hours'   => $hours,
				'minutes' => $minutes,
			);
		}

		parent::setValue( $value, $validate );
	}

	/**
	 * @return string
	 */
	public function renderLabel() {
		return $this->hasLabel() ? '<label>' . esc_html( $this->label ) . '</label>' : '';
	}

	/**
	 * @return string
	 *
	 * @since 1.10.2
	 */
	public function renderInput() {

		$output = '<div ' . mpa_tmpl_atts( $this->inputAtts() ) . '><label>';

		if ( 0 < $this->maxYears ) {

			$yearsRange   = range( $this->minYears, $this->maxYears );
			$yearsOptions = array_combine( $yearsRange, $yearsRange );

			$output .= mpa_tmpl_select( $yearsOptions, $this->value['years'], array( 'name' => "{$this->inputName}[years]" ) ) .
				'&nbsp;' . esc_html__( 'years', 'motopress-appointment' ) . '&nbsp;';
		}

		if ( 0 < $this->maxMonths ) {

			$monthsRange   = range( $this->minMonths, $this->maxMonths );
			$monthsOptions = array_combine( $monthsRange, $monthsRange );

			$output .= mpa_tmpl_select( $monthsOptions, $this->value['months'], array( 'name' => "{$this->inputName}[months]" ) ) .
				'&nbsp;' . esc_html__( 'months', 'motopress-appointment' ) . '&nbsp;';
		}

		if ( 0 < $this->maxDays ) {

			$daysRange   = range( $this->minDays, $this->maxDays );
			$daysOptions = array_combine( $daysRange, $daysRange );

			$output .= mpa_tmpl_select( $daysOptions, $this->value['days'], array( 'name' => "{$this->inputName}[days]" ) ) .
				'&nbsp;' . esc_html__( 'days', 'motopress-appointment' ) . '&nbsp;';
		}

		if ( 0 < $this->maxHours ) {

			$hoursRange   = range( $this->minHours, $this->maxHours );
			$hoursOptions = array_combine( $hoursRange, $hoursRange );

			$output .= mpa_tmpl_select( $hoursOptions, $this->value['hours'], array( 'name' => "{$this->inputName}[hours]" ) ) .
				'&nbsp;' . esc_html__( 'hours', 'motopress-appointment' ) . '&nbsp;';
		}

		if ( 0 < $this->maxMinutes ) {

			$minutesRange   = range( $this->minMinutes, $this->maxMinutes, $this->minutesStep );
			$minutesOptions = array_combine( $minutesRange, $minutesRange );

			$output .= mpa_tmpl_select( $minutesOptions, $this->value['minutes'], array( 'name' => "{$this->inputName}[minutes]" ) ) .
				'&nbsp;' . esc_html__( 'minutes', 'motopress-appointment' );
		}

		$output .= '</label></div>';

		return $output;
	}
}
