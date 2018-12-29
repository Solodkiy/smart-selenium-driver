<?php
declare(strict_types=1);

namespace Solodkiy\SmartSeleniumDriver;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use PHPUnit\Framework\TestCase;
use Solodkiy\SmartSeleniumDriver\Exceptions\SmartSeleniumCommandError;

class SmartSeleniumDriverTest extends TestCase
{
    /**
     * Try to download 100k file
     * @throws SmartSeleniumCommandError
     * @throws TimeOutException
     */
    public function testDownloadFile()
    {
        // This is integration test. Use this command to run smart-selenium instance:
        // docker run -p 4444:4444 solodkiy/smart-selenium

        $driver = $this->createDriver();
        $driver->clearDownloadDir();

        $url = 'http://httpbin.org/stream-bytes/100000';
        $driver->get($url);

        $content = $driver->getDownloadedFileByName('100000', 5);
        $this->assertEquals(100000, strlen($content));
    }

    private function createDriver(): SmartSeleniumDriver
    {
        $capabilities = DesiredCapabilities::chrome();
        $url = 'http://localhost:4444/wd/hub';
        $driver = SmartSeleniumDriver::create($url, $capabilities, 5000);

        $window = new WebDriverDimension(1024, 768);
        $driver->manage()->window()->setSize($window);
        return $driver;
    }
}
