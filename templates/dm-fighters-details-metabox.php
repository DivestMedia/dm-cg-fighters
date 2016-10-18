<div class="admin-meta-box-uf-information">

  <?php
  $basic_info_fields = [
    '_uf_firstname'=>'First Name',
    '_uf_lastname'=>'Last Name',
    '_uf_nickname'=>'Nickname',
    '_uf_hometown'=>'Hometown',
    '_uf_birthday'=>'Birthday',
    '_uf_association'=>'Association',
    '_uf_age'=>'Age',
    '_uf_height'=>'Height',
    '_uf_weight'=>'Weight',
    '_uf_reach'=>'Reach',
    '_uf_leg_reach'=>'Leg Reach',
  ];

  $skill_breakdown = [
    '_uf_rank'=>"Rank",
    '_uf_win'=>"Win",
    '_uf_loss'=>"Loss",
    '_uf_draw'=>"Draw",
    '_uf_summary'=>"Summary",
    '_uf_takedown'=>"Takedown",
    '_uf_takedownattempts'=>"Takedown Attempts",
    '_uf_takedowndefense'=>"Takedown Defense",
    '_uf_striking'=>"Striking",
    '_uf_strikingattempts'=>"Striking Attempts",
    '_uf_strikingdefense'=>"Striking Defense",
    '_uf_submission'=>"Submissions",
  ];


  foreach ($basic_info_fields as $key => $value) {
    if($key=="_uf_biography"){
      ?>
      <div class="cont-ta">
        <label for="<?=$key?>"><?=$value?>: </label>
        <textarea name="<?=$key?>" id="<?=$key?>"><?=$gr_ov_data[$key]?></textarea>
      </div>
      <?php
    }else{
      ?>
      <div class="cont-tb">
        <label for="<?=$key?>"><?=$value?>: </label>
        <input type="text" name="<?=$key?>" id="<?=$key?>" value="<?=$gr_ov_data[$key]?>" />
      </div>
      <?php
    }
  }
  ?>
  <h4>Skill Breakdown</h4>
  <?php
  foreach ($skill_breakdown as $key => $value) {
    ?>
    <div class="cont-tb">
      <label for="<?=$key?>"><?=$value?>: </label>
      <input type="text" name="<?=$key?>" id="<?=$key?>" value="<?=$gr_ov_data[$key]?>" />
    </div>
    <?php
  }
  ?>
</div>
