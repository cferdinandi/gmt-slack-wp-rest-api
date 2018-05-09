<?php

	function gmt_slack_api_invite($request) {

		$options = gmt_slack_api_get_theme_options();
		$params = $request->get_params();

		// Check domain whitelist
		if (!empty($options['origin'])) {
			$origin = $request->get_header('origin');
			if (empty($origin) || !in_array($origin, explode(',', $options['origin']))) {
				return new WP_REST_Response(array(
					'code' => 400,
					'status' => 'disallowed_domain',
					'message' => 'This domain is not whitelisted.'
				), 400);
			}
		}

		// Check key/secret
		if ( !empty($options['form_key']) && !empty($options['form_secret']) && (!isset($params[$options['form_key']]) || empty($params[$options['form_key']]) || $params[$options['form_key']] !== $options['form_secret']) ) {
			return new WP_REST_Response(array(
				'code' => 400,
				'status' => 'failed',
				'message' => 'Unable to subscribe at this time. Please try again.'
			), 400);
		}

		// Check honeypot field
		if ( isset($params['name']) && !empty($params['name'])  ) {
			return new WP_REST_Response(array(
				'code' => 400,
				'status' => 'failed',
				'message' => 'Unable to subscribe at this time. Please try again.'
			), 400);
		}

		// If email is invalid
		if ( empty( filter_var( $params['email'], FILTER_VALIDATE_EMAIL ) ) ) {
			return new WP_REST_Response(array(
				'code' => 400,
				'status' => 'invalid_email',
				'message' => 'Please use a valid email address.'
			), 400);
		}

		// Limit to EDD purchases only
		if (in_array('purchase_required', $params) && $params['purchase_required'] === 'edd' && function_exists('edd_get_users_purchases')) {
			if (empty(edd_get_users_purchases($params['email']))) {
				return new WP_REST_Response(array(
					'code' => 400,
					'status' => 'purchase_required',
					'message' => 'This Slack workspace is only available to customers.'
				), 400);
			}
		}

		// Check if channels specified
		$channels = array_key_exists('channels', $params) && !empty($params['channels']) ? array('channels' => $params['channels']) : array();

		// Invite purchaser to Slack
		$slack = new Slack_Invite( $options['auth_token'], $options['domain_name'] );
		$invitation = $slack->send_invite( sanitize_email( $params['email'] ), $channels );

		if ($invitation['ok'] === TRUE) {
			return new WP_REST_Response(array(
				'code' => 200,
				'status' => 'success',
				'message' => 'An invitation to join the Slack workspace has been sent.'
			), 200);
		}

		if ($invitation['error'] === 'already_invited') {
			return new WP_REST_Response(array(
				'code' => 400,
				'status' => 'already_invited',
				'message' => 'You\'ve already been sent an invite. If you didn\'t receive it, please contact the workspace administrator.'
			), 400);
		}

		if ($invitation['error'] === 'already_in_team') {
			if (!empty($channels)) {
				$channels = explode(',', $channels['channels']);
				$added_to_channels = true;
				foreach ($channels as $channel) {
					$add = $slack->add_to_group( sanitize_email( $params['email'] ), $channel );
					if ($add['ok'] === FALSE) {
						$added_to_channels = false;
					}
				}

				if ($added_to_channels === TRUE) {
					return new WP_REST_Response(array(
						'code' => 200,
						'status' => 'new_channel',
						'message' => 'You have been added to a new channel in this workspace.'
					), 200);
				}

				return;
			}

			return new WP_REST_Response(array(
				'code' => 400,
				'status' => 'already_in_team',
				'message' => 'You\'re already a member of this Slack workspace.'
			), 400);
		}

		return new WP_REST_Response(array(
			'code' => 400,
			'status' => 'failed',
			'message' => 'Unable to subscribe at this time. Please try again.'
		), 400);

	}


	function gmt_slack_api_register_routes () {
		register_rest_route('gmt-slack/v1', '/invite', array(
			'methods' => 'POST',
			'callback' => 'gmt_slack_api_invite',
		));
	}
	add_action('rest_api_init', 'gmt_slack_api_register_routes');