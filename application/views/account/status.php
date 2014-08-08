<?php if($this->session->userdata('username') != 'guest') $this->load->view('account/side_nav'); //Not the guest account ?>
<section id="task_view" class="right">
	<h1>Your Account</h1>
	<dl>
		<dt>User Id</dt>
		<dd><?= ($this->session->userdata('num_format') == 1) ? $this->todo->kanji_num($this->session->userdata('uid')) : $this->session->userdata('uid') ?></dd>

		<dt>Username</dt>
		<dd><?= $user ?></dd>

		<dt>Email</dt>
		<dd><?= $email ?></dd>


		<dt>Timezone</dt>
		<dd>
			<select id="timezone" name="timezone">
		    <?php
		    $continent = '';
		    $timezone_identifiers = DateTimeZone::listIdentifiers();
		    foreach( $timezone_identifiers as $value ){
		        if ( preg_match( '/^(America|Asia|Atlantic|Europe|Indian|Pacific)\//', $value ) ){
		            $ex=explode("/",$value);//obtain continent,city
		            if ($continent!=$ex[0]){
		                if ($continent!="") ?></optgroup><?php
		                ?><optgroup label="<?=$ex[0]?>"><?php
		            }

		            $city=$ex[1];
		            $city .= (isset($ex[2])) ? "/".$ex[2] : "";
		            $continent=$ex[0];
		            ?><option <?= ($value == $timezone) ? 'selected="selected"' : "";?> value="<?=$value?>"><?= $city ?></option><?php
		        }
		    }
    		?>
        		</optgroup>
    		</select>
		</dd>
		<dt>Id Number Format</dt>
		<dd>
			<select name="num_format" id="num_format">
				<option value="0" <?= ($num_format == 0) ? 'selected="selected"' : '' ?>>Arabic (1,2,3)</option>
				<option value="1" <?= ($num_format == 1) ? 'selected="selected"' : '' ?>>Chinese (一,二,三)</option>
				<option value="-1" <?= ($num_format == -1) ? 'selected="selected"' : '' ?>>Hide Ids</option>
			</select>
		</dd>
	</dl>
</section>