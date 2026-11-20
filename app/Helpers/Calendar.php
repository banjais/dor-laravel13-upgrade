<?php
use Carbon\Carbon;

class Calendar {

	private $_nep_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'nmonth' => '', 'num_day' => '');

	private $_eng_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'emonth' => '', 'num_day' => '');

	public $debug_info = "";

	/**
	* Return nepali number
	* @param int $num
	* @return string
	*/
	private static function _get_nep_num($num)
	{
		$numArray = array(
			'0' => '०',
			'1' => '१',
			'2' => '२',
			'3' => '३',
			'4' => '४',
			'5' => '५',
			'6' => '६',
			'7' => '७',
			'8' => '८',
			'9' => '९',
		);
		$arr = str_split($num);
		$returnValue = '';
		for($i=0; $i< count($arr); $i++){
			$new = $arr[$i];
			$returnValue .= (array_key_exists( $new , $numArray )) ? $numArray[$new] : false;
		}
		return $returnValue;

	}

		/**
		 * Return day
		 *
		 * @param int $day
		 * @return string
		 */
		private static function _get_day_of_week($day, $type)
		{
			$dayArray = array(
							'1' => array('Sunday', 'आइतवार'),
							'2' => array('Monday', 'सोमवार'),
							'3' => array('Tuesday', 'मङ्गलवार'),
							'4' => array('Wednesday', 'बुधवार'),
							'5' => array('Thursday', 'बिहिवार'),
							'6' => array('Friday', 'शुक्रवार'),
							'7' => array('Saturday', 'शनिवार')
						);
			return (array_key_exists( $day , $dayArray )) ? $dayArray[$day][$type] : false;
		}

		/**
		 * Return english month name
		 *
		 * @param int $m
		 * @return string
		 */
		private static function _get_english_month($m)
		{
			$monthArray = array(
							'01' => 'January',
							'02' => 'February',
							'03' => 'March',
							'04' => 'April',
							'05' => 'May',
							'06' => 'June',
							'07' => 'July',
							'08' => 'August',
							'09' => 'September',
							'10' => 'October',
							'11' => 'November',
							'12' => 'December',
						);
			return (array_key_exists ( $m , $monthArray )) ? $monthArray[$m] : false;

		}

		/**
		 * Return nepali month name
		 *
		 * @param int $m
		 * @return string
		 */
		private static function _get_nepali_month($m, $type)
		{
			$nepaliMonthArray = array(
									'01' => array('Baishak','बैशाख'),
									'02' => array('Jestha','जेठ'),
									'03' => array('Ashad','आषाढ'),
									'04' => array('Shrawn','साउन'),
									'05' => array('Bhadra','भाद्र'),
									'06' => array('Ashwin','आश्विन'),
									'07' => array('kartik','कार्तिक'),
									'08' => array('Mangshir','मंसिर'),
									'09' => array('Poush','पौष'),
									'10' => array('Magh','माघ'),
									'11' => array('Falgun','फाल्गुण'),
									'12' => array('Chaitra','चैत्र')
								);
			return (array_key_exists ( $m , $nepaliMonthArray )) ? $nepaliMonthArray[$m][$type] : false;

		}

		/**
		 * Check if date range is in english
		 *
		 * @param int $yy
		 * @param int $mm
		 * @param int $dd
		 * @return bool
		 */
		private static function _is_in_range_eng($yy, $mm, $dd)
		{
			if ($yy < 1944 || $yy > 2033)
			{
				$returnArray['type'] = false;
				$returnArray['message'] = 'Supported only between 1944-2022';
				return $returnArray;
			}

			if ($mm < 1 || $mm > 12)
			{
				$returnArray['type'] = false;
				$returnArray['message'] = 'Error! month value can be between 1-12 only';
				return $returnArray;
			}

			if ($dd < 1 || $dd > 31)
			{
				$returnArray['type'] = false;
				$returnArray['message'] = 'Error! day value can be between 1-31 only';
				return $returnArray;
			}

			$returnArray['type'] = true;
			$returnArray['message'] = 'Success! There is no Error.';
			return $returnArray;
		}

		/**
		 * Check if date is with in nepali data range
		 *
		 * @param int $yy
		 * @param int $mm
		 * @param int $dd
		 * @return bool
		 */
		private static function _is_in_range_nep($yy, $mm, $dd)
		{
			if ($yy < 2000 || $yy > 2089)
			{
				return 'Supported only between 2000-2089';
			}

			if ($mm < 1 || $mm > 12)
			{
				return 'Error! month value can be between 1-12 only';
			}

			if ($dd < 1 || $dd > 32)
			{
				return 'Error! day value can be between 1-32 only';
			}

			return TRUE;
		}

		/**
		 * Calculates whether english year is leap year or not
		 *
		 * @param int $year
		 * @return bool
		 */
		public static function is_leap_year($year)
		{
			$a = $year;
			if ($a % 100 == 0)
			{
				if ($a % 400 == 0)
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				if ($a % 4 == 0)
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
		}

		/**
		 * currently can only calculate the date between AD 1944-2033...
		 *
		 * @param int $yy
		 * @param int $mm
		 * @param int $dd
		 * @return array
		 */
		public static function eng_to_nep($yy, $mm, $dd)
		{
			// Check for date range
			$chk = self::_is_in_range_eng($yy, $mm, $dd);

			if($chk['type'] !== TRUE)
			{
				return $returnArray['type'] = false;
			}
			else
			{
				// Month data.
				$month  = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

				// Month for leap year
				$lmonth = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

				$def_eyy     = 1944;	// initial english date.
				$def_nyy     = 2000;
				$def_nmm     = 9;
				$def_ndd     = 17 - 1;	// inital nepali date.
				$total_eDays = 0;
				$total_nDays = 0;
				$a           = 0;
				$day         = 7 - 1;
				$m           = 0;
				$y           = 0;
				$i           = 0;
				$j           = 0;
				$numDay      = 0;

				// Count total no. of days in-terms year
				for ($i = 0; $i < ($yy - $def_eyy); $i++) //total days for month calculation...(english)
				{
					if (self::is_leap_year($def_eyy + $i) === TRUE)
					{
						for ($j = 0; $j < 12; $j++)
						{
							$total_eDays += $lmonth[$j];
						}
					}
					else
					{
						for ($j = 0; $j < 12; $j++)
						{
							$total_eDays += $month[$j];
						}
					}
				}


				// Count total no. of days in-terms of month
				for ($i = 0; $i < ($mm - 1); $i++)
				{
					if (self::is_leap_year($yy) === TRUE)
					{
						$total_eDays += $lmonth[$i];
					}
					else
					{
						$total_eDays += $month[$i];
					}
				}

				// Count total no. of days in-terms of date
                // dd($dd);
				$total_eDays += $dd;


				$i           = 0;
				$j           = $def_nmm;
				$total_nDays = $def_ndd;
				$m           = $def_nmm;
				$y           = $def_nyy;

				// Count nepali date from array
				while ($total_eDays != 0)
				{
					$a = config('calender.'.$i)[$j];

					$total_nDays++;		//count the days
					$day++;				//count the days interms of 7 days

					if ($total_nDays > $a)
					{
						$m++;
						$total_nDays = 1;
						$j++;
					}

					if ($day > 7)
					{
						$day = 1;
					}

					if ($m > 12)
					{
						$y++;
						$m = 1;
					}

					if ($j > 12)
					{
						$j = 1;
						$i++;
					}

					$total_eDays--;
				}

                if($total_nDays == '1'){ $total_nDays = '01'; }
                if($total_nDays == '2'){ $total_nDays = '02'; }
                if($total_nDays == '3'){ $total_nDays = '03'; }
                if($total_nDays == '4'){ $total_nDays = '04'; }
                if($total_nDays == '5'){ $total_nDays = '05'; }
                if($total_nDays == '6'){ $total_nDays = '06'; }
                if($total_nDays == '7'){ $total_nDays = '07'; }
                if($total_nDays == '8'){ $total_nDays = '08'; }
                if($total_nDays == '9'){ $total_nDays = '09'; }

                if($m == '1'){ $m = '01'; }
                if($m == '2'){ $m = '02'; }
                if($m == '3'){ $m = '03'; }
                if($m == '4'){ $m = '04'; }
                if($m == '5'){ $m = '05'; }
                if($m == '6'){ $m = '06'; }
                if($m == '7'){ $m = '07'; }
                if($m == '8'){ $m = '08'; }
                if($m == '9'){ $m = '09'; }

				$numDay = $day;

				$returnArray['type'] = true;
				$returnArray['nepali_year']		= self::_get_nep_num($y);
				$returnArray['nepali_month']	= self::_get_nep_num($m);
				$returnArray['nepali_date']		= self::_get_nep_num($total_nDays);
				$returnArray['nepali_day']		= self::_get_day_of_week($day, 1);
				$returnArray['nepali_nmonth']	= self::_get_nepali_month($m, 1);
				$returnArray['nepali_num_day']	= self::_get_nep_num($numDay);

                return $returnArray;
			}
		}


		/**
		 * Currently can only calculate the date between BS 2000-2089
		 *
		 * @param int $yy
		 * @param int $mm
		 * @param int $dd
		 * @return array
		 */
		public static function nep_to_eng($yy, $mm, $dd)
		{
			$def_eyy     = 1943;
			$def_emm     = 4;
			$def_edd     = 14 - 1;	// initial english date.
			$def_nyy     = 2000;
			$def_nmm     = 1;
			$def_ndd     = 1;		// iniital equivalent nepali date.
			$total_eDays = 0;
			$total_nDays = 0;
			$a           = 0;
			$day         = 4 - 1;
			$m           = 0;
			$y           = 0;
			$i           = 0;
			$k           = 0;
			$numDay      = 0;

			$month  = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
			$lmonth = array(0,31,29,31,30,31,30,31,31,30,31,30,31);

			// Check for date range
			$chk = self::_is_in_range_nep($yy, $mm, $dd);

			if ( $chk !== TRUE)
			{
				die($chk);
			}
			else
			{
				// Count total days in-terms of year
				for ($i = 0; $i < ($yy - $def_nyy); $i++)
				{
					for ($j = 1; $j <= 12; $j++)
					{
						//$total_nDays += $this->_bs[$k][$j];
						$total_nDays = config('calender.'.$k)[$j];
					}
					$k++;
				}


				// Count total days in-terms of month
				for ($j = 1; $j < $mm; $j++)
				{
					//$total_nDays += $this->_bs[$k][$j];
					$total_nDays += config('calender.'.$k)[$j];
				}

				// Count total days in-terms of dat
				$total_nDays += $dd;

				// Calculation of equivalent english date...
				$total_eDays = $def_edd;
				$m           = $def_emm;
				$y           = $def_eyy;
				while ($total_nDays != 0)
				{
					if (self::is_leap_year($y))
					{
						$a = $lmonth[$m];
					}
					else
					{
						$a = $month[$m];
					}

					$total_eDays++;
					$day++;

					if ($total_eDays > $a)
					{
						$m++;
						$total_eDays = 1;
						if ($m > 12)
						{
							$y++;
							$m = 1;
						}
					}

					if ($day > 7)
					{
						$day = 1;
					}

					$total_nDays--;
				}
                if($total_eDays == '1'){ $total_eDays = '01'; }
                if($total_eDays == '2'){ $total_eDays = '02'; }
                if($total_eDays == '3'){ $total_eDays = '03'; }
                if($total_eDays == '4'){ $total_eDays = '04'; }
                if($total_eDays == '5'){ $total_eDays = '05'; }
                if($total_eDays == '6'){ $total_eDays = '06'; }
                if($total_eDays == '7'){ $total_eDays = '07'; }
                if($total_eDays == '8'){ $total_eDays = '08'; }
                if($total_eDays == '9'){ $total_eDays = '09'; }

                if($m == '1'){ $m = '01'; }
                if($m == '2'){ $m = '02'; }
                if($m == '3'){ $m = '03'; }
                if($m == '4'){ $m = '04'; }
                if($m == '5'){ $m = '05'; }
                if($m == '6'){ $m = '06'; }
                if($m == '7'){ $m = '07'; }
                if($m == '8'){ $m = '08'; }
                if($m == '9'){ $m = '09'; }

				$numDay = $day;

				$returnArray = array();
				$returnArray['english_year']	= $y;
				$returnArray['english_month']	= $m;
				$returnArray['english_date']	= $total_eDays;
				$returnArray['english_day']		= self::_get_day_of_week($day, 0);
				$returnArray['english_nmonth']	= self::_get_english_month($m);
				$returnArray['english_num_day']	= $numDay;

                return $returnArray;

			}
		}

		public static function eng_to_nep_dor($date)
		{
			$arrdate 	= explode("-", $date);
			$yy		= $arrdate[0];
			$mm		= $arrdate[1];
			$dd		= $arrdate[2];
			$nepaliDate = self::eng_to_nep($yy, $mm, $dd);

			// dd($nepaliDate);
            if($nepaliDate){
				$date = $nepaliDate['nepali_year'] .' '. $nepaliDate['nepali_nmonth']. ' '. $nepaliDate['nepali_date'];
			}
			return $date;
		}

		public static function toFormattedDateString($date)
		{
			$arrdate 	= explode("-", $date);
			$dt = Carbon::create(intval($arrdate[0]), intval($arrdate[1]), intval($arrdate[2]) );
			return $dt->toFormattedDateString();
		}
	}
