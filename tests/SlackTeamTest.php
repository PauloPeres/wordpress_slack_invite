<?php
use PHPUnit\Framework\TestCase;
use Slack_Interface\Slack_Team;

class SlackTeamTest extends TestCase {
    public function test_to_json() {
        $data = [
            'id' => 'T123',
            'name' => 'My Team',
            'domain' => 'myteam',
            'email_domain' => 'example.com',
            'icons' => ['image_68' => 'icon.png'],
            'enterprise_id' => 'E1',
            'enterprise_name' => 'My Enterprise',
        ];
        $team = new Slack_Team($data);
        $this->assertEquals($data, json_decode($team->to_json(), true));
    }

    public function test_getters_and_setters() {
        $team = new Slack_Team([]);
        $team->set_id('T234');
        $team->set_name('Another');
        $team->set_domain('another');
        $team->set_email_domain('another.com');
        $team->set_icons(['img' => 'icon.png']);
        $team->set_enterprise_id('E2');
        $team->set_enterprise_name('Another Enterprise');

        $this->assertSame('T234', $team->get_id());
        $this->assertSame('Another', $team->get_name());
        $this->assertSame('another', $team->get_domain());
        $this->assertSame('another.com', $team->get_email_domain());
        $this->assertEquals(['img' => 'icon.png'], $team->get_icons());
        $this->assertSame('E2', $team->get_enterprise_id());
        $this->assertSame('Another Enterprise', $team->get_enterprise_name());
    }
}
