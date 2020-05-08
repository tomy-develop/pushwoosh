<?php

namespace NotificationChannels\Pushwoosh;

use DateTimeInterface;
use DateTimeZone;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use JsonSerializable;

class PushwooshMessage implements JsonSerializable
{
    protected $androidRootParameters;
    protected $apnsTrimContent;
    protected $campaign;
    protected $content;
    protected $data;
    protected $identifier;
    protected $iosBadges;
    protected $iosCategoryId;
    protected $iosCritical;
    protected $iosRootParameters;
    protected $iosSilent;
    protected $iosSound;
    protected $iosSubtitle;
    protected $iosThreadId;
    protected $iosTitle;
    protected $iosTtl;
    protected $preset;
    protected $recipientTimezone;
    protected $shortenUrl;
    protected $timezone;
    protected $throughput;
    protected $url;
    protected $when;

    /**
     * Create a new push message.
     *
     * @param string $content
     * @return void
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
        $this->recipientTimezone = false;
        $this->when = 'now';
    }

    /**
     * Associate the message to the given notification.
     *
     * @param \Illuminate\Notifications\Notification $notification
     * @return $this
     */
    public function associate(Notification $notification)
    {
        if (!$this->identifier) {
            $this->identifier = $notification->id;
        }

        return $this;
    }

    /**
     * Set the Pushwoosh apns trim content code.
     *
     * @param int $apnsTrimContent
     * @return $this
     */
    public function apnsTrimContent(int $apnsTrimContent)
    {
        if (!in_array($apnsTrimContent, [0, 1])) {
            throw new InvalidArgumentException("Invalid platform {$apnsTrimContent}");
        }

        $this->apnsTrimContent = $apnsTrimContent;

        return $this;
    }

