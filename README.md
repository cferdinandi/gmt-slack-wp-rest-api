# GMT Slack WP Rest API
Provides a WP Rest API endpoint for inviting users to your Slack channel.

*__Note:__ This requires a [legacy token](https://api.slack.com/custom-integrations/legacy-tokens) to authenticate. The new type of token will not work.*

**WORK IN PROGRESS**


## Setup

1. Install the plugin using the WordPress plugin installer.
2. Configure your settings under `Settings > Slack API` in the WordPress Dashboard.
3. Call the desired endpoint from your code.


## Endpoints

- **`<your-domain>/wp-json/gmt-slack/v1/invite`** - Invite someone to your Slack workspace.



## Parameters

|Argument|Example|Required|Description|
|--------|-------|--------|-----------|
|`email`|`some@email.com`|Required|The email address of the user to invite.|
|`channels`|`12345,67890`|Optional|Any channels to add the user to, separated by a comma.|
|`name`|`John`|Optional|If submitted, should be empty. This is a honeypot field, and if completed, indicates a bot filled out the form.|
|`key` (random string)|`hghjkg1247`|Required|To secure your form, use a random string for the field `name` and `value`. These should match the `form_key` and `form_secret` values you used in the settings. Example: `<input type="hidden" name="abcd12345" value="efghijk67890">`|



## Response

### Format

```js
{
	code: 200,
	status: 'success',
	message: 'An invitation to join the Slack workspace has been sent.'
}
```

### Codes

- `200` - Successfully added
- `400` - Something went wrong!

### Statuses

|Code|Status|Message|
|--------|-------|--------|-----------|
|`200`|`success`|An invitation to join the Slack workspace has been sent.|
|`200`|`new_channel`|You have been added to a new channel in this workspace.|
|`400`|`already_invited`|You've already been sent an invite. If you didn't receive it, please contact the workspace administrator.|
|`400`|`already_in_team`|You're already a member of this Slack workspace.|
|`400`|`invalid_email`|Please use a valid email address.|
|`400`|`failed`|Unable to subscribe at this time. Please try again.|


## License

The code is available under the [GPLv3](LICENSE.md). Shoutout to [Automate Slack Invite Gravity Forms](https://wordpress.org/plugins/automate-slack-invite-gravityforms/) for providing the Slack API integration framework.