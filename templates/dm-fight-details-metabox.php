<div id="fight-nav-container">
	<div class="active fight-nav" data-tab="fight-card">Fight Card</div>
	<div class="fight-nav" data-tab="fight-result">Result</div>
</div>
<div id="fight-main-container" style="background-image:url('<?=DM_FIGHTERS_PLUGIN_URL?>/img/fight-bg.jpg');background-size: cover;background-repeat: no-repeat;background-position: top center;">
	<div id="fight-card">
		<div class="cont-left cont-details">
			<div class="cont-image cont-left-image"></div>
			<select class="dd-fighter-category" name="_fd_left_cat_id">
				<?php 
					foreach ($terms as $cat) {
						$selected = $cat->term_id == $fd_meta_data['_fd_left_cat_id']?'selected="selected"':'';
				?>
				<option value="<?=$cat->term_id?>" <?=$selected?>><?=$cat->name?></option>
				<?php }?>
			</select><br>
			<select class="dd-fighters" data-pos="left" name="_fd_left_id" data-selected="<?=$fd_meta_data['_fd_left_id']?>"></select><br>
			<label class="record"></label>
			<label class="nickname"></label>
			<label class="age"></label>
			<label class="height"></label>
			<label class="weight"></label>
			<label class="reach"></label>
			<label class="legreach"></label>
			<label>-</label>
			<label class="striking"></label>
			<label class="striking_attempts"></label>
			<label class="striking_defense"></label>
			<label>-</label>
			<label class="takedown"></label>
			<label class="takedown_attempts"></label>
			<label class="takedown_defense"></label>
		</div>
		<div class="cont-center cont-details">
			<label>VS</label>
			<label> - Record - </label>
			<label> - Nickname - </label>
			<label> - Age - </label>
			<label> - Height - </label>
			<label> - Weight - </label>
			<label> - Reach - </label>
			<label> - Leg Reach - </label>
			<label>SIGNIFICANT STRIKES</label>
			<label> - Striking - </label>
			<label> - Striking Attempts - </label>
			<label> - Striking Defense - </label>
			<label>GRAPPLING</label>
			<label> - Takedown - </label>
			<label> - Takedown Attempts - </label>
			<label> - Takedown Defense - </label>
		</div>
		<div class="cont-right cont-details">
			<div class="cont-image cont-right-image"></div>
			<select class="dd-fighter-category" name="_fd_right_cat_id">
				<?php 
					foreach ($terms as $cat) {
						$selected = $cat->term_id == $fd_meta_data['_fd_right_cat_id']?'selected="selected"':'';
				?>
				<option value="<?=$cat->term_id?>" <?=$selected?>><?=$cat->name?></option>
				<?php }?>
			</select><br>
			<select class="dd-fighters" data-pos="right" name="_fd_right_id" data-selected="<?=$fd_meta_data['_fd_right_id']?>"></select><br>
			<label class="record"></label>
			<label class="nickname"></label>
			<label class="age"></label>
			<label class="height"></label>
			<label class="weight"></label>
			<label class="reach"></label>
			<label class="legreach"></label>
			<label>-</label>
			<label class="striking"></label>
			<label class="striking_attempts"></label>
			<label class="striking_defense"></label>
			<label>-</label>
			<label class="takedown"></label>
			<label class="takedown_attempts"></label>
			<label class="takedown_defense"></label>
		</div>
	</div>
	<div id="fight-result" style="display:none;">
		<div id="container-fight-result">
			<div class="cont-tb cont_fr_result">
				<label>Result: </label>
				<select id="_fr_result" name="_fr_result">
					<?php
						foreach ($this->fight_result as $key => $value) {
							?>
							<option value="<?=$key?>" <?=$fd_meta_data['_fr_result']==$key?'selected':''?>><?=$value?></option>
							<?php
						}
					?>
				</select>
				<!-- <input type="text" name="_fr_result" id="_fr_result" value="<?=$fd_meta_data['_fr_result']?>" /> -->
			</div>
			<?php 
				$style = '';
				if($fd_meta_data['_fr_winner']=='-1')
					$style = 'style="display:none;"';
			?>
			<div class="cont-tb cont_fr_winner" <?=$style?>>
				<label>Winner: </label>
				<select id="_fr_winner" name="_fr_winner" data-value="<?=$fd_meta_data['_fr_winner']?>">
					
				</select>
				<!-- <input type="text" name="_fr_result" id="_fr_result" value="<?=$fd_meta_data['_fr_result']?>" /> -->
			</div>
			<?php
				$basic_info_fields = [
					'_fr_award'=>'Fight Award',
				];

				foreach ($basic_info_fields as $key => $value) {
			?>
					<div class="cont-tb">
						<label for="<?=$key?>"><?=$value?>: </label>
						<input type="text" name="<?=$key?>" id="<?=$key?>" value="<?=$fd_meta_data[$key]?>" />
					</div>
			<?php
				}

				$result_fields = [
					'_fr_left_control',
	                '_fr_left_knockdowns',
	                '_fr_left_strikes_landed',
	                '_fr_left_strikes_attempts',
	                '_fr_left_s_strikes_landed',
	                '_fr_left_s_strikes_attempts',
	                '_fr_left_takedowns',
	                '_fr_left_takedowns_attempts',
	                '_fr_left_submission_attempts',
	                '_fr_right_control',
	                '_fr_right_knockdowns',
	                '_fr_right_strikes_landed',
	                '_fr_right_strikes_attempts',
	                '_fr_right_s_strikes_landed',
	                '_fr_right_s_strikes_attempts',
	                '_fr_right_takedowns',
	                '_fr_right_takedowns_attempts',
	            	'_fr_right_submission_attempts',
				];
				foreach ($result_fields as $key) {
			?>
					<input type="hidden" name="<?=$key?>" id="<?=$key?>" value="<?=!empty($fd_meta_data[$key])?$fd_meta_data[$key]:''?>" />
			<?php
				}

			?>

			<div class="round-container">
				<label>Round: </label>
				<div class="active">1</div>
				<div>2</div>
				<div>3</div>
				<div>4</div>
				<div>5</div>
			</div>
			
			<div class="round-record-container">
				<div class="cont-record-label">
					<label class="active" data-tab="control">Control</label>
					<label data-tab="knockdowns">Knockdowns</label>
					<label data-tab="strikes">Strikes</label>
					<label data-tab="sstrikes">Significant Strikes</label>
					<label data-tab="takedowns">Takedowns</label>
					<label data-tab="submission">Submission Attempts</label>
				</div>
				<?php 
					$rv = [];
					foreach ($result_fields as $key) {
						$rv[$key] = !empty($fd_meta_data[$key])?explode(',', $fd_meta_data[$key]):null;
					}
					$ctr = 1;
					while($ctr<=5){
				?>
				<div class="cont-round round-<?=$ctr?> <?=$ctr==1?'active':''?>">
					<div class="cont-record-content active tab-control">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<label>Control:</label><input type="text" class="_fr_left_control" placeholder="Control" value="<?=!empty($rv['_fr_left_control'][$ctr-1])?$rv['_fr_left_control'][$ctr-1]:''?>" />
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<label>Control:</label><input type="text" class="_fr_right_control" placeholder="Control" value="<?=!empty($rv['_fr_right_control'][$ctr-1])?$rv['_fr_right_control'][$ctr-1]:''?>" />
						</div>
					</div>
					<div class="cont-record-content tab-knockdowns">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<label>Knockdowns:</label><input type="text" class="_fr_left_knockdowns" placeholder="Knockdowns" value="<?=!empty($rv['_fr_left_knockdowns'][$ctr-1])?$rv['_fr_left_knockdowns'][$ctr-1]:''?>" />
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<label>Knockdowns:</label><input type="text" class="_fr_right_knockdowns" placeholder="Knockdowns" value="<?=!empty($rv['_fr_right_knockdowns'][$ctr-1])?$rv['_fr_right_knockdowns'][$ctr-1]:''?>" />
						</div>
					</div>
					<div class="cont-record-content tab-strikes">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<div><label>Strikes Landed:</label><input type="text" class="_fr_left_strikes_landed" placeholder="Strikes Landed" value="<?=!empty($rv['_fr_left_strikes_landed'][$ctr-1])?$rv['_fr_left_strikes_landed'][$ctr-1]:''?>" /></div>
							<div><label>Strikes Attempt:</label><input type="text" class="_fr_left_strikes_attempts" placeholder="Strikes Attempt" value="<?=!empty($rv['_fr_left_strikes_attempts'][$ctr-1])?$rv['_fr_left_strikes_attempts'][$ctr-1]:''?>" /></div>
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<div><label>Strikes Landed:</label><input type="text" class="_fr_right_strikes_landed" placeholder="Strikes Landed" value="<?=!empty($rv['_fr_right_strikes_landed'][$ctr-1])?$rv['_fr_right_strikes_landed'][$ctr-1]:''?>" /></div>
							<div><label>Strikes Attempt:</label><input type="text" class="_fr_right_strikes_attempts" placeholder="Strikes Attempt" value="<?=!empty($rv['_fr_right_strikes_attempts'][$ctr-1])?$rv['_fr_right_strikes_attempts'][$ctr-1]:''?>" /></div>
						</div>
					</div>
					<div class="cont-record-content tab-sstrikes">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<div><label>Significant Strikes Landed:</label><input type="text" class="_fr_left_s_strikes_landed" placeholder="Significant Strikes Landed" value="<?=!empty($rv['_fr_left_s_strikes_landed'][$ctr-1])?$rv['_fr_left_s_strikes_landed'][$ctr-1]:''?>" /></div>
							<div><label>Significant Strikes Attempt:</label><input type="text" class="_fr_left_s_strikes_attempts" placeholder="Significant Strikes Attempt" value="<?=!empty($rv['_fr_left_s_strikes_attempts'][$ctr-1])?$rv['_fr_left_s_strikes_attempts'][$ctr-1]:''?>" /></div>
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<div><label>Significant Strikes Landed:</label><input type="text" class="_fr_right_s_strikes_landed" placeholder="Significant Strikes Landed" value="<?=!empty($rv['_fr_right_s_strikes_landed'][$ctr-1])?$rv['_fr_right_s_strikes_landed'][$ctr-1]:''?>" /></div>
							<div><label>Significant Strikes Attempt:</label><input type="text" class="_fr_right_s_strikes_attempts" placeholder="Significant Strikes Attempt" value="<?=!empty($rv['_fr_right_s_strikes_attempts'][$ctr-1])?$rv['_fr_right_s_strikes_attempts'][$ctr-1]:''?>" /></div>
						</div>
					</div>
					<div class="cont-record-content tab-takedowns">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<div><label>Takedowns:</label><input type="text" class="_fr_left_takedowns" placeholder="Takedowns" value="<?=!empty($rv['_fr_left_takedowns'][$ctr-1])?$rv['_fr_left_takedowns'][$ctr-1]:''?>" /></div>
							<div><label>Takedowns Landed:</label><input type="text" class="_fr_left_takedowns_attempts" placeholder="Takedowns Attempt" value="<?=!empty($rv['_fr_left_takedowns_attempts'][$ctr-1])?$rv['_fr_left_takedowns_attempts'][$ctr-1]:''?>" /></div>
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<div><label>Takedowns:</label><input type="text" class="_fr_right_takedowns" placeholder="Takedowns" value="<?=!empty($rv['_fr_right_takedowns'][$ctr-1])?$rv['_fr_right_takedowns'][$ctr-1]:''?>" /></div>
							<div><label>Takedowns Landed:</label><input type="text" class="_fr_right_takedowns_attempts" placeholder="Takedowns Attempt" value="<?=!empty($rv['_fr_right_takedowns_attempts'][$ctr-1])?$rv['_fr_right_takedowns_attempts'][$ctr-1]:''?>" /></div>
						</div>
					</div>
					<div class="cont-record-content tab-submission">
						<div class="crc-left crc-cont">
							<div class="fimage-cont-left"></div>
							<div class="fighter-left-name"></div>
							<div><label>Submission Attempts:</label><input type="text" class="_fr_left_submission_attempts" placeholder="Submission Attempts" value="<?=!empty($rv['_fr_left_submission_attempts'][$ctr-1])?$rv['_fr_left_submission_attempts'][$ctr-1]:''?>" /></div>
						</div>
						<div class="crc-right crc-cont">
							<div class="fimage-cont-right"></div>
							<div class="fighter-right-name"></div>
							<div><label>Submission Attempts:</label><input type="text" class="_fr_right_submission_attempts" placeholder="Submission Attempts" value="<?=!empty($rv['_fr_right_submission_attempts'][$ctr-1])?$rv['_fr_right_submission_attempts'][$ctr-1]:''?>" /></div>
						</div>
					</div>
				</div>
				<?php
					$ctr++;
					}
				?>
			</div>
			
		</div>
	</div>
</div>