    /**
     * Set the Pushwoosh campaign code.
     *
     * @param string $campaign
     * @return $this
     */
    public function campaign(string $campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Set the message content.
     *
     * @param string $content
     * @param string|null $language
     * @return $this
     */
    public function content(string $content, string $language = null)
    {
        if ($language) {
            if (!is_array($this->content)) {
                $this->content = [];
            }

            $this->content[$language] = $content;
        } else {
            $this->content = $content;
        }

        return $this;
    }

    /**
     * Set the delivery moment.
     *
     * @param \DateTimeInterface|string $when
     * @param \DateTimeZone|string|null $timezone
     * @return $this
     */
    public function deliverAt($when, $timezone = null)
    {
        if ($when instanceof DateTimeInterface) {
            $timezone = $when->getTimezone();
            $when = $when->format('Y-m-d H:i');
        }

        if ($timezone instanceof DateTimeZone) {
            $timezone = $timezone->getName();
        }

        $this->timezone = $timezone;
        $this->when = $when;

        return $this;
    }

    /**
     * Set the message identifier.
     *
     * @param string $identifier
     * @return $this
     */
    public function identifier(string $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set the Pushwoosh ios badges code.
     *
     * @param string $iosBadges
     * @return $this
     */
    public function iosBadges(string $iosBadges)
    {
        $this->iosBadges = $iosBadges;

        return $this;
    }

    /**
     * Set the Pushwoosh ios category id code.
     *
     * @param int $iosCategoryId
     * @return $this
     */
    public function iosCategoryId(int $iosCategoryId)
    {
        $this->iosCategoryId = $iosCategoryId;

        return $this;
    }

    /**
     * Set the Pushwoosh ios critical code.
     *
     * @param bool $iosCritical
     * @return $this
     */
    public function iosCritical(bool $iosCritical)
    {
        $this->iosCritical = $iosCritical;

        return $this;
    }

    /**
     * Set the Pushwoosh ios silent code.
     *
     * @param int $iosSilent
     * @return $this
     */
    public function iosSilent(int $iosSilent)
    {
        if (!in_array($iosSilent, [0, 1])) {
            throw new InvalidArgumentException("Invalid platform {$iosSilent}");
        }

        $this->iosSilent = $iosSilent;

        return $this;
    }

    /**
     * Set the Pushwoosh ios sound code.
     *
     * @param string $iosSound
     * @return $this
     */
    public function iosSound(string $iosSound)
    {
        $this->iosSound = $iosSound;

        return $this;
    }

    /**
     * Set the Pushwoosh ios subtitle code.
     *
     * @param string $iosSubtitle
     * @return $this
     */
    public function iosSubtitle(string $iosSubtitle)
    {
        $this->iosSubtitle = $iosSubtitle;

        return $this;
    }

    /**
     * Set the Pushwoosh ios thread id code.
     *
     * @param string $iosThreadId
     * @return $this
     */
    public function iosThreadId(string $iosThreadId)
    {
        $this->iosThreadId = $iosThreadId;

        return $this;
    }

    /**
     * Set the Pushwoosh ios title code.
     *
     * @param string $iosTitle
     * @return $this
     */
    public function iosTitle(string $iosTitle)
    {
        $this->iosTitle = $iosTitle;

        return $this;
    }

    /**
     * Set the Pushwoosh ios ttl code.
     *
     * @param int $iosTtl
     * @return $this
     */
    public function iosTtl(int $iosTtl)
    {
        $this->iosTtl = $iosTtl;

        return $this;
    }

    /**
     * Convert the message into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $payload = [
            'android_root_params' => $this->androidRootParameters,
            'apns_trim_content' => $this->apnsTrimContent,
            'campaign' => $this->campaign,
            'content' => $this->content,
            'data' => $this->data,
            'ignore_user_timezone' => !$this->recipientTimezone,
            'ios_badges' => $this->iosBadges,
            'ios_category_id' => $this->iosCategoryId,
            'ios_critical' => $this->iosCritical,
            'ios_root_params' => $this->iosRootParameters,
            'ios_silent' => $this->iosSilent,
            'ios_sound' => $this->iosSound,
            'ios_subtitle' => $this->iosSubtitle,
            'ios_thread_id' => $this->iosThreadId,
            'ios_title' => $this->iosTitle,
            'ios_ttl' => $this->iosTtl,
            'link' => $this->url,
            'minimize_link' => $this->url ? $this->shortenUrl : null,
            'preset' => $this->preset,
            'send_date' => $this->when,
            'send_rate' => $this->throughput,
            'transactionId' => $this->identifier,
            'timezone' => $this->timezone,
        ];

        return array_filter($payload, function ($value) {
            return $value !== null;
        });
    }

    /**
     * Set the Pushwoosh preset code.
     *
     * @param string $preset
     * @return $this
     */
    public function preset(string $preset)
    {
        $this->preset = $preset;

        return $this;
    }

    /**
     * Throttle the message rollout.
     *
     * @param int $limit
     * @return $this
     */
    public function throttle(int $limit)
    {
        $this->throughput = max(100, min($limit, 1000));

        return $this;
    }

    /**
     * Set the URL the message should link to.
     *
     * @param string $url
     * @param bool $shorten
     * @return $this
     */
    public function url(string $url, bool $shorten = true)
    {
        $this->shortenUrl = $shorten;
        $this->url = $url;

        return $this;
    }

    /**
     * Add a root level parameter.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $platform
     * @return $this
     */
    public function with(string $key, $value, string $platform = null)
    {
        if (!in_array($platform, [null, 'ios', 'android'])) {
            throw new InvalidArgumentException("Invalid platform {$platform}");
        }

        if (($platform ?: 'android') === 'android') {
            $this->androidRootParameters[$key] = $value;
            $this->data[$key] = $value; # android_root_params seems to (not always) work
        }

        if (($platform ?: 'ios') === 'ios') {
            $this->iosRootParameters[$key] = $value;
        }

        return $this;
    }

    /**
     * Respect the recipients' timezone when delivering.
     *
     * @return $this
     */
    public function useRecipientTimezone()
    {
        $this->recipientTimezone = true;

        return $this;
    }
}
