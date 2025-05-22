<?php
use PHPUnit\Framework\TestCase;
use Slack_Interface\Slack;

class SlackTest extends TestCase
{
    protected function setUp(): void
    {
        Requests::reset();
        $GLOBALS['wp_options'] = [];
        $GLOBALS['wp_options']['myog_slack_access'] = json_encode([
            'access_token' => 'token',
            'scope' => [],
            'team_name' => 'Test Team',
            'team_id' => 'T123',
            'incoming_webhook' => [
                'url' => 'https://hooks.slack.com/services/testhook',
                'channel' => 'C123'
            ],
        ]);
    }

    public function test_get_api_url_uses_team_domain()
    {
        $GLOBALS['wp_options']['myog_slack_team'] = json_encode([
            'id' => 'T123',
            'name' => 'Test Team',
            'domain' => 'myteam'
        ]);

        $slack = new Slack();
        $url = $slack->get_api_url('users.admin.invite');
        $this->assertSame('https://myteam.slack.com/api/users.admin.invite', $url);
    }

    public function test_get_channels_parses_channel_ids()
    {
        Requests::addResponse(json_encode([
            'ok' => true,
            'channels' => [
                (object)['id' => 'C1', 'name' => 'general', 'is_private' => false],
                (object)['id' => 'C2', 'name' => 'random', 'is_private' => false],
                (object)['id' => 'C3', 'name' => 'secret', 'is_private' => true],
            ],
        ]));

        $GLOBALS['wp_options']['myog_slack_team'] = json_encode(['domain' => 'myteam']);
        $slack = new Slack();
        $ids = $slack->get_channels('general,random');

        $this->assertSame(['C1', 'C2'], $ids);
        $this->assertStringContainsString('conversations.list', Requests::$requests[0]['url']);
    }

    public function test_send_notification_posts_to_webhook()
    {
        Requests::addResponse('ok');

        $GLOBALS['wp_options']['myog_slack_team'] = json_encode(['domain' => 'myteam']);
        $slack = new Slack();
        $slack->send_notification('Hello');

        $this->assertNotEmpty(Requests::$requests);
        $request = Requests::$requests[0];
        $this->assertSame('https://hooks.slack.com/services/testhook', $request['url']);
        $payload = json_decode($request['data'], true);
        $this->assertSame('Hello', $payload['text']);
        $this->assertSame('C123', $payload['channel']);
    }
}
