# Slack Guest Invite
How to make it work
To make this plugin work you will have to create your own application with slack!

1. On your Slack Workspace create a public chat room, where the guests will be joining
2. First log into your slack account and go to https://api.slack.com/apps, click on "Create New App"
3. Name your App (can be any name), and choose the Workspace you want to be able to invite new members
4. Change Permissions on the APP, click on "OAuth & Permissions"
    Add this link as a redirect url
    https://YOURLINK.com/wp-admin/options-general.php?page=slack_settings&action=oauth

    On Scopes choose
    `admin,channel:write,channels:read`

5. On the "Basic Information" tab yuou will get the CLIENT ID and CLIENT SECRET you can either:
    Fill the form with the CLIENT ID and CLIENT SECRET, but it's saved as plain text and anyone with access to you WP Wordpress can se it

    Or you can set on your wp_config.php

    `define( 'SLACK_CLIENT_ID', 'XXXXXXXXXXXXX.XXXXXXXXXXXXXXX' );`

    `define( 'SLACK_CLIENT_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXX' );`
    
6. After having that informaiton yuou be able to click on "Add to Slack" Button and authorize your application
7. With the application authorized, insert the short-code `[slack_invite_form channels=channel-name]` (only one channel name) in one of your pages, or popups, and that should work

# Demo
![Invite demo](/invite_demo.png)

# Install on your wordpress
For now i'm not publishing this on the Wordpress Marketplace 
You can simple download the slack_invite.zip and install as a plugin on your wordpress.
Hope this helps anyone
# Contributores 
The Slack Interface was first developed by Jarkko Laine <jarkko@jarkkolaine.com>
https://github.com/jarkkolaine/php-slack-tutorial
Thank you Jarkko



## Repository Overview
This repository contains a WordPress plugin named **Slack Guest Invite**. It lets visitors request invitations to a Slack workspace. The setup steps earlier in this document explain how to create a Slack app, configure OAuth credentials, and place the shortcode `[slack_invite_form channels=channel-name]` on a page.

The main plugin file `myog-slack-guest-invite.php` defines metadata, registers activation hooks, loads dependencies, and boots the plugin:

```
 * Plugin Name:       Slack Guest Invite
 * Plugin URI:        https://bitbucket.org/myowngames/slack-guest-invite/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
```

Further down it starts the plugin:

```
function run_myog_slack_guest_invite() {
        $plugin = new Myog_Slack_Guest_Invite();
        $plugin->run();
}
run_myog_slack_guest_invite();
```

### Code Structure

```
wordpress_slack_invite/
├── admin/         # Admin-facing code (menu, settings, assets)
├── includes/      # Core plugin classes and Slack interface
├── languages/     # Translation template (.pot)
├── public/        # Public-facing form, scripts, and styles
├── myog-slack-guest-invite.php  # Plugin bootstrap
├── uninstall.php  # Cleanup when the plugin is removed
└── README.md
```

1. **Admin area** – `admin/class-myog-slack-guest-invite-admin.php` adds a Slack Invite Settings submenu and handles OAuth callbacks and credential storage.
2. **Public area** – `public/class-myog-slack-guest-invite-public.php` registers the shortcode and processes form submissions to send invites.
3. **Slack integration** – files in `includes/slack-interface` communicate with Slack. `send_invite()` calls the API and maps errors to messages.
4. **Loader and i18n** – the loader class manages hooks while another class loads translations.
5. **Assets** – CSS and JS files style the admin page and form; the public script adds a loading state on submission.

### Key Points for New Contributors

- Slack OAuth credentials (`CLIENT_ID`, `CLIENT_SECRET`) are stored in WordPress options or defined as constants.
- The `[slack_invite_form channels=...]` shortcode accepts one channel name and resolves the channel ID before inviting.
- Translation strings live in `languages/myog-slack-guest-invite.pot`.

### Suggested Next Steps to Learn

- Experiment with the plugin in a WordPress environment to see the form and API calls in action.
- Review how the `Slack` class builds HTTP requests with the Requests library to extend functionality.
- Explore WordPress Plugin Boilerplate conventions used by the classes to add hooks or features.
