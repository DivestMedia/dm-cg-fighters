<div id="container-event-details">
<?php
	$decision = ['NA','UD','MD','SD','TKO','KO','NC'];
	$result = ['NA','Win','Loss','Draw'];
?>

		<div class="cont-tb">
			<label for="_ed_date">Date: </label>
			<input type="text" name="_ed_date" id="_ed_date" value="<?=!empty($ed_meta_data['_ed_date'])?$ed_meta_data['_ed_date']:''?>">
		</div>

		<div class="cont-tb">
			<label for="_ed_fighter">Fighter: </label>
			<input type="text" name="_ed_fighter" id="_ed_fighter" value="<?=!empty($ed_meta_data['_ed_fighter'])?$ed_meta_data['_ed_fighter']:''?>" />
			<input type="hidden" name="_ed_fighter_id" id="_ed_fighter_id" value="<?=!empty($ed_meta_data['_ed_fighter_id'])?$ed_meta_data['_ed_fighter_id']:''?>" />
		</div>

		<div class="cont-tb">
			<label for="_ed_opponent">Opponent: </label>
			<input type="text" name="_ed_opponent" id="_ed_opponent" value="<?=!empty($ed_meta_data['_ed_opponent'])?$ed_meta_data['_ed_opponent']:''?>" />
			<input type="hidden" name="_ed_opponent_id" id="_ed_opponent_id" value="<?=!empty($ed_meta_data['_ed_opponent_id'])?$ed_meta_data['_ed_opponent_id']:''?>" />
		</div>

		<div class="cont-tb">
			<label for="_ed_location">Location: </label>
			<input type="text" name="_ed_location" id="_ed_location" value="<?=!empty($ed_meta_data['_ed_location'])?$ed_meta_data['_ed_location']:''?>" />
		</div>


			<div class="cont-tb">
				<label for="_ed_result">Result: </label>
				<select name="_ed_result" id="_ed_result" value="<?=!empty($ed_meta_data['_ed_result'])?$ed_meta_data['_ed_result']:''?>">
<?php
	foreach ($result as $r) {
		$selected = '';
		if(!empty($ed_meta_data['_ed_result'])&&$ed_meta_data['_ed_result']==$r)
			$selected = 'selected="selected"';
?>
					<option <?=$selected?>><?=$r?></option>
<?php
	}
?>
				</select>
			</div>

			<div class="cont-tb">
				<label for="_ed_decision">Decision: </label>
				<select name="_ed_decision" id="_ed_decision" value="<?=!empty($ed_meta_data['_ed_decision'])?$ed_meta_data['_ed_decision']:''?>">
<?php
	foreach ($decision as $d) {
		$selected = '';
		if(!empty($ed_meta_data['_ed_decision'])&&$ed_meta_data['_ed_decision']==$d)
			$selected = 'selected="selected"';
?>
		<option <?=$selected?>><?=$d?></option>
<?php
	}
?>
				</select>
			</div>

		<div class="cont-tb">
			<label for="_ed_round">Round: </label>
			<input type="text" name="_ed_round" id="_ed_round" value="<?=!empty($ed_meta_data['_ed_round'])?$ed_meta_data['_ed_round']:''?>" />
		</div>


		<div class="cont-tb">
			<label for="_ed_time">Time: </label>
			<input type="text" name="_ed_time" id="_ed_time" value="<?=!empty($ed_meta_data['_ed_time'])?$ed_meta_data['_ed_time']:''?>" />
		</div>

</div>