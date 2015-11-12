<?php

/**
 * Calendar View Controller
 */
class Calendar extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('calendar');
		$this->load->model('task_model');

	}

	/**
	 * Calendar View
	 */
	public function index()
	{
		//Output
		$this->page->build('task/calendar', $this->get_calendar_data());
	}

	/**
	 * Get the data for the calendar display
	 *
	 * @return mixed
	 */
	protected function get_calendar_data()
	{
		//Offset time for custom months
		if($this->uri->segment(3) && $this->uri->segment(4))
		{
			$year = $this->uri->segment(3);
			$month = $this->uri->segment(4);
		}

		$_months = array(
			1 => 'January', 2 => 'February',
			3 => 'March', 4 => 'April',
			5 => 'May', 6 => 'June',
			7 => 'July', 8 => 'August',
			9 => 'September', 10 => 'October',
			11 => 'November', 12 => 'December'
		);

		$year = (isset($year)) ? $year : date('Y');
		$month = (isset($month)) ? $month : date('m');

		$local_time = time();

		$data = array();

		$data['month'] = $_months[(int)$month].' '.$year;
		$data['calendar'] = array();
		$data['today'] = getdate();

		$days_in_month = $this->calendar->get_total_days($month, $year);

		// Set the starting day number
		$local_date = mktime(0, 0, 0, $month, 1, $year);
		$month_end  = mktime(0, 0, 0, $month, $days_in_month, $year);
		$date = getdate($local_date);
		$day  = 0 + 1 - $date["wday"];

		//Get tasks for each day
		$content = $this->task_model->get_day_task_list($local_date, $month_end, $days_in_month);

		// Set the current month/year/day
		// We use this to determine the "today" date
		$cur_year	= date("Y", $local_time);
		$cur_month	= date("m", $local_time);
		$cur_day	= date("j", $local_time);

		$is_current_month = ($cur_year == $year AND $cur_month == $month);

		$out = null;

		while ($day <= $days_in_month)
		{
			for ($i = 0; $i < 7; $i++)
			{
				if($i == 0)
				{
					$out .= '<tr>';
				}

				if ($day > 0 AND $day <= $days_in_month)
				{
					if (isset($content[$day]))
					{
						// Cells with content
						$out .= ($is_current_month == TRUE AND $day == $cur_day) ? '<td class="today">' : '<td>';
						$out .= '<div><span class="date">'.$day.'</span><ul>'.$content[$day].'</ul></div></td>';
					}
					else
					{
						// Cells with no content
						$out .= ($is_current_month == TRUE AND $day == $cur_day) ? '<td class="today">' : '<td>';
						$out .= '<div><span class="date">'.$day.'</span>&nbsp;</div></td>';
					}
				}
				else
				{
					// Blank cells
					$out .= '<td>&nbsp;</td>';
				}


				$day++;

				if($i == 6)
				{
					$out .= '</tr>';
				}
			}


		}

		$data['calendar'] = $out;

		return $data;
	}

}