<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
$collector = array();
$_REQUEST['additional_taxes'] = $additional_taxes;
$_REQUEST['hide_terms_count_txt'] = isset($this->settings['hide_terms_count_txt']) ? $this->settings['hide_terms_count_txt'] : 0;
$woof_hide_dynamic_empty_pos = false;
if (!function_exists('woof_draw_mselect_childs'))
{

    function woof_draw_mselect_childs(&$collector, $taxonomy_info, $tax_slug, $childs, $level, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos)
    {
        global $WOOF;
        $request = $WOOF->get_request_data();
$woof_hide_dynamic_empty_pos = false;

        $current_request = array();
        if ($WOOF->is_isset_in_request_data($tax_slug))
        {
            $current_request = $request[$tax_slug];
            $current_request = explode(',', urldecode($current_request));
        }

        //excluding hidden terms
        $hidden_terms = array();
        if (isset($WOOF->settings['excluded_terms'][$tax_slug]))
        {
            $hidden_terms = explode(',', $WOOF->settings['excluded_terms'][$tax_slug]);
        }
        
        $childs = apply_filters('woof_sort_terms_before_out', $childs, 'mselect');
        ?>
        <?php foreach ($childs as $term) : ?>
            <?php
            $count_string = "";
            $count = 0;
            if (!in_array($term['slug'], $current_request))
            {
                if ($show_count)
                {
                    if ($show_count_dynamic)
                    {
                        $count = $WOOF->dynamic_count($term, 'mselect', $_REQUEST['additional_taxes']);
                    } else
                    {
                        $count = $term['count'];
                    }
                    $count_string = '(' . $count . ')';
                }
                //+++
                if ($hide_dynamic_empty_pos AND $count == 0)
                {
                    continue;
                }
            }

            if ($_REQUEST['hide_terms_count_txt'])
            {
                $count_string = "";
            }

            //excluding hidden terms
            if (in_array($term['term_id'], $hidden_terms))
            {
                continue;
            }
            ?>
            <option <?php if ($show_count AND $count == 0 AND ! in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo $term['slug'] ?>" <?php echo selected(in_array($term['slug'], $current_request)) ?>><?php echo str_repeat('&nbsp;&nbsp;&nbsp;', $level) ?><?php
                if (has_filter('woof_before_term_name'))
                    echo apply_filters('woof_before_term_name', $term, $taxonomy_info);
                else
                    echo $term['name'];
                ?> <?php echo $count_string ?></option>
            <?php
            if (!isset($collector[$tax_slug]))
            {
                $collector[$tax_slug] = array();
            }
            $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug']);

            if (!empty($term['childs']))
            {
                woof_draw_mselect_childs($collector, $taxonomy_info, $tax_slug, $term['childs'], $level + 1, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
            }
            ?>
        <?php endforeach; ?>
        <?php
    }

}
?>
<select class="woof_mselect woof_mselect_<?php echo $tax_slug ?>" data-placeholder="<?php echo WOOF_HELPER::wpml_translate($taxonomy_info) ?>" multiple="" size="<?php echo($this->is_woof_use_chosen() ? 1 : '') ?>" name="<?php echo $tax_slug ?>">
    <option value="0"></option>
    <?php
    $woof_tax_values = array();
    $current_request = array();
    $request = $this->get_request_data();
    $shown_options_tags = 0;
    if ($this->is_isset_in_request_data($tax_slug))
    {
        $current_request = $request[$tax_slug];
        $current_request = explode(',', urldecode($current_request));
    }

    //excluding hidden terms
    $hidden_terms = array();
    if (isset($this->settings['excluded_terms'][$tax_slug]))
    {
        $hidden_terms = explode(',', $this->settings['excluded_terms'][$tax_slug]);
    }
    
    $terms = apply_filters('woof_sort_terms_before_out', $terms, 'mselect');
    ?>
    <?php foreach ($terms as $term) : ?>
        <?php
        $count_string = "";
        $count = 0;
        if (!in_array($term['slug'], $current_request))
        {
            if ($show_count)
            {
                if ($show_count_dynamic)
                {
                    $count = $this->dynamic_count($term, 'mselect', $_REQUEST['additional_taxes']);
                } else
                {
                    $count = $term['count'];
                }
                $count_string = '(' . $count . ')';
            }
            //+++
            if ($hide_dynamic_empty_pos AND $count == 0)
            {
                continue;
            }
        }

        if ($_REQUEST['hide_terms_count_txt'])
        {
            $count_string = "";
        }

        if (in_array($term['term_id'], $hidden_terms))
        {
            continue;
        }
        ?>
        <option <?php if ($show_count AND $count == 0 AND ! in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo $term['slug'] ?>" <?php echo selected(in_array($term['slug'], $current_request)) ?>><?php
            if (has_filter('woof_before_term_name'))
                echo apply_filters('woof_before_term_name', $term, $taxonomy_info);
            else
                echo $term['name'];
            ?> <?php echo $count_string ?></option>
        <?php
        if (!isset($collector[$tax_slug]))
        {
            $collector[$tax_slug] = array();
        }

        $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug']);

        //+++

        if (!empty($term['childs']))
        {
            woof_draw_mselect_childs($collector, $taxonomy_info, $tax_slug, $term['childs'], 1, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
        }

        $shown_options_tags++;
        ?>
    <?php endforeach; ?>
</select>
<?php if ($shown_options_tags == 0): ?>
    <style type="text/css">
        .woof_container_<?php echo $tax_slug ?>{
            display:none;
        }
    </style>
<?php endif; ?>

<?php
//this is for woof_products_top_panel
if (!empty($collector))
{
    foreach ($collector as $ts => $values)
    {
        if (!empty($values))
        {
            foreach ($values as $value)
            {
                ?>
                <input type="hidden" value="<?php echo $value['name'] ?>" class="woof_n_<?php echo $ts ?>_<?php echo $value['slug'] ?>" />
                <?php
            }
        }
    }
}

//we need it only here, and keep it in $_REQUEST for using in function for child items
unset($_REQUEST['additional_taxes']);
