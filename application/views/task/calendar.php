<?php $this->load->view('task/side_nav'); ?>
<?php $y = $this->uri->segment(3);
$m = $this->uri->segment(4);
$next = ($this->uri->segment(4) == FALSE) ? $this->calendar->adjust_date(date('m') + 1, date('Y')) : 
	$this->calendar->adjust_date($m + 1, $y);
	
$prev = ($this->uri->segment(4) == FALSE) ? $this->calendar->adjust_date(date('m') - 1, date('Y')) : 
$this->calendar->adjust_date($m - 1, $y); ?>
<section id="task_view" class="right">
	<h1>Task Calendar</h1>
	<p>Today is <?= date("l, F d, Y") ?><br />
		<a href="<?= site_url('task/calendar/'.$prev['year'].'/'.$prev['month']); ?>">&laquo; Previous Month</a>&nbsp;&nbsp;
		<a href="<?= site_url('task/calendar/'.$next['year'].'/'.$next['month']); ?>">Next Month &raquo;</a>
	</p>
	<table id="task_calendar">
		<caption><?= $month ?>
		</caption>
		<thead>
			<tr>
				<th>Sunday</th>
				<th>Monday</th>
				<th>Tuesday</th>
				<th>Wednesday</th>
				<th>Thursday</th>
				<th>Friday</th>
				<th>Saturday</th>
			</tr>
		</thead>
		<tbody>
			<?= $calendar ?>
		</tbody>
	</table>
</section>