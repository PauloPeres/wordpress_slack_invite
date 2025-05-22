<?php
use PHPUnit\Framework\TestCase;
use Slack_Interface\Slack_Access;

class SlackAccessTest extends TestCase {
    public function test_to_json() {
        $data = [
            'access_token' => 'token123',
            'scope' => ['read', 'write'],
            'team_name' => 'Team',
            'team_id' => 'TID',
            'incoming_webhook' => ['url' => 'https://hooks.slack.com/services/TOKEN', 'channel' => 'general'],
        ];
        $access = new Slack_Access($data);
        $this->assertEquals($data, json_decode($access->to_json(), true));
    }

    public function test_is_configured() {
        $configured = new Slack_Access(['access_token' => 'abc']);
        $this->assertTrue($configured->is_configured());

        $empty = new Slack_Access([]);
        $this->assertFalse($empty->is_configured());
    }

    public function test_getters() {
        $data = [
            'access_token' => 'token456',
            'team_name' => 'Example',
            'team_id' => 'T456',
            'incoming_webhook' => ['url' => 'https://hooks.slack.com/services/TOKEN2', 'channel' => 'random'],
        ];
        $access = new Slack_Access($data);

        $this->assertSame('token456', $access->get_access_token());
        $this->assertSame('Example', $access->get_team_name());
        $this->assertSame('T456', $access->get_team_id());
        $this->assertSame('https://hooks.slack.com/services/TOKEN2', $access->get_incoming_webhook());
        $this->assertSame('random', $access->get_incoming_webhook_channel());
    }
}
