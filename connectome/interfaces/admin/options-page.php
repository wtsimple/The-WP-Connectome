<?php

namespace Connectome;

class OptionPage
{
    private $optionGroup;
    private $page;
    private $postTypes;
    /**
    * Object to encapsulate option naming functionality
    * @var SettingsOptionHandler
    */
    private $optionHandler;

    public function __construct()
    {
        $this->optionGroup = OptionStorage::get_option('OPTIONS_NAME');
        $this->page = OptionStorage::get_option('OPTIONS_NAME');
        $this->postTypes = $this->get_post_types();
        $this->optionHandler = new SettingsOptionHandler();
    }

    /**
    * Sets the WP hooks for needed to show the settings page
    *
    * @return void
    */
    public function set_hooks()
    {
        add_action('admin_menu', [$this, 'register_option_page']);
        add_action('admin_init', [$this, 'register_fields']);
    }

    /**
    * Creates a WP settings page with name and location
    *
    * @return void
    */
    public function register_option_page()
    {
        add_options_page('Connectome Options', 'The Connectome', 'manage_options', $this->page, [$this, 'connectome_admin_page']);
    }

    /**
    * Registers all the option fields the page will have
    *
    * It calls other functions to actually generates the fields
    * @return void
    */
    public function register_fields()
    {
        register_setting($this->optionGroup, $this->optionGroup);
        $section = 'main_section';
        add_settings_section($section, 'Graph Settings', [$this, 'main_section'], $this->page);
        $fieldPrefix = 'connectome_element_';
        // Users field
        $usersAmount = count_users()['total_users'];
        $userArgs = ['type' => ['name' => 'user', 'amount' => $usersAmount], 'index' => 0];
        add_settings_field(
            $fieldPrefix . 'users',
            'Users',
            [$this, 'element_type_field'],
            $this->page,
            $section,
            $userArgs
        );
        // Terms field
        $termsAmount = $this->get_amount_of_terms();
        $termArgs = ['type' => ['name' => 'term', 'amount' => $termsAmount], 'index' => 1];
        add_settings_field(
            $fieldPrefix . 'terms',
            'Taxonomy Terms',
            [$this, 'element_type_field'],
            $this->page,
            $section,
            $termArgs
        );
        // Post types fields
        foreach ($this->postTypes as $index => $type) {
            add_settings_field(
                $fieldPrefix . $type['name'],
                ucfirst($type['name']) . 's',
                [$this, 'element_type_field'],
                $this->page,
                $section,
                ['type' => $type, 'index' => $index + 2, 'post_type' => true]
            );
        }
    }

    /**
    * Generates the heading for the first section
    * @return void
    */
    public function main_section()
    {
        echo '<h3>Elements to be included </h3> <p>-1 means all, 0 means none</p>';
    }

    public function element_type_field($args)
    {
        $colorPalette = OptionStorage::get_option('COLOR_PALETTE');
        $index = $args['index'];
        $type = $args['type'];
        $subType = isset($args['post_types']) ? 'postTypes' : '';
        $amountField = $this->optionHandler->generate_name(['types', $type['name'], $subType, 'max']);
        $amount = $this->get_field($amountField, -1);
        $colorField = $this->optionHandler->generate_name(['types', $type['name'], $subType, 'color']);
        $color = $this->get_field($colorField, $colorPalette[$index]); ?>
<span> Color: </span>
<input disabled id='<?php echo $colorField ?>'
    name='<?php echo  $this->optionGroup . '[' . $colorField . ']'?>'
    type='color' value='<?php echo  $color ?>' />
<span style='margin-left:10px;'> Max: </span>
<input style='max-width:80px;' id='<?php  echo $amountField ?>'
    name='<?php echo $this->optionGroup . '[' . $amountField . ']'?>'
    type='number' min='-1' value='<?php  echo $amount ?>' />
<span style='margin-left:10px;'>Total: <span class='elements-amount'><?php echo $type['amount'] ?></span></span>
<?php
    }

