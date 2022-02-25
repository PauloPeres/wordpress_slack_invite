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

# Contributores 
The Slack Interface was first developed by Jarkko Laine <jarkko@jarkkolaine.com>
https://github.com/jarkkolaine/php-slack-tutorial
Thank you Jarkko

