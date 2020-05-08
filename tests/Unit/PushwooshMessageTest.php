<?php

namespace NotificationChannels\Pushwoosh\Tests\Unit;

use DateTime;
use DateTimeZone;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Pushwoosh\PushwooshMessage;
use PHPUnit\Framework\TestCase;

class PushwooshMessageTest extends TestCase
{
    /**
     * Get a fresh notification.
     *
     * @return \Illuminate\Notifications\Notification
     */
    protected function newNotification()
    {
        return tap(new Notification, function (Notification $notification) {
            $notification->id = Str::random(24);
        });
    }

    /**
     * Test if the required attributes are present upon serialization.
     *
     * @return void
     */
    public function testRequiredAttributesArePresent()
    {
        $payload = (new PushwooshMessage)->jsonSerialize();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('content', $payload);
        $this->assertIsString($payload['content']);
        $this->assertArrayHasKey('ignore_user_timezone', $payload);
        $this->assertTrue($payload['ignore_user_timezone']);
        $this->assertArrayHasKey('send_date', $payload);
        $this->assertEquals('now', $payload['send_date']);
    }

    /**
     * Test the modification of the apns trim content.
     *
     * @return void
     */
    public function testApnsTrimContentModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('apns_trim_content', $message->jsonSerialize());

        $message->apnsTrimContent(0);
        $this->assertArrayHasKey('apns_trim_content', $message->jsonSerialize());
        $this->assertEquals(0, $message->jsonSerialize()['apns_trim_content']);