    /**
    * Generates the options page html
    *
    * @return void
    */
    public function connectome_admin_page()
    {
        ?>
<div class="wrap">
    <h2>The WP Connectome</h2>

    <div class="graph">
        <h3>Graph Widget</h3>
        <div style="max-width:860px;">
            <p><strong>TIPS: </strong> Insert <strong
                    style="color:#195770; font-size:1.2em;">[connectome-graph]</strong> anywhere in a page or a post to
                show
                this widget.</p>
            <p>
                The widget might not look the same on the user's side because the css styles
                applied could be different. Click once on a node to select it, twice to show information.
                The unconnected nodes are in the widget but they fly away from the graph center. </p>
        </div>
        <div><?php echo apply_filters('the_content', '[connectome-graph]'); ?>
        </div>
    </div>

    <form action="options.php" method="post" class="connectome-form">
        <?php settings_fields($this->optionGroup); ?>
        <?php do_settings_sections($this->page); ?>
        <p class="submit">
            <input name="Submit" type="submit" class="button-primary"
                value="<?php esc_attr_e('Save Changes & Build Graph'); ?>" />
        </p>
    </form>

</div>

<script>
    var fix_elements_numbers = function(params) {
        let maxNumbers = jQuery("input[type='number']");
        let amount = 0;
        maxNumbers.each(function(index) {
            let number = parseInt(jQuery(this).attr('value'));
            let totalEl = jQuery(this).parent().find("span.elements-amount");
            let total = parseInt(totalEl.html());

            if (number < 0) {
                amount += total;
            } else {
                amount += Math.min(number, total);
            }
        });
        jQuery("p.total-information").remove();
        jQuery("<p class='total-information'>Total to show: <strong style='margin-right:10px;'> " + amount +
                " </strong>  (We recommend less than 300)</p>")
            .insertAfter(
                "form.connectome-form p");
    }
    jQuery(document).ready(fix_elements_numbers);
    jQuery("input[type='number']").change(fix_elements_numbers);
</script>

<?php
    }

    public function get_post_types()
    {
        $types = get_all_post_types();

        $postTypes = [];
        if (!empty($types)) {
            foreach ($types as $type) {
                $postTypes[] = ['name' => $type, 'amount' => $this->count_post_types($type)];
            }
        }

        return $postTypes;
    }

    public function count_post_types($type = '')
    {
        $storedCount = get_post_types_count();
        if (isset(wp_count_posts($type)->publish)) {
            return wp_count_posts($type)->publish;
        } elseif (isset($storedCount[$type])) {
            return $storedCount[$type];
        }
        return null;
    }

    public function get_options()
    {
        return get_option($this->optionGroup);
    }

    public function get_field(string $name, $default)
    {
        $options = $this->get_options();
        if (isset($options[$name])) {
            return $options[$name];
        } else {
            return $default;
        }
    }

    /**
    * Gets the total amount of terms existing in all taxonomies
    *
    * @return int
    */
    public function get_amount_of_terms()
    {
        $taxonomies = get_taxonomies();
        $amount = 0;
        foreach ($taxonomies as $tax) {
            $amount += wp_count_terms($tax);
        }
        return $amount;
    }

    public static function get_color_palette()
    {
    }
}

    $optionPage = new OptionPage();
    $optionPage->set_hooks();

    // function setting_dropdown_fn()
    // {
        //     $options = get_option('plugin_options');
        //     $items = ['Red', 'Green', 'Blue', 'Orange', 'White', 'Violet', 'Yellow'];
        //     echo "<select id='drop_down1' name='plugin_options[dropdown1]'>";
        //     foreach ($items as $item) {
            //         $selected = ($options['dropdown1'] == $item) ? 'selected="selected"' : '';
            //         echo "<option value='$item' $selected>$item</option>";
            //     }
            //     echo '</select>';
            // }

            // function setting_textarea_fn()
            // {
                //     $options = get_option('plugin_options');
                //     echo "<textarea id='plugin_textarea_string' name='plugin_options[text_area]' rows='7' cols='50' type='textarea'>{$options['text_area']}</textarea>";
                // }

                // function setting_string_fn()
                // {
                    //     $options = get_option('plugin_options');
                    //     echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
                    // }

                    // function setting_pass_fn()
                    // {
                        //     $options = get_option('plugin_options');
                        //     echo "<input id='plugin_text_pass' name='plugin_options[pass_string]' size='40' type='password' value='{$options['pass_string']}' />";
                        // }

                        // function setting_chk1_fn()
                        // {
                            //     $options = get_option('plugin_options');
                            //     if ($options['checkbox1']) {
                                //         $checked = ' checked="checked" ';
                                //     }
                                //     echo '<input ' . $checked . " id='plugin_chk1' name='plugin_options[checkbox1]' type='checkbox' />";
                                // }
