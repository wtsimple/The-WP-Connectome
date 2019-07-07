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
    /**
     * Object to handle the graph data
     * @var SiteGraph
     */
    private $siteGraph;
    /**
     * Tells whether the elements in the siteGraph have being prepared
     * @var boolean
     */
    private $areElementsPrepared = false;

    /**
     * Tells whether the option array has been cached
     * @var boolean
     */
    private $areOptionsSet = false;

    /**
     * Caches the options
     * @var array
     */
    private $options = [];
    /**
     * Tells whether the options have been saved before
     * @var boolean
     */
    private $haveOptionsBeenSaved = true;

    public function __construct()
    {
        $this->optionGroup = OptionStorage::get_option('OPTIONS_NAME');
        $this->page = OptionStorage::get_option('OPTIONS_NAME');
        $this->postTypes = $this->get_post_types();
        $this->optionHandler = new SettingsOptionHandler();
        $this->siteGraph = new SiteGraph();
        $max = get_options_max($this->optionGroup);
        if (empty($max)) {
            $this->haveOptionsBeenSaved = false;
        } else {
            $this->haveOptionsBeenSaved = true;
        }
    }

    /**
     * Gets the graph elements ready to be used in the option page
     * @return void
     */
    public function prepare_elements()
    {
        if (!$this->areElementsPrepared) {
            $removeDisabled = false;
            $this->siteGraph->prepare_all_elements($removeDisabled);
            $this->siteGraph->build_graph();
        }
        $this->areElementsPrepared = true;
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
        $this->prepare_elements();

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
                ['type' => $type, 'index' => $index + 2, 'post_types' => true]
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
        $supraType = $this->get_supra_type($args);
        $amountField = $this->optionHandler->generate_name(['types', $supraType, $type['name'], 'max']);
        $amount = $this->get_field($amountField, 10);
        $colorField = $this->optionHandler->generate_name(['types', $supraType, $type['name'], 'color']);
        $color = $this->get_field($colorField, $colorPalette[$index]);

        $getAll = true;
        $elements = $this->siteGraph->graphData->get_elements_by_id($type['name'], null, $getAll); ?>
<span> Color: </span>
<input disabled id='<?php echo $colorField ?>'
    name='<?php echo  $this->optionGroup . '[' . $colorField . ']'?>'
    type='color' value='<?php echo  $color ?>' />
<span style='margin-left:10px;'> Max: </span>
<input style='max-width:80px;' id='<?php  echo $amountField ?>'
    name='<?php echo $this->optionGroup . '[' . $amountField . ']'?>'
    type='number' min='-1' value='<?php  echo $amount ?>' />
<span style='margin-left:10px;'>Total: <span class='elements-amount'><?php echo $type['amount'] ?></span></span>
<div class="individual-element-selection">
    <div class="individual-element-selectors" style="display:none;">
        <?php foreach ($elements as $element):
        $checkName = $this->optionHandler->generate_name(['types', $supraType, $type['name'], 'elements', $element['id'], 'isActive']);
        if ($this->get_field($checkName, true) === 'on' or !$this->haveOptionsBeenSaved) {
            $checked = ' checked="checked" ';
        } else {
            $checked = '';
        } ?>
        <span class="element-checkbox-container">
            <input type="checkbox" <?php echo $checked ?>
            name="<?php echo $this->optionGroup . '[' . $checkName . ']' ?>"
            id="<?php echo $checkName ?>" />
            <?php echo $element['label']; ?>
        </span>
        <?php endforeach ?>

    </div>
    <div>
        <button type="button" class="element-selection-show-button">Select Individual Elements</button>
    </div>
</div>
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

    jQuery(".element-selection-show-button").click(
        function(event) {
            let target = jQuery(event.target);
            let container = target.parent().parent().find(".individual-element-selectors");

            container.toggle(500);

        }
    );
</script>

<style>
    .element-checkbox-container {
        margin-right: 10px;
        display: inline-block;
    }
</style>

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
        // Do some caching to not ask over and over for the options
        if (!$this->areOptionsSet) {
            $this->options = get_option($this->optionGroup);
        }
        return $this->options;
    }

    /**
     * Gets an element under the options array with a given name.
     * If the element isn't set, returns the default
     *
     * @param string $name the array key within the options array
     * @param mixed $default value to return if the field isn't set
     * @return void
     */
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

    public function get_supra_type($args)
    {
        $postSupra = OptionStorage::get_option('POST_TYPE_SUPRA');
        return isset($args['post_types']) ? $postSupra : '';
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
