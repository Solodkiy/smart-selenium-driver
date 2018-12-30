<?php
declare(strict_types=1);

namespace Solodkiy\SmartSeleniumDriver;

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use PHPUnit\Framework\TestCase;
use Solodkiy\SmartSeleniumDriver\Exceptions\SmartSeleniumCommandError;

/**
 * This is integration test. Use this command to run smart-selenium instance:
 * docker run -p 4444:4444 solodkiy/smart-selenium
 */
class SmartSeleniumDriverTest extends TestCase
{
    /**
     * Try to download 100k file
     * @throws SmartSeleniumCommandError
     * @throws TimeOutException
     */
    public function testDownloadFile()
    {
        $driver = $this->createDriver();

        $driver->get('https://httpbin.org/html');

        $driver->clearDownloadDir();

        $url = 'http://httpbin.org/stream-bytes/100000';
        $driver->get($url);

        $content = $driver->getDownloadedFileByName('100000', 5);
        $this->assertEquals(100000, strlen($content));

        $pageTitle = $driver->findElement(WebDriverBy::cssSelector('H1'))->getText();
        $this->assertEquals('Herman Melville - Moby-Dick', $pageTitle);
    }

    public function testGetFiles()
    {
        $driver = $this->createDriver();

        $driver->get('https://httpbin.org/html');

        $driver->clearDownloadDir();
        $files = $driver->getDownloadedFiles();
        $this->assertEquals([], $files);

        $url = 'http://httpbin.org/response-headers?Content-Disposition=' . urlencode('attachment; filename=MyFileName.ext');
        $driver->get($url);

        $files = $driver->getDownloadedFiles();
        $this->assertEquals(1, count($files));

        $pageTitle = $driver->findElement(WebDriverBy::cssSelector('H1'))->getText();
        $this->assertEquals('Herman Melville - Moby-Dick', $pageTitle);
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
