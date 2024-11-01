<?php
  /* Multiplugin functions */
  if(!function_exists('wp_get_current_user'))
    include(ABSPATH . "wp-includes/pluggable.php");

  /* Красивая функция вывода масивов */
  if (!function_exists('prr')){ function prr($str) { echo "<pre>"; print_r($str); echo "</pre>\r\n"; } }

  if( isset($_REQUEST['page']) && $_REQUEST['page'] == 'yummi' && !function_exists('yummi_register_settings') || isset($_REQUEST['page']) && $_REQUEST['page'] == 'wpas' && !function_exists('yummi_register_settings') ){ /* Filter pages */
    add_action( 'admin_init', 'yummi_register_settings' );
    function yummi_register_settings() {
      $url = plugin_dir_url( __FILE__ );
      //register_setting( 'wpas_admin_menu', 'wpas', 'wpas_validate_options' );
      wp_enqueue_style( 'yummi-style', $url . '/css/admin_style.min.css' );
      wp_enqueue_style( 'yummi-hint', $url . '/css/hint.min.css' );

      if ( !current_user_can('manage_options') )
        wp_die(__('Sorry, you are not allowed to install plugins on this site.'));
    }
  }

  add_action('admin_menu', 'wpas_admin_menu');
  function wpas_admin_menu() {
    if( empty( $GLOBALS['admin_page_hooks']['yummi']) )
      add_menu_page( 'yummi', 'Yummi Plugins', 'manage_options', 'yummi', 'yummi_plugins_wpas', WPAS_URL.'/includes/img/dashicons-yummi.png' );

    /*add_submenu_page( parent_slug, page_title, menu_title, rights(user can manage_options), menu_slug, function ); */
    add_submenu_page('yummi', __('Admin Style', 'wpas'), __('Admin Style', 'wpas'), 'manage_options', 'wpas', 'wpas_options_page');
  }

  function yummi_plugins_wpas() { if(!function_exists('yummi_plugins')) include_once( WPAS_PATH . '/includes/yummi-plugins.php' ); }
  /* /Multiplugin functions */

  // Function to generate options page
  function wpas_options_page() {
  	global $wpas;

    $mcss = !empty($_POST['mcss']) ? $_POST['mcss'] : '';

    $wpas = array(
        'style' => !empty($_POST['style']) ? $_POST['style'] : ''
       ,'mcss' => !empty($_POST['mcss']) ? $_POST['mcss'] : ''
       ,'css' => !empty($_POST['css']) ? $_POST['css'] : '' //textarea .ab-item { display: none; }
    );
    //update_option("wpas", $wpas);

    #Get option values
    $wpas = get_option( 'wpas', $wpas );

    // prr($wpas);

    #Get new updated option values, and save them
    if( @$_POST['action'] == 'update' ) {

      check_admin_referer('update-options-wpas');

      $wpas = array( //(int)$_POST[wpas] //sanitize_text_field($_POST[wpas])
        //Валидация данных https://codex.wordpress.org/%D0%92%D0%B0%D0%BB%D0%B8%D0%B4%D0%B0%D1%86%D0%B8%D1%8F_%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D1%85
         'style' => !empty($_POST['style']) ? $_POST['style'] : ''
        ,'mcss' => !empty($_POST['mcss']) ? $_POST['mcss'] : ''
        ,'css' => !empty($_POST['css']) ? $_POST['css'] : '' //textarea .ab-item { display: none; }
      );
      update_option("wpas", $wpas);
      echo '<div id="message" class="updated fade"><p><strong>'.__('Settings saved.').'</strong></p></div>'; //<script type="text/javascript">document.location.reload(true);</script>
    }

    // function wpas_validate_options( $input ) {
    // 	global $qt_options;
    //
    // 	$settings = get_option( 'qt_options', $qt_options );
    //
    // 	$input['w_img_radius'] = wp_filter_nohtml_kses( $input['w_img_radius'] );
    //   $input['w_img_size'] = wp_filter_nohtml_kses( $input['w_img_size'] );
    //   $input['s_img_radius'] = wp_filter_nohtml_kses( $input['s_img_radius'] );
    //   $input['s_img_size'] = wp_filter_nohtml_kses( $input['s_img_size'] );
    //
    // 	$input['css'] = wp_filter_post_kses( $input['css'] );
    //
    // 	return $input;
    // }

    global $wp_version;
    $isOldWP = floatval($wp_version) < 2.5;

    $beforeRow = $isOldWP ? "<p>" : '<tr valign="top"><th scope="row">';
    $betweenRow = $isOldWP ? "" : '</th><td>';
    $afterRow = $isOldWP ? "</p>" : '</td><tr>';

    //prr($_POST);
    // if ( false !== $_REQUEST['updated'] ) echo '<div class="updated fade"><p><strong>'.__( 'Options saved' ).'</strong></p></div>'; // If the form has just been submitted, this shows the notification ?>

  	<div class="wrap">

      <?php screen_icon(); echo "<h1>" . __('WP Admin Style', 'wpas') .' '. __( 'Settings' ) . "</h1>"; ?>
      <div style='float:right;margin-top: -27px;'><span style="font-size:1.3em">&starf;</span> <a href="https://wordpress.org/support/plugin/wp-admin-style/reviews/#new-post" target="_blank"><?php _e('Rate')?></a> &ensp; ❤ <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLHFMF373Z9GG&source=url" target="_blank"><?php _e('Donate', 'yabp')?></a></div>

    	<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>">

      	<?php
        if(function_exists('wp_nonce_field'))
          wp_nonce_field('update-options-wpas');

        if(!$isOldWP)
          echo "<table class='form-table'>"; ?>

        <?php
        function endKey($array){
          end($array);
          return key($array);
        }
        $i = is_array($wpas['mcss']) ? endKey($wpas['mcss']) : 0;

        if( is_array($wpas['mcss']) && $wpas['snow_type'] == 'snow' ){
          foreach ($wpas['mcss'] as $key => $snowflake): ?>

            <?php echo $beforeRow ?>
              <label for="mcss[<?php echo $key?>]"><?php echo __('Class', 'wpas').' '.$key?></label>
            <?php echo $betweenRow ?>
              <input id="mcss[<?php echo $key?>]" name="mcss[<?php echo $key?>]" type="text" value="<?php echo $snowflake ?>" /> <span class="del">✖</span>
            <?php echo $afterRow ?>

          <?php endforeach;
        }?>

        <div id="mcss"></div>

        <?php echo $beforeRow ?>
        <?php //echo '<span id="add">✚ '.__('Add one', 'wpas').'</span>' // if( $wpas['snow_type'] == 'snow' ) echo '<span id="add">✚ '.__('Add one', 'wpas').'</span>'  ?>

        <?php echo $betweenRow ?>
          <script type="text/javascript">
          var i=<?php echo $i?>;
          jQuery('#add').on('click', function(){
            i++;
            jQuery('<?php echo $beforeRow ?><label for="mcss['+i+']"><?php _e('Style', 'wpas')?> '+i+'</label><?php echo $betweenRow ?><input id="mcss['+i+']" name="mcss['+i+']" type="text" value=""/> <span class="del">✖</span><?php echo $afterRow ?>').appendTo( "#mcss" );
          });
          jQuery('.del').on('click', function(){
            jQuery(this).parent().parent().remove();
          });</script>
        <?php echo $afterRow ?>

        <?php echo $beforeRow ?>
          <label for="style"><?php _e('Style', 'wpas')?></label>
        <?php echo $betweenRow ?>
          <select name="style" id="style">
            <option value="default" <?php if($wpas['style'] == 'default') echo ' selected="selected"' ?> class='none'>- <?php _e('Default')?> -</option>
            <option value="yummi" <?php if($wpas['style'] == 'yummi') echo ' selected="selected"' ?>><?php _e('Black with Pink')?></option>
          </select>
        <?php echo $afterRow ?>

        <?php echo $beforeRow ?>
          <?php _e('Custom Css', 'wpas')?></label>
        <?php echo $betweenRow ?>
           <textarea id="css" name="css" rows="5" cols="30"><?php echo stripslashes($wpas['css']); ?></textarea>
        <?php echo $afterRow ?>

        <?php if(!$isOldWP)
            echo "</table>"; ?>

        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="wpas" />

        <p class="submit">
          <input type="submit" name="Submit" class="button-primary collectsnow" value="<?php _e('Save Changes') ?>" />
        </p>
        <span id="log"></span>

    	</form>

  	</div>

    <!-- <h3><?php _e('Installation codes', 'wpas') ?>:</h3>
    <p>
      <h4>[add_bookmark]</h4>
      <strong><?php _e('Extended', 'wpas') ?></strong>: [wpas post_types=post,recipes post_types_num=4 customnames=intro customfields=intro_name]<br/>
      <.?php _e('Where \'post_types\' can be all your Post Type, \'post_types_num\' is number of posts in Post Types to show, \'customnames\' can contain custom fields names, \'customfields\' can contain custom fields.', 'wpas') ?><br/>

      <h4>[booknarks]</h4>

      <small><?php _e('Put one of this shortcode to your pages.', 'wpas') ?></small>
    </p>
    <em>- <?php _e('or','wpas'); ?> -</em>
    <p>
      <h4>&lt;?php echo do_shortcode('[add_bookmark]') ?&gt;</h4>
      <h4>&lt;?php echo do_shortcode('[booknarks]') ?&gt;</h4>
      <small><?php _e('Put one of this code to your template files', 'wpas') ?>: <?php _e('Appearance') ?> &gt; <?php _e('Editor') ?></small>
    </p> -->

  	<?php
  }