        $message->apnsTrimContent(1);
        $this->assertArrayHasKey('apns_trim_content', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['apns_trim_content']);
    }

    /**
     * Test modification of the campaign code.
     *
     * @return void
     */
    public function testCampaignModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('campaign', $message->jsonSerialize());

        $message = (new PushwooshMessage)->campaign('foo');
        $this->assertArrayHasKey('campaign', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['campaign']);
    }

    /**
     * Test modification of the message content.
     *
     * @return void
     * @depends testRequiredAttributesArePresent
     */
    public function testContentModification()
    {
        $message = (new PushwooshMessage)->content('foo');
        $this->assertEquals('foo', $message->jsonSerialize()['content']);

        $message = (new PushwooshMessage)->content('bar', 'baz');
        $this->assertEquals(['baz' => 'bar'], $message->jsonSerialize()['content']);
    }

    /**
     * Test modification of the delivery moment.
     *
     * @return void
     * @throws \Exception
     * @depends testRequiredAttributesArePresent
     */
    public function testDeliveryMomentModification()
    {
        $message = (new PushwooshMessage)->deliverAt('2019-02-07 19:33');
        $this->assertEquals('2019-02-07 19:33', $message->jsonSerialize()['send_date']);
        $this->assertArrayNotHasKey('timezone', $message->jsonSerialize());

        $message = (new PushwooshMessage)->deliverAt('2019-01-09 03:57', 'Europe/Amsterdam');
        $this->assertEquals('2019-01-09 03:57', $message->jsonSerialize()['send_date']);
        $this->assertArrayHasKey('timezone', $message->jsonSerialize());
        $this->assertEquals('Europe/Amsterdam', $message->jsonSerialize()['timezone']);

        $datetime = new DateTime('2019-05-23 09:49', new DateTimeZone('Australia/Brisbane'));
        $message = (new PushwooshMessage)->deliverAt($datetime);
        $this->assertEquals('2019-05-23 09:49', $message->jsonSerialize()['send_date']);
        $this->assertArrayHasKey('timezone', $message->jsonSerialize());
        $this->assertEquals('Australia/Brisbane', $message->jsonSerialize()['timezone']);

        $message = (new PushwooshMessage)->deliverAt('2019-03-19 21:50', new DateTimeZone('Atlantic/Bermuda'));
        $this->assertEquals('2019-03-19 21:50', $message->jsonSerialize()['send_date']);
        $this->assertArrayHasKey('timezone', $message->jsonSerialize());
        $this->assertEquals('Atlantic/Bermuda', $message->jsonSerialize()['timezone']);
    }

    /**
     * Test the modification of the identifier.
     *
     * @return void
     */
    public function testIdentifierModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('transactionId', $message->jsonSerialize());

        $message->identifier('foo');
        $this->assertArrayHasKey('transactionId', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['transactionId']);
    }

    /**
     * Test the modification of the ios badges.
     *
     * @return void
     */
    public function testIosBadgesModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_badges', $message->jsonSerialize());

        $message->iosBadges(1);
        $this->assertArrayHasKey('ios_badges', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['ios_badges']);
    }

    /**
     * Test the modification of the ios category id.
     *
     * @return void
     */
    public function testIosCategoryIdModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_category_id', $message->jsonSerialize());

        $message->iosCategoryId(1);
        $this->assertArrayHasKey('ios_category_id', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['ios_category_id']);
    }

    /**
     * Test the modification of the ios critical.
     *
     * @return void
     */
    public function testIosCriticalModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_critical', $message->jsonSerialize());

        $message->iosCritical(true);
        $this->assertArrayHasKey('ios_critical', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['ios_critical']);
    }

    /**
     * Test the modification of the ios silent.
     *
     * @return void
     */
    public function testIosSilentModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_silent', $message->jsonSerialize());

        $message->iosSilent(0);
        $this->assertArrayHasKey('ios_silent', $message->jsonSerialize());
        $this->assertEquals(0, $message->jsonSerialize()['ios_silent']);

        $message->iosSilent(1);
        $this->assertArrayHasKey('ios_silent', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['ios_silent']);
    }

    /**
     * Test the modification of the ios sound.
     *
     * @return void
     */
    public function testIosSoundModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_sound', $message->jsonSerialize());

        $message->iosSound('foo');
        $this->assertArrayHasKey('ios_sound', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['ios_sound']);
    }

    /**
     * Test the modification of the ios subtitle.
     *
     * @return void
     */
    public function testIosSubtitleModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_subtitle', $message->jsonSerialize());

        $message->iosSubtitle('foo');
        $this->assertArrayHasKey('ios_subtitle', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['ios_subtitle']);
    }

    /**
     * Test the modification of the ios threadId.
     *
     * @return void
     */
    public function testIosThreadIdModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_thread_id', $message->jsonSerialize());

        $message->iosThreadId('foo');
        $this->assertArrayHasKey('ios_thread_id', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['ios_thread_id']);
    }

    /**
     * Test the modification of the ios title.
     *
     * @return void
     */
    public function testIosTitleModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_title', $message->jsonSerialize());

        $message->iosTitle('foo');
        $this->assertArrayHasKey('ios_title', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['ios_title']);
    }

    /**
     * Test the modification of the ios ttl.
     *
     * @return void
     */
    public function testIosTtlModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('ios_ttl', $message->jsonSerialize());

        $message->iosTtl(1);
        $this->assertArrayHasKey('ios_ttl', $message->jsonSerialize());
        $this->assertEquals(1, $message->jsonSerialize()['ios_ttl']);
    }

    /**
     * Test the association of message to notification.
     *
     * @return void
     * @depends testIdentifierModification
     */
    public function testNotificationAssociation()
    {
        $message = new PushwooshMessage();
        $notification = $this->newNotification();

        $message->associate($notification);
        $this->assertArrayHasKey('transactionId', $message->jsonSerialize());
        $this->assertEquals($notification->id, $message->jsonSerialize()['transactionId']);

        $message->identifier('foo')->associate($notification);
        $this->assertArrayHasKey('transactionId', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['transactionId']);
    }

    /**
     * Test modification of the preset code.
     *
     * @return void
     */
    public function testPresetModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('preset', $message->jsonSerialize());

        $message->preset('foo');
        $this->assertArrayHasKey('preset', $message->jsonSerialize());
        $this->assertEquals('foo', $message->jsonSerialize()['preset']);
    }

    /**
     * Test modification of the root parameters.
     *
     * @return void
     */
    public function testRootParameterModification()
    {
        $message = new PushwooshMessage();
        $params = ['foo' => 'bar'];

        $this->assertArrayNotHasKey('android_root_params', $message->jsonSerialize());
        $this->assertArrayNotHasKey('ios_root_params', $message->jsonSerialize());

        $message->with('foo', 'bar');

        $this->assertArrayHasKey('android_root_params', $message->jsonSerialize());
        $this->assertEquals($params, $message->jsonSerialize()['android_root_params']);
        $this->assertArrayHasKey('ios_root_params', $message->jsonSerialize());
        $this->assertEquals($params, $message->jsonSerialize()['ios_root_params']);
    }

    /**
     * Test modification of the root parameters (Android only).
     *
     * @return void
     */
    public function testRootParameterModificationAndroid()
    {
        $message = new PushwooshMessage();
        $params = ['foo' => 'bar'];

        $message->with('foo', 'bar', 'android');

        $this->assertArrayHasKey('android_root_params', $message->jsonSerialize());
        $this->assertArrayNotHasKey('ios_root_params', $message->jsonSerialize());
        $this->assertEquals($params, $message->jsonSerialize()['android_root_params']);
    }

    /**
     * Test modification of the root parameters (iOS only).
     *
     * @return void
     */
    public function testRootParameterModificationIos()
    {
        $message = new PushwooshMessage();
        $params = ['foo' => 'bar'];

        $message->with('foo', 'bar', 'ios');

        $this->assertArrayNotHasKey('android_root_params', $message->jsonSerialize());
        $this->assertArrayHasKey('ios_root_params', $message->jsonSerialize());
        $this->assertEquals($params, $message->jsonSerialize()['ios_root_params']);
    }

    /**
     * Test modification of rollout throughput.
     *
     * @return void
     */
    public function testThroughputModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('send_rate', $message->jsonSerialize());

        $message->throttle(10);
        $this->assertArrayHasKey('send_rate', $message->jsonSerialize());
        $this->assertEquals(100, $message->jsonSerialize()['send_rate']);

        $message->throttle(100);
        $this->assertArrayHasKey('send_rate', $message->jsonSerialize());
        $this->assertEquals(100, $message->jsonSerialize()['send_rate']);

        $message->throttle(1000);
        $this->assertArrayHasKey('send_rate', $message->jsonSerialize());
        $this->assertEquals(1000, $message->jsonSerialize()['send_rate']);

        $message->throttle(10000);
        $this->assertArrayHasKey('send_rate', $message->jsonSerialize());
        $this->assertEquals(1000, $message->jsonSerialize()['send_rate']);
    }

    /**
     * Test modification of the URL.
     *
     * @return void
     */
    public function testUrlModification()
    {
        $message = new PushwooshMessage();
        $this->assertArrayNotHasKey('link', $message->jsonSerialize());
        $this->assertArrayNotHasKey('minimize_link', $message->jsonSerialize());

        $message->url('https://google.com');
        $this->assertArrayHasKey('link', $message->jsonSerialize());
        $this->assertArrayHasKey('minimize_link', $message->jsonSerialize());
        $this->assertEquals('https://google.com', $message->jsonSerialize()['link']);
        $this->assertTrue($message->jsonSerialize()['minimize_link']);

        $message->url('https://google.com', false);
        $this->assertArrayHasKey('link', $message->jsonSerialize());
        $this->assertArrayHasKey('minimize_link', $message->jsonSerialize());
        $this->assertEquals('https://google.com', $message->jsonSerialize()['link']);
        $this->assertFalse($message->jsonSerialize()['minimize_link']);
    }

    /**
     * Test modification of the timezone strategy.
     *
     * @return void
     * @depends testRequiredAttributesArePresent
     */
    public function testTimezoneStrategyModification()
    {
        $message = new PushwooshMessage();
        $this->assertTrue($message->jsonSerialize()['ignore_user_timezone']);

        $message = (new PushwooshMessage)->useRecipientTimezone();
        $this->assertFalse($message->jsonSerialize()['ignore_user_timezone']);
    }
}
