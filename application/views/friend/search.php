<?php $this->load->view('friend/side_nav'); ?>
<section class="right">
	<form action="#" method="post">
		<fieldset>
			<legend>Find Friends</legend>
			<dl>
				<dt><label for="q">Username/Email:</label></dt>
				<dd><input type="text" name="q" id="q" placeholder="Search by email or username" size="40" /></dd>
				<!--<dt>&nbsp;</dt>
				<dd><input type="submit" name="friend_search" value="Search" /></dd>-->
			</dl>
		</fieldset>
	</form>
	<table id="friend_search_results">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th class="sortable">Username</th>
				<th class="sortable">Email</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</section>
