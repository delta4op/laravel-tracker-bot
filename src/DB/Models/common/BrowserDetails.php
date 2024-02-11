<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\common;

use Delta4op\Laravel\TrackerBot\DB\Models\BaseModel;
use Delta4op\Laravel\TrackerBot\Enums\BrowserType;
use Delta4op\Laravel\TrackerBot\Enums\OperatingSystem;
use hisorange\BrowserDetect\Facade as Browser;

/**
 * @property ?string $deviceType
 * @property ?string $mobileGrade
 * @property ?string $platformName
 * @property ?string $platformVersion
 * @property ?string $platformFamily
 * @property ?string $platformVersionMajor
 * @property ?string $platformVersionMinor
 * @property ?string $platformVersionPatch
 * @property ?string $deviceModel
 * @property ?string $deviceFamily
 * @property ?string $browserName
 * @property ?string $browserFamily
 * @property ?string $browserType
 * @property ?string $browserEngine
 * @property ?string $browserVersion
 * @property ?string $os
 * @property ?string $userAgent
 * @property ?string $clientId
 * @property ?array<string> $clientIps
 */
class BrowserDetails extends BaseModel
{
    public static function autoInit(): static
    {
        $instance = new self;

        $instance->deviceType = Browser::deviceType();
        $instance->deviceModel = Browser::deviceModel();
        $instance->deviceFamily = Browser::deviceFamily();
        $instance->browserName = Browser::browserName();
        if (Browser::isChrome()) {
            $instance->browserType = BrowserType::CHROME;
        } elseif (Browser::isEdge()) {
            $instance->browserType = BrowserType::EDGE;
        } elseif (Browser::isSafari()) {
            $instance->browserType = BrowserType::SAFARI;
        } elseif (Browser::isFirefox()) {
            $instance->browserType = BrowserType::FIREFOX;
        } elseif (Browser::isOpera()) {
            $instance->browserType = BrowserType::OPERA;
        } elseif (Browser::isInApp()) {
            $instance->browserType = BrowserType::IN_APP;
        } elseif (Browser::isIE()) {
            $instance->browserType = BrowserType::INTERNET_EXPLORER;
        }

        $instance->browserEngine = Browser::browserEngine();
        $instance->browserVersion = Browser::browserVersion();
        $instance->browserFamily = Browser::browserFamily();

        if (Browser::isAndroid()) {
            $instance->os = OperatingSystem::ANDROID;
        } elseif (Browser::isMac()) {
            $instance->os = OperatingSystem::MAC;
        } elseif (Browser::isWindows()) {
            $instance->os = OperatingSystem::WINDOWS;
        } elseif (Browser::isLinux()) {
            $instance->os = OperatingSystem::LINUX;
        }

        $instance->userAgent = Browser::userAgent();

        $instance->platformName = Browser::platformName();
        $instance->platformVersion = Browser::platformVersion();
        $instance->platformFamily = Browser::platformFamily();
        $instance->platformVersionMajor = Browser::platformVersionMajor();
        $instance->platformVersionMinor = Browser::platformVersionMinor();
        $instance->platformVersionPatch = Browser::platformVersionPatch();

        return $instance;
    }
}
