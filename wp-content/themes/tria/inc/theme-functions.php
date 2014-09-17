<?php

function tria_custom_add_query_vars($aVars) {
  $aVars[] = "blog_order";
  $aVars[] = "blog_dir";
  $aVars[] = "drid";
  $aVars[] = "formid";
  return $aVars;
}
add_filter('query_vars', 'tria_custom_add_query_vars');


function tria_custom_blog_posts_order( $query ){
    if( $query->is_home() ){

      $blog_order = get_query_var('blog_order');
      $blog_dir   = get_query_var('blog_dir');

      $blog_order = (!empty($blog_order)) ? $blog_order : 'date' ;
      switch ($blog_order) {
        case 'date':
          break;
        case 'alpha':
          $query->set('orderby', 'title');
          $query->set('order', 'ASC');
          break;
        default:
          break;
      }
      if (!empty($blog_dir)) {
        $query->set('order', $blog_dir);
      }

    }
}
add_action('pre_get_posts', 'tria_custom_blog_posts_order');


function wen_get_speaker_info($speaker){

  $output = array_merge($speaker, array());
  $output['speaker_gravatar']    = '';
  $output['speaker_meta']        = '';
  $output['speaker_profile_url'] = '';

  if (!empty($speaker['speaker_id'])) {
    $output['speaker_full_name'] = $speaker['speaker_id']->post_title;
    $output['speaker_profile_url'] = esc_url( get_permalink( $speaker['speaker_id']->ID ) );
  }
  else{
    $output['speaker_full_name'] = $speaker['speaker_name'];
  }

  if ( 1 == $speaker['use_default_picture'] ) {
    if (!empty($speaker['speaker_id'])) {
      $image = get_field( 'dr_image', $speaker['speaker_id']->ID );
      if (!empty($image)) {
        $output['speaker_gravatar'] = $image['url'];
      }
      $output['speaker_meta'] = get_fields($speaker['speaker_id']->ID);
    }
  }
  else{
    if (!empty($speaker['speaker_picture'])) {
      $output['speaker_gravatar'] = $speaker['speaker_picture']['url'];
    }
  }
  return $output;
}
//////////////////////////////////////
add_filter( 'nav_menu_link_attributes', 'tria_filter_menu_class', 10, 3 );
function tria_filter_menu_class( $atts, $item, $args ) {

  if ( isset( $args->theme_location ) ){
    if ( 'primary' !== $args->theme_location ){
      return $atts;
    }
  }
  if ( 1 == $item->menu_order ) {
    $atts['class'] = 'noborderleft';
  }
  return $atts;
}


class tria_walker_nav_menu extends Walker_Nav_Menu{

  function end_el(&$output, $item, $depth, $args) {

    global $post;

    $our_page_id = wen_get_option('cp_services') ;

    if( $our_page_id == $item->object_id ){

      $output .= '</li>';
      // wrapper start
      $output .= '<div id="hover1" data-dropdown-content class="medium f-dropdown content row" >';

        $custom_query_args = array(
          'post_type' => 'service',
          'posts_per_page' => -1,
          'tax_query' => array(
              array(
                'taxonomy' => 'service_type',
                'field'    => 'slug',
                'terms'    => 'tria-service',
              ),
            ),
          );
        $services = get_posts($custom_query_args);
        if ( ! empty( $services ) ) {
          $output .= '<div class="large-6 columns"><h3 class="menuhead">Services</h3><hr><div class="bignavul"><ul>';
          foreach ($services as $key => $serv) {
            $post = $serv;
            setup_postdata( $serv );
            $output .= '<li><a href="'.get_permalink( get_the_ID() ).'">'.get_the_title().'</a></li>';
          }
          $output .= '</ul></div></div>';
        }
        wp_reset_postdata();
        $custom_query_args = array(
          'post_type' => 'service',
          'posts_per_page' => -1,
          'tax_query' => array(
              array(
                'taxonomy' => 'service_type',
                'field'    => 'slug',
                'terms'    => 'tria-unique-program',
              ),
            ),
          );
        $services = get_posts($custom_query_args);
        if ( ! empty( $services ) ) {
          $output .= ' <div class="large-6 columns"><h3 class="menuhead-2">Unique Programs at Tria</h3><hr><div class="bignavul"><ul>';
          foreach ($services as $key => $serv) {
            $post = $serv;
            setup_postdata( $serv );
            $output .= '<li><a href="'.get_permalink( get_the_ID() ).'">'.get_the_title().'</a></li>';
          }
          $output .= '</ul></div></div>';
        }
        wp_reset_postdata();

      // wrapper close
      $output .= '<div class="clearfix"></div><a href="http://tria.snapagency.com/acute-injury-clinic/"><img src="http://tria.snapagency.com/wp-content/uploads/2014/09/dropdown-image-acute.jpg"></a></div>'; //end #drop1 div

     }
     else{
       $output .= "</li>\n";

     }
  }
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

    $our_page_id = wen_get_option('cp_services') ;

    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    /**
     * Filter the CSS class(es) applied to a menu item's <li>.
     *
     * @since 3.0.0
     *
     * @see wp_nav_menu()
     *
     * @param array  $classes The CSS classes that are applied to the menu item's <li>.
     * @param object $item    The current menu item.
     * @param array  $args    An array of wp_nav_menu() arguments.
     */
    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    /**
     * Filter the ID applied to a menu item's <li>.
     *
     * @since 3.0.1
     *
     * @see wp_nav_menu()
     *
     * @param string $menu_id The ID that is applied to the menu item's <li>.
     * @param object $item    The current menu item.
     * @param array  $args    An array of wp_nav_menu() arguments.
     */
    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    $output .= $indent . '<li' . $id . $class_names .'>';

