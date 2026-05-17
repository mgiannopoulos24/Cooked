<?php
/**
 * Cooked Allergens
 *
 * @package     Cooked
 * @subpackage  Allergens
 * @since       1.15.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cooked_Allergens Class
 *
 * Handles allergen display on recipe cards and single recipe views.
 *
 * @since 1.15.0
 */
class Cooked_Allergens {

    /**
     * Initialize allergen hooks.
     *
     * @since 1.15.0
     */
    public static function init() {
        // Admin: Add allergen meta box to sidebar
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_meta_box' ] );

        // Admin: Add shortcode documentation
        add_action( 'cooked_recipe_shortcodes_after', [ __CLASS__, 'shortcode_documentation' ] );

        // Frontend: Display allergens on recipe cards
        add_action( 'cooked_recipe_grid_after_name', [ __CLASS__, 'render_from_recipe' ], 10 );

        // Frontend: Display allergens on single recipe (via shortcode)
        add_shortcode( 'cooked-allergens', [ __CLASS__, 'shortcode' ] );
    }

    /**
     * Register the allergens meta box for the sidebar.
     *
     * @since 1.15.0
     */
    public static function register_meta_box() {
        add_meta_box(
            'cooked_allergens',
            __( 'Allergens', 'cooked' ),
            [ __CLASS__, 'render_meta_box' ],
            'cp_recipe',
            'side',
            'default'
        );
    }

    /**
     * Render the allergens meta box content.
     *
     * @since 1.15.0
     * @param WP_Post $post The post object.
     */
    public static function render_meta_box( $post ) {
        $recipe_settings = get_post_meta( $post->ID, '_recipe_settings', true );
        $selected = isset( $recipe_settings['allergens'] ) && is_array( $recipe_settings['allergens'] )
            ? $recipe_settings['allergens']
            : [];
        $allergens = self::get_allergens();
        ?>
        <div class="cooked-allergens-checkboxes">
            <?php foreach ( $allergens as $key => $allergen ) : ?>
                <label class="cooked-allergen-checkbox">
                    <input type="checkbox"
                        name="_recipe_settings[allergens][]"
                        value="<?php echo esc_attr( $key ); ?>"
                        <?php checked( in_array( $key, $selected, true ) ); ?>>
                    <span class="cooked-allergen-badge" style="background-color:<?php echo esc_attr( $allergen['color'] ); ?>;"><?php echo esc_html( $allergen['abbr'] ); ?></span>
                    <span class="cooked-allergen-label"><?php echo esc_html( $allergen['label'] ); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Get the list of FDA major allergens.
     *
     * @since 1.15.0
     * @return array
     */
    public static function get_allergens() {
        return apply_filters( 'cooked_allergens', [
            'milk' => [
                'label' => __( 'Milk', 'cooked' ),
                'abbr'  => 'M',
                'color' => '#6b9ac4',
            ],
            'eggs' => [
                'label' => __( 'Eggs', 'cooked' ),
                'abbr'  => 'E',
                'color' => '#d4a84b',
            ],
            'fish' => [
                'label' => __( 'Fish', 'cooked' ),
                'abbr'  => 'F',
                'color' => '#5a9a9a',
            ],
            'shellfish' => [
                'label' => __( 'Shellfish', 'cooked' ),
                'abbr'  => 'SF',
                'color' => '#c97c7c',
            ],
            'tree_nuts' => [
                'label' => __( 'Tree Nuts', 'cooked' ),
                'abbr'  => 'TN',
                'color' => '#8b7355',
            ],
            'peanuts' => [
                'label' => __( 'Peanuts', 'cooked' ),
                'abbr'  => 'P',
                'color' => '#c4a35a',
            ],
            'wheat' => [
                'label' => __( 'Wheat', 'cooked' ),
                'abbr'  => 'W',
                'color' => '#c9a227',
            ],
            'soybeans' => [
                'label' => __( 'Soybeans', 'cooked' ),
                'abbr'  => 'SB',
                'color' => '#7a9a5a',
            ],
            'sesame' => [
                'label' => __( 'Sesame', 'cooked' ),
                'abbr'  => 'SE',
                'color' => '#a89a8a',
            ],
        ] );
    }

    /**
     * Render allergen badges from recipe array (used in hooks).
     *
     * @since 1.15.0
     * @param array $recipe Recipe data array with 'id' key.
     */
    public static function render_from_recipe( $recipe ) {
        if ( empty( $recipe['id'] ) ) {
            return;
        }

        $recipe_settings = Cooked_Recipes::get_settings( $recipe['id'] );
        echo self::render( $recipe_settings );
    }

    /**
     * Render allergen badges HTML.
     *
     * @since 1.15.0
     * @param array $recipe_settings Recipe settings array.
     * @return string HTML output.
     */
    public static function render( $recipe_settings ) {
        if ( empty( $recipe_settings['allergens'] ) || ! is_array( $recipe_settings['allergens'] ) ) {
            return '';
        }

        $allergens = self::get_allergens();
        $selected = $recipe_settings['allergens'];
        $output = '<span class="cooked-allergens">';

        foreach ( $selected as $key ) {
            if ( ! isset( $allergens[ $key ] ) ) {
                continue;
            }

            $allergen = $allergens[ $key ];
            $output .= sprintf(
                '<span class="cooked-allergen cooked-allergen-%s" title="%s" style="background-color:%s;">%s</span>',
                esc_attr( $key ),
                /* translators: %s: allergen name */
                esc_attr( sprintf( __( 'Contains %s', 'cooked' ), $allergen['label'] ) ),
                esc_attr( $allergen['color'] ),
                esc_html( $allergen['abbr'] )
            );
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Shortcode handler for displaying allergens.
     *
     * Usage: [cooked-allergens] or [cooked-allergens id="123"]
     *
     * @since 1.15.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public static function shortcode( $atts ) {
        global $post;

        $atts = shortcode_atts( [
            'id' => $post ? $post->ID : 0,
        ], $atts, 'cooked-allergens' );

        $recipe_id = intval( $atts['id'] );
        if ( ! $recipe_id ) {
            return '';
        }

        $recipe_settings = Cooked_Recipes::get_settings( $recipe_id );
        return self::render( $recipe_settings );
    }

    /**
     * Render shortcode documentation in the Shortcodes tab.
     *
     * @since 1.15.0
     */
    public static function shortcode_documentation() {
        ?>
        <hr class="cooked-hr">

        <!-- [cooked-allergens] -->
        <div class="cooked-clearfix">

            <div class="cooked-setting-column-23">

                <h3 class="cooked-settings-title cooked-bm-0"><?php _e( 'Allergens', 'cooked' ); ?></h3>
                <p class="cooked-bm-10"><?php _e( 'This will display the allergen badges for this recipe, if any are selected.', 'cooked' ); ?></p>
                <div class="cooked-bm-20 cooked-block">
                    <input class='cooked-shortcode-field' type='text' readonly value='[cooked-allergens]' />
                </div>

            </div>

            <div class="cooked-setting-column-13">
                <p class="cooked-bm-10 cooked-tm-10"><strong class="cooked-heading"><?php _e( 'Available Variables', 'cooked' ); ?></strong></p>
                <p class="cooked-bm-10">
                    <em><?php _e( 'None', 'cooked' ); ?></em>
                </p>
            </div>

        </div>
        <?php
    }

}

// Initialize
add_action( 'init', [ 'Cooked_Allergens', 'init' ] );
