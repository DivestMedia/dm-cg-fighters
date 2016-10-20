<div id="container-event-details">
<?php
	$decision = ['UD','MD','SD','TKO','KO','NC'];
	$result = ['Win','Loss','Draw'];
?>

		<div class="cont-tb">
			<label for="ed_date">Date: </label>
			<input type="text" name="ed_date" id="ed_date" value="<?=!empty($ed_meta_data['ed_date'])?$ed_meta_data['ed_date']:''?>">
		</div>

		<div class="cont-tb">
			<label for="ed_fighter">Fighter: </label>
			<input type="text" name="ed_fighter" id="ed_fighter" value="<?=!empty($ed_meta_data['ed_fighter'])?$ed_meta_data['ed_fighter']:''?>" />
			<input type="hidden" name="ed_fighter_id" id="ed_fighter_id" value="<?=!empty($ed_meta_data['ed_fighter_id'])?$ed_meta_data['ed_fighter_id']:''?>" />
		</div>

		<div class="cont-tb">
			<label for="ed_opponent">Opponent: </label>
			<input type="text" name="ed_opponent" id="ed_opponent" value="<?=!empty($ed_meta_data['ed_opponent'])?$ed_meta_data['ed_opponent']:''?>" />
			<input type="hidden" name="ed_opponent_id" id="ed_opponent_id" value="<?=!empty($ed_meta_data['ed_opponent_id'])?$ed_meta_data['ed_opponent_id']:''?>" />
		</div>

		<div class="cont-tb">
			<label for="ed_location">Location: </label>
			<input type="text" name="ed_location" id="ed_location" value="<?=!empty($ed_meta_data['ed_location'])?$ed_meta_data['ed_location']:''?>" />
		</div>


			<div class="cont-tb">
				<label for="ed_result">Result: </label>
				<select name="ed_result" id="ed_result" value="<?=!empty($ed_meta_data['ed_result'])?$ed_meta_data['ed_result']:''?>">
<?php
	foreach ($result as $r) {
		$selected = '';
		if(!empty($ed_meta_data['ed_result'])&&$ed_meta_data['ed_result']==$r)
			$selected = 'selected="selected"';
?>
					<option <?=$selected?>><?=$r?></option>
<?php
	}
?>
				</select>
			</div>

			<div class="cont-tb">
				<label for="ed_decision">Decision: </label>
				<select name="ed_decision" id="ed_decision" value="<?=!empty($ed_meta_data['ed_decision'])?$ed_meta_data['ed_decision']:''?>">
<?php
	foreach ($decision as $d) {
		$selected = '';
		if(!empty($ed_meta_data['ed_decision'])&&$ed_meta_data['ed_decision']==$d)
			$selected = 'selected="selected"';
?>
		<option <?=$selected?>><?=$d?></option>
<?php
	}
?>
				</select>
			</div>

		<div class="cont-tb">
			<label for="ed_location">Round: </label>
			<input type="text" name="ed_round" id="ed_round" value="<?=!empty($ed_meta_data['ed_round'])?$ed_meta_data['ed_round']:''?>" />
		</div>


		<div class="cont-tb">
			<label for="ed_location">Time: </label>
			<input type="text" name="ed_time" id="ed_time" value="<?=!empty($ed_meta_data['ed_time'])?$ed_meta_data['ed_time']:''?>" />
		</div>

</div>