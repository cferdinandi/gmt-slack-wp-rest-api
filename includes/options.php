<?php

/**
 * Theme Options v1.1.0
 * Adjust theme settings from the admin dashboard.
 * Find and replace `gmt_slack_api` with your own namepspacing.
 *
 * Created by Michael Fields.
 * https://gist.github.com/mfields/4678999
 *
 * Forked by Chris Ferdinandi
 * http://gomakethings.com
 *
 * Free to use under the MIT License.
 * http://gomakethings.com/mit/
 */


	/**
	 * Theme Options Fields
	 * Each option field requires its own uniquely named function. Select options and radio buttons also require an additional uniquely named function with an array of option choices.
	 */

	function gmt_slack_api_settings_field_domain_name() {
		$options = gmt_slack_api_get_theme_options();
		?>
		<input type="text" name="gmt_slack_api_theme_options[domain_name]" class="large-text" id="domain_name" value="<?php echo esc_attr( $options['domain_name'] ); ?>" />
		<label class="description" for="domain_name"><?php _e( 'Your Slack team domain name', 'gmt_slack_api' ); ?></label>
		<?php
	}

	function gmt_slack_api_settings_field_auth_token() {
		$options = gmt_slack_api_get_theme_options();
		?>
		<input type="text" name="gmt_slack_api_theme_options[auth_token]" class="large-text" id="auth_token" value="<?php echo esc_attr( $options['auth_token'] ); ?>" />
		<label class="description" for="auth_token"><?php _e( 'Your Slack authorization token', 'gmt_slack_api' ); ?></label>
		<?php
	}

	function gmt_slack_api_settings_field_form_key() {
		$options = gmt_slack_api_get_theme_options();
		?>
		<input type="text" name="gmt_slack_api_theme_options[form_key]" class="large-text" id="form_key" value="<?php echo esc_attr( $options['form_key'] ); ?>" />
		<label class="description" for="form_key"><?php _e( 'A key to use in your form to verify the source', 'gmt_slack_api' ); ?></label>
		<?php
	}

	function gmt_slack_api_settings_field_form_secret() {
		$options = gmt_slack_api_get_theme_options();
		?>
		<input type="text" name="gmt_slack_api_theme_options[form_secret]" class="large-text" id="form_secret" value="<?php echo esc_attr( $options['form_secret'] ); ?>" />
		<label class="description" for="form_secret"><?php _e( 'A secret to use in your form to verify the source', 'gmt_slack_api' ); ?></label>
		<?php
	}

	function gmt_slack_api_settings_field_origin() {
		$options = gmt_slack_api_get_theme_options();
		?>
		<input type="text" name="gmt_slack_api_theme_options[origin]" class="large-text" id="origin" value="<?php echo esc_attr( $options['origin'] ); ?>" />
		<label class="description" for="origin"><?php _e( 'Whitelisted domain origins for the API (optional, comma-separated)', 'gmt_slack_api' ); ?></label>
		<?php
	}



	/**
	 * Theme Option Defaults & Sanitization
	 * Each option field requires a default value under gmt_slack_api_get_theme_options(), and an if statement under gmt_slack_api_theme_options_validate();
	 */

	// Get the current options from the database.
	// If none are specified, use these defaults.
	function gmt_slack_api_get_theme_options() {
		$saved = (array) get_option( 'gmt_slack_api_theme_options' );
		$defaults = array(
			'domain_name' => '',
			'auth_token' => '',
			'form_key' => '',
			'form_secret' => '',
			'origin' => '',
		);

		$defaults = apply_filters( 'gmt_slack_api_default_theme_options', $defaults );

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return $options;
	}

	// Sanitize and validate updated theme options
	function gmt_slack_api_theme_options_validate( $input ) {
		$output = array();

		if ( isset( $input['domain_name'] ) && ! empty( $input['domain_name'] ) )
			$output['domain_name'] = wp_filter_nohtml_kses( $input['domain_name'] );

		if ( isset( $input['auth_token'] ) && ! empty( $input['auth_token'] ) )
			$output['auth_token'] = wp_filter_nohtml_kses( $input['auth_token'] );

		if ( isset( $input['form_key'] ) && ! empty( $input['form_key'] ) )
			$output['form_key'] = wp_filter_nohtml_kses( $input['form_key'] );

		if ( isset( $input['form_secret'] ) && ! empty( $input['form_secret'] ) )
			$output['form_secret'] = wp_filter_nohtml_kses( $input['form_secret'] );

		if ( isset( $input['origin'] ) && ! empty( $input['origin'] ) )
			$output['origin'] = wp_filter_nohtml_kses( str_replace(' ', '', $input['origin']) );

		return apply_filters( 'gmt_slack_api_theme_options_validate', $output, $input );
	}



	/**
	 * Theme Options Menu
	 * Each option field requires its own add_settings_field function.
	 */

	// Create theme options menu
	// The content that's rendered on the menu page.
	function gmt_slack_api_theme_options_render_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Slack WP Rest API Settings', 'gmt_slack_api' ); ?></h2>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'gmt_slack_api_options' );
					do_settings_sections( 'theme_options' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Register the theme options page and its fields
	function gmt_slack_api_theme_options_init() {

		// Register a setting and its sanitization callback
		// register_setting( $option_group, $option_name, $sanitize_callback );
		// $option_group - A settings group name.
		// $option_name - The name of an option to sanitize and save.
		// $sanitize_callback - A callback function that sanitizes the option's value.
		register_setting( 'gmt_slack_api_options', 'gmt_slack_api_theme_options', 'gmt_slack_api_theme_options_validate' );


		// Register our settings field group
		// add_settings_section( $id, $title, $callback, $page );
		// $id - Unique identifier for the settings section
		// $title - Section title
		// $callback - // Section callback (we don't want anything)
		// $page - // Menu slug, used to uniquely identify the page. See gmt_slack_api_theme_options_add_page().
		add_settings_section( 'general', null,  '__return_false', 'theme_options' );


		// Register our individual settings fields
		// add_settings_field( $id, $title, $callback, $page, $section );
		// $id - Unique identifier for the field.
		// $title - Setting field title.
		// $callback - Function that creates the field (from the Theme Option Fields section).
		// $page - The menu page on which to display this field.
		// $section - The section of the settings page in which to show the field.
		add_settings_field( 'domain_name', __( 'Team Name', 'gmt_slack_api' ), 'gmt_slack_api_settings_field_domain_name', 'theme_options', 'general' );
		add_settings_field( 'auth_token', __( 'Authorization Token', 'gmt_slack_api' ), 'gmt_slack_api_settings_field_auth_token', 'theme_options', 'general' );
		add_settings_field( 'form_key', __( 'Form Key', 'gmt_slack_api' ), 'gmt_slack_api_settings_field_form_key', 'theme_options', 'general' );
		add_settings_field( 'form_secret', __( 'Form Secret', 'gmt_slack_api' ), 'gmt_slack_api_settings_field_form_secret', 'theme_options', 'general' );
		add_settings_field( 'origin', __( 'Whitelisted Domains', 'gmt_slack_api' ), 'gmt_slack_api_settings_field_origin', 'theme_options', 'general' );

	}
	add_action( 'admin_init', 'gmt_slack_api_theme_options_init' );

	// Add the theme options page to the admin menu
	// Use add_theme_page() to add under Appearance tab (default).
	// Use add_menu_page() to add as it's own tab.
	// Use add_submenu_page() to add to another tab.
	function gmt_slack_api_theme_options_add_page() {

		// add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		// $page_title - Name of page
		// $menu_title - Label in menu
		// $capability - Capability required
		// $menu_slug - Used to uniquely identify the page
		// $function - Function that renders the options page
		// $theme_page = add_theme_page( __( 'Theme Options', 'gmt_slack_api' ), __( 'Theme Options', 'gmt_slack_api' ), 'edit_theme_options', 'theme_options', 'gmt_slack_api_theme_options_render_page' );

		// $theme_page = add_menu_page( __( 'Theme Options', 'gmt_slack_api' ), __( 'Theme Options', 'gmt_slack_api' ), 'edit_theme_options', 'theme_options', 'gmt_slack_api_theme_options_render_page' );
		$theme_page = add_submenu_page( 'options-general.php', __( 'Slack API', 'gmt_slack_api' ), __( 'Slack API', 'gmt_slack_api' ), 'edit_theme_options', 'slack_settings', 'gmt_slack_api_theme_options_render_page' );
	}
	add_action( 'admin_menu', 'gmt_slack_api_theme_options_add_page' );



	// Restrict access to the theme options page to admins
	function gmt_slack_api_option_page_capability( $capability ) {
		return 'edit_theme_options';
	}
	add_filter( 'option_page_capability_gmt_slack_api_options', 'gmt_slack_api_option_page_capability' );
