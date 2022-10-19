<?php

/**
 * Plugin Name:       Our MetaBox
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Practice plugin about custom metabox from LWHH.
 * Version:           1.0
 * Author:            Habib
 * Author URI:        https://github.com/habibur-dev
 * License:           GPL v2 or later
 * Text Domain:       our-metabox
 * Domain Path:       /languages
 */

 class OurMetaBox{
    public function __construct(){
        add_action('plugins_loaded', array($this, 'omb_load_textdomain'));
        add_action('admin_menu', array($this, 'omb_add_metabox'));
        add_action('save_post', array($this, 'omb_save_metabox'));
    }

    public function omb_add_metabox(){
        add_meta_box(
            'omb_post_location', 
            __('Location Info', 'our-metabox'), 
            array($this, 'omb_display_metabox'),
            array('post', 'page'),
        );
    }

    public function omb_display_metabox($post){
        $location = get_post_meta($post->ID, 'omb_location', true);
        $country = get_post_meta($post->ID, 'omb_country', true);
        $is_favorite = get_post_meta($post->ID, 'omb_is_favorite', true);
        $checked = $is_favorite == 1? 'checked' : '';

        $saved_colors = get_post_meta($post->ID, 'omb_clrs', true);
        $saved_rcolors = get_post_meta($post->ID, 'omb_rclrs', true);
        $saved_seasons = get_post_meta($post->ID, 'omb_seasons', true);

        $label = __('Location', 'our-metabox');
        $label2 = __('Country', 'our-metabox');
        $label3 = __('Is Favorite', 'our-metabox');
        $label4 = __('Colors', 'our-metabox');
        $label5 = __('Radio Colors', 'our-metabox');
        $label6 = __('Select Season', 'our-metabox');

        $colors = array('red', 'green', 'blue', 'yellow', 'magenta', 'pink', 'black', 'white');
        $seasons = array('summer', 'rainy', 'autumn', 'late autumn', 'winter', 'spring');

        wp_nonce_field( 'omb_location', 'omb_location_field' );
        $metabox = <<<EOD
<p>
<label for="omb_location">{$label}</label>
<input type="text" name="omb_location" id="omb_location" value="{$location}">
<br>
<label for="omb_country">{$label2}</label>
<input type="text" name="omb_country" id="omb_country" value="{$country}">
</p>

<p>
<label for="omb_is_favorite">{$label3}</label>
<input type="checkbox" name="omb_is_favorite" id="omb_is_favorite" value="1" {$checked}>
</p>

<p>
<label>{$label4}: </label>

EOD;
    
        foreach($colors as $color){
            $_color = ucwords($color);
            $checked = in_array($color, $saved_colors) ? 'checked' : '';
            $metabox .= <<<EOD
<label for="omb_clr_{$color}">{$_color}</label>
<input type="checkbox" name="omb_clrs[]" id="omb_clr_{$color}" value="{$color}" {$checked}>
EOD;
        }

        $metabox .= "</p>";

        $metabox .= <<<EOD
<p>
<label>{$label5}: </label>
EOD;

        foreach($colors as $color){
            $_color = ucwords($color);
            $checked = ($color == $saved_rcolors) ? 'checked="checked"' : '';
            $metabox .= <<<EOD
<label for="omb_rclr_{$color}">{$_color}</label>
<input type="radio" name="omb_rclrs" id="omb_rclr_{$color}" value="{$color}" {$checked}>
EOD;
    
        }

        $metabox .= "</p>";
        $metabox .= <<<EOD
<p>
<label for="omb_seasons">{$label6}: </label>
<select name="omb_seasons" id="omb_seasons">
EOD;    

        foreach($seasons as $season){
            $_season = ucwords($season);
            $selected = ($season == $saved_seasons) ? 'selected' : '';
            $metabox .= <<<EOD
<option value="{$season}" {$selected}>{$_season}</option>
EOD;
        }
        $metabox .= "</select>";
        $metabox .= "</p>";
        echo $metabox;
    }

    public function omb_load_textdomain(){
        load_plugin_textdomain('our-metabox', false, dirname(__FILE__)."/languages");
    }

    private function is_secured($nonce_field, $action, $post_id){
        $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : '';

        if($nonce == ''){
            return false;
        }

        if(!wp_verify_nonce($nonce, $action)){
            return false;
        }

        if(!current_user_can('edit_post', $post_id))
        {
            return false;
        }

        if(wp_is_post_autosave($post_id)){
            return false;
        }
        if(wp_is_post_revision($post_id)){
            return false;
        }

        return true;
    }

    public function omb_save_metabox($post_id){
        if(!$this->is_secured('omb_location_field', 'omb_location', $post_id)){
            return $post_id;
        }
        $location = isset($_POST['omb_location']) ? $_POST['omb_location'] : '';
        $country = isset($_POST['omb_country']) ? $_POST['omb_country'] : '';
        $is_favorite = isset($_POST['omb_is_favorite']) ? $_POST['omb_is_favorite'] : '';
        $colors = isset($_POST['omb_clrs']) ? $_POST['omb_clrs'] : array();
        $radio_colors = isset($_POST['omb_rclrs']) ? $_POST['omb_rclrs'] : '';
        $omb_seasons = isset($_POST['omb_seasons']) ? $_POST['omb_seasons'] : '';

        if($location == '' && $country == ''){
            return $post_id;
        }

        $location = sanitize_text_field($location);
        $country = sanitize_text_field($country);

        update_post_meta($post_id, 'omb_location', $location);
        update_post_meta($post_id, 'omb_country', $country);
        update_post_meta($post_id, 'omb_is_favorite', $is_favorite);
        update_post_meta($post_id, 'omb_clrs', $colors);
        update_post_meta($post_id, 'omb_rclrs', $radio_colors);
        update_post_meta($post_id, 'omb_seasons', $omb_seasons);
    }
    

 }

new OurMetaBox();
