<div class="admin-meta-box-fighter-images">
<div class="fighter-img-container">
  <?php
    $upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
    $_uf_images = explode(',', $gr_ov_data['_uf_image']);
  if(!empty($_uf_images)){
    foreach ($_uf_images as $id) {
      if(!empty($id)){
        $img_url = wp_get_attachment_image_src($id)[0];
        ?>
          <div class="cont-thumb-img" data-url="<?=$img_url?>" style="background: url('<?=$img_url?>') no-repeat center center;background-size: cover;"></div>
        <?php
      }
    }
  }
  ?>
  </div>
  <input type="hidden" name="_uf_image" value="<?=esc_attr($gr_ov_data['_uf_image'])?>">
  <p class="hide-if-no-js">
      <a class="upload-custom-img button" href='<?php echo $upload_link ?>'>Add Image/s</a>
  </p>
</div>
