
<div class="admin-meta-box-tabbable">
    <table style="width:100%;">
        <!-- <thead>
        <tr>
        <th width="300px;">Banner Upload</th>
        <th></th>
    </tr>
</thead> -->
<tbody>
    <?php
    $valuepack = [];

    foreach ($ab_upload_data as $field => $data):
        if(!empty($data['value'])):
            foreach ($data['value'] as $k => $value) {
                if(empty($valuepack[$k])){
                    $valuepack[$k] = [];
                }
                $valuepack[$k][$field] = [
                    'options' => $data['options'],
                    'value' => $value
                ];
            }
        else:
            $valuepack[0][$field] = [
                'options' => $data['options'],
                'value' => ''
            ];
        endif;
    endforeach;
    if(isset($_GET['banner-action'])):
        switch ($_GET['banner-action']) {
            case 'add-new-banner':
            $next = count($valuepack);
            foreach ($ab_upload_data as $field => $data):
                $valuepack[$next][$field] = [
                    'options' => $data['options'],
                    'value' => ''
                ];
            endforeach;
            break;
        }
    endif;
    foreach ($valuepack as $k=>$fields):
        ?>
        <tr class="banner-row">
            <td>
                <table style="width:100%">
                    <tbody>
                        <?php
                        foreach ($fields as $field => $data):
                            ?>
                            <tr data-field="<?=$field?>">
                                <td style="width:300px">
                                    <label>
                                        <?=$data['options']['label']?><br>
                                        <small><?=$data['options']['description']?></small>
                                    </label>
                                </td>
                                <td>
                                    <?php
                                    switch ($data['options']['type']) {
                                        case 'htmlcode':
                                        wp_editor( $data['value'], $field . '_editor_'.$k, [
                                            'textarea_name' => $field.'[]',
                                            'textarea_rows' => 4,
                                            'teeny' => true,
                                            'media_buttons' => false,
                                            'wpautop' => false,
                                        ]);
                                        break;
                                        case 'textarea': ?>
                                        <textarea type="text" name="<?=($field)?>" id="<?=($field)?>" <?=($data['options']['required'] ? 'required' : '')?> style="width:100%;" rows="4"><?=(!empty($data['value']) ? esc_textarea($data['value']) : '')?></textarea>
                                        <?php break;
                                        case 'number':?>
                                        <input type="number" name="<?=($field)?>[]" id="<?=($field)?>" min="1" value="<?=($data['value'])?>" style="width:100px;" <?=($data['options']['required'] ? 'required' : '')?>>
                                        <?php break;
                                        case 'text':?>
                                        <?php default: ?>
                                        <input type="text" name="<?=($field)?>[]" id="<?=($field)?>" value="<?=($data['value'])?>" class="regular-text" <?=($data['options']['required'] ? 'required' : '')?>>
                                        <?php break;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>

                            </td>

                            <td>
                                <button class="button button-secondary delete-banner">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<a class="button button-primary add-new-banner" href="<?=add_query_arg([
    'banner-action' => 'add-new-banner'
    ])?>">Add New Banner Code</a>
</div>
<script>
    jQuery(function($){
        $('.delete-banner').click(function(){
            $(this).closest('tr.banner-row').remove();
        });
    });
</script>
