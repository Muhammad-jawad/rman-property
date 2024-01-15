<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/admin/partials
 */




class Ma_Rman_show_frontend
{
    private $post_id;
    private $dataObject;


    // public function __construct() {

	// 	$this->post_id = $post_id;
    //     $this->dataObject = $dataObject;
        
	// }

    public function generate_html($dataObject, $post) {

        $property_fields = get_post_meta( $post, '_properties_data');

        var_dump($property_fields);

        foreach($dataObject as $e_field){

            if($e_field['type'] ==  "radio")
            {
                ?>
                <div class="row">
                    <div class="label"><?= $e_field['name'];?></div>
                        <div class="fields">
                            <?php
                                $index = 0;
                                foreach($e_field['values'] as $value):
                                    $checked = "";
                                    if(($property_fields[0][$e_field['id']] == $value)) {
                                        $checked = "checked";
                                    }
                                    echo "<label>
                                            <input type='{$e_field['type']}' name='_properties_data[{$e_field['id']}]' value='{$value}' {$checked}/> {$value} 
                                        </label>";

                                    $index++;

                                endforeach;
                            ?>
                        </div>
                </div>
                <?php
            }

            if($e_field['type'] ==  "checkbox")
            {
                ?>
                    <div class="row">
                    <div class="label"><?= $e_field['name'];?></div>
                        <div class="fields">
                            <?php
                                $index = 0;
                                foreach($e_field['values'] as $value):
                                    $checked = "";
                                    if(($property_fields[0][$e_field['id']] == $value)) {
                                        $checked = "checked";
                                    }
                                    echo "<label>
                                            <input type='{$e_field['type']}' name='_properties_data[{$e_field['id']}][$index]' value='{$value}' {$checked}/> {$value} 
                                        </label>";

                                    $index++;

                                endforeach;
                            ?>
                        </div>
                </div>
                <?php
            }

            else 
            {
                ?>
                <div class="row">
                    <div class="label"><?= $e_field['name'];?></div>
    
                    <div class="fields"><input type="<?= $e_field['type'];?>" name="_properties_data[<?= $e_field['id']; ?>]" value="<?php echo $property_fields[0][$e_field["id"]]; ?>"></div>
                </div>
    
                <?php
            }
           
        }
           
        
    }
}
?>