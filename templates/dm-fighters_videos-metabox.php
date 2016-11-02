<!-- <strong>NOTE:</strong> <p style="font-size: 13px;font-style: italic;display: inline-block;">first image - profile, second image - left image, third image - right image,</p> -->
<div class="admin-meta-box-fighter-videos">
  <div class="fighter-vid-container">
    <?php 
      $upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
      $_uf_video = explode(',', $gr_ov_data['_uf_video']);
      if(!empty($_uf_video)){
        foreach ($_uf_video as $id) {
          if(!empty($id)){
            $img_url = wp_get_attachment_image_src(get_post_thumbnail_id($id),'full')[0];
            ?>
              <div class="cont-thumb-img" data-id="<?=$id?>" data-url="<?=$img_url?>" style="background: url('<?=$img_url?>') no-repeat center center;background-size: cover;"></div>
            <?php
          }
        }
      }
    ?>
  </div>
  
  <input type="hidden" name="_uf_video" value="<?=esc_attr($gr_ov_data['_uf_video'])?>">
  <p class="hide-if-no-js">
    <button type="button" class="btn-get-all-videos button" >Add Video/s</button>
  </p>
  <div class="fighter-vid-preview-container"></div>
</div>