    $atts = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
    $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
    $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

    /**
     * Filter the HTML attributes applied to a menu item's <a>.
     *
     * @since 3.6.0
     *
     * @see wp_nav_menu()
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
     *
     *     @type string $title  Title attribute.
     *     @type string $target Target attribute.
     *     @type string $rel    The rel attribute.
     *     @type string $href   The href attribute.
     * }
     * @param object $item The current menu item.
     * @param array  $args An array of wp_nav_menu() arguments.
     */
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
    if( $our_page_id == $item->object_id ){
      $atts['data-dropdown'] = 'hover1';
      $atts['data-options']='is_hover:true';


    }


    $attributes = '';
    foreach ( $atts as $attr => $value ) {
      if ( ! empty( $value ) ) {
        $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    /** This filter is documented in wp-includes/post-template.php */
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    /**
     * Filter a menu item's starting output.
     *
     * The menu item's starting output only includes $args->before, the opening <a>,
     * the menu item's title, the closing </a>, and $args->after. Currently, there is
     * no filter for modifying the opening and closing <li> for a menu item.
     *
     * @since 3.0.0
     *
     * @see wp_nav_menu()
     *
     * @param string $item_output The menu item's starting HTML output.
     * @param object $item        Menu item data object.
     * @param int    $depth       Depth of menu item. Used for padding.
     * @param array  $args        An array of wp_nav_menu() arguments.
     */
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

}

// add_filter( 'wp_nav_menu_objects', 'tria_test_wp_nav_menu_items', 10, 2 );

function tria_test_wp_nav_menu_items( $items, $args ){

  if ($args->theme_location == 'primary') {

    // nspre( $items,'ooo', false, false );

    foreach ($items as $key => $menu_item) {

      if ($menu_item->object_id == 174 ) {
        nspre($menu_item,'itest', false, false);
      }

    }


    // $items .= '<span>rrr</span>';
    // echo 'tttend';

  }
  return $items;


}
////////////////////////////////////
function tria_get_registration_url($post_meta, $meta_key = 'register_url' ){

  // nspre($post_meta,'p');

  $register_url = 'javascript:void(0)';

  if (!empty($post_meta[ $meta_key ])) {

    $cp_registration_form = wen_get_option('cp_registration_form');

    if (!empty($cp_registration_form)) {
      $args = array(
        'formid' => $post_meta[ $meta_key ]->ID
        );
      $register_url = add_query_arg( $args, get_permalink( $cp_registration_form ) );
    }

  }

  return $register_url;

}

// hook the translation filters
add_filter(  'gettext',  'tria_change_comment_to_feedback'  );
add_filter(  'ngettext',  'tria_change_comment_to_feedback'  );

function tria_change_comment_to_feedback( $translated ) {
     $translated = str_ireplace(  'Comment',  'Feedback',  $translated );  // ireplace is PHP5 only
     $translated = str_ireplace(  'comments',  'feedbacks',  $translated );
     $translated = str_ireplace(  'comment',  'feedback',  $translated );
     return $translated;
}

// Extra column in Provider Starts
add_filter('manage_edit-doctor_columns', 'tria_custom_add_new_columns_provider');
function tria_custom_add_new_columns_provider( $columns ){
    $columns['featured_provider'] = 'Featured';
    return $columns;
}

add_action('manage_doctor_posts_custom_column', 'custom_manage_new_columns', 10, 2);
function custom_manage_new_columns( $column_name, $id ){
    if ('featured_provider'==$column_name){
     $feature_doctor_on_homepage = get_field( 'feature_doctor_on_homepage', $id );
     $classes = array(  'btn-featured-doctor' );
     if (1 == $feature_doctor_on_homepage) {
       $classes[] = 'selected';
     }
     echo '<input type="checkbox" class="'.implode( ' ', $classes ).'" id="btn-doctor-featured_'.$id.'" ';
     checked($feature_doctor_on_homepage,1);
     echo '/>';


    }
}

add_action( 'wp_ajax_tria_doctor_featured', 'tria_ajax_featured_provider' );
function tria_ajax_featured_provider(){
  $ns_featured = $_POST['ns_featured'];
  $id = (int)$_POST['post'];
  if( !empty( $id ) && $ns_featured !== NULL ) {
      if ( $ns_featured == 'no' ){
          update_post_meta( $id, "feature_doctor_on_homepage", '0' );
      }
      else {
          update_post_meta( $id, "feature_doctor_on_homepage", '1' );
      }
  }
  wp_send_json_success();

}

// Extra column in Provider ENDS
add_action( 'admin_menu', 'tria_custom_remove_admin_menus' );
function tria_custom_remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}

add_action( 'admin_head', 'tria_custom_css_in_admin_head' );
function tria_custom_css_in_admin_head() {

  $screen = get_current_screen();
  // nspre($_REQUEST);
  // nspre($screen);
  ?>

  <?php if ( 'edit-bodypart' == $screen->id && empty($_REQUEST['tag_ID']) ): ?>
  <style>
    body.taxonomy-bodypart .form-wrap {
      display: none;
    }
  </style>

  <?php endif ?>
  <?php if ( 'doctor' == $screen->id ): ?>
  <style>
    #doc_interestdiv {
      width:47%;
      display: inline-block;
      vertical-align: top;
    }
    #doc_ptypediv {
      width:47%;
      display: inline-block;
      vertical-align: top;
    }

    #acf_after_title-sortables .acf_postbox{
      max-width: 47%;
      width:100%;
      display: inline-block;
      vertical-align: top;
    }

  </style>

  <?php endif ?>

  <?php

}