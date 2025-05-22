<?php
use PHPUnit\Framework\TestCase;
use Slack_Interface\Slack;
use Slack_Interface\Slack_API_Exception;

class SlackOauthTest extends TestCase
{
    protected function setUp(): void
    {
        Requests::reset();
        $GLOBALS['wp_options'] = [];
    }

    public function test_do_oauth_stores_access()
    {
        Requests::addResponse(json_encode([
            'ok' => true,
            'access_token' => 'token123',
            'scope' => 'admin,channels:read',
            'team' => (object)['name' => 'Team', 'id' => 'T1'],
            'incoming_webhook' => ['url' => 'https://hooks.slack.com/services/hook','channel' => 'C1'],
        ]));

        $slack = new Slack();
        $access = $slack->do_oauth('code');

        $this->assertSame('token123', $access->get_access_token());
        $this->assertArrayHasKey('myog_slack_access', $GLOBALS['wp_options']);
    }

    public function test_do_oauth_throws_on_error()
    {
        Requests::addResponse(json_encode(['ok' => false, 'error' => 'invalid']));
        $slack = new Slack();
        $this->expectException(Slack_API_Exception::class);
        $slack->do_oauth('bad');
    }
}
