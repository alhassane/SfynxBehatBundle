<?php
/**
 * This file is part of the <Behat> project.
 *
 * @category   Behat
 * @package    Mink
 * @subpackage Context
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-03-02
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\BehatBundle\Behat\MinkExtension\Context;

use Behat\MinkExtension\Context\MinkContext as BaseMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Mink\Exception\UnsupportedDriverActionException,
    Behat\Mink\Exception\ExpectationException;
use Behat\Symfony2Extension\Driver\KernelDriver;
use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Exception\ResponseTextException;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Mink context for Behat BDD tool.
 * Provides Mink integration and base step definitions with additional options.
 * 
 * @category   Behat
 * @package    Mink
 * @subpackage Context
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-03-02
 */
class MinkContext extends BaseMinkContext implements SnippetAcceptingContext,KernelAwareContext
{
    /**
     * Behat additional options
     * 
     * @var array $options
     */
    
    public static $options;

    /**
     * Allowed values for addtional options
     * 
     * @var array $allowed
     */
    public static $allowed;
    
    /**
     * Application Kernel
     * 
     * @var KernelInterface $kernel
     */
    protected $kernel;

    /**
     * Behat additional options initializer
     */
    public function __construct()
    {
        if (isset(self::$options['server']) &&
            self::$options['locale']
        ) {
            exit;
            $this->forTheServer(self::$options['server'], self::$options['locale']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $container = $this->kernel->getContainer();
        if (self::$options === null) {
            self::$options = $container->getParameter('behat.options');
        }
        if (self::$allowed === null) {
            self::$allowed = array(
                'servers' => $container->getParameter('behat.servers'),
                'locales' => $container->getParameter('behat.locales')
            );
        }
    }

    /**
     * Using a specific server and locale
     *
     * @Given /^for the server "(?P<server>(?:[^"]|\\")*)"(?:| with locale "(?P<locale>(?:[^"]|\\")*)")$/
     */
    public function forTheServer($server = null, $locale = null)
    {
        if (count(self::$allowed) >= 1) {
            if (!in_array($server, self::$allowed['servers']) && !empty($server)) {
                throw new \Exception('Website server "'.$server.'" not found.');
            } else {
                $server = self::$options['server'];
            }
            if ($locale !== '' && !in_array($locale, self::$allowed['locales']) && !empty($locale)) {
                throw new \Exception('Website locale "'.$locale.'" not found.');
            } elseif ($locale == '') {
                $locale = self::$options['locale'];
            }

            $baseUrl = 'http://'.strtolower($server);
            $this->setMinkParameter('base_url', strtr($baseUrl, [' ', '']));
        }
    }
    

    public function getSymfonyProfile()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof KernelDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with '.
                '"@mink:symfony2". Using the profiler is not '.
                'supported by %s', $driver
            );
        }

        $profile = $driver->getClient()->getProfile();
        if (false === $profile) {
            throw new \RuntimeException(
                'The profiler is disabled. Activate it by setting '.
                'framework.profiler.only_exceptions to false in '.
                'your config'
            );
        }

        return $profile;
    }    
    
    /**
     * Log with a role
     * 
     * @Given /^(?:|I am )logged as "(?P<role>(?:[^"]|\\")*)"$/
     */
    public function logAs($role)
    {
        switch ($role) {
            case 'super_admin':
                break;
            case 'admin':
                break;
            case 'user':
                break;
        }
    }

    /**
     * @When I wait for :time seconds
     */
    public function iWaitForMessageDisplay($time)
    {
        $this->getSession()->wait($time * 1000);
    }
    
    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )click on mist button"(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @When /^(?:|I )click on mist button"(?P<field>(?:[^"]|\\")*)" with:$/
     * @When /^(?:|I )click on mist button"(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function fillFieldMist($field, $value)
    {
        $this->assertSession()->elementExists($field, $value)->click();
    }
    
    /**
     * Enable or disable JS
     * 
     * @Given /^With(?P<suffix>(?:|out)) Javascript$/
     */
    public function withJavascript($suffix)
    {
        if ($suffix == 'out') {
            // Use Goutte (default: Selenium)
        }
    }
    
    /**
     * Checks, that element with specified CSS is visible on page.
     *
     * @Then /^(?:|The )"(?P<element>[^"]*)" element (should be|is) visible$/
     */
    public function assertElementVisible($element)
    {
        if (!$this->assertSession()->elementExists('css', $element)->isVisible()) {
            throw new \Exception('Element "'.$element.'" not visible.');
        }
    }
    
    /**
     * Checks, that element with specified CSS is not visible on page.
     *
     * @Then /^(?:|The )"(?P<element>[^"]*)" element (should not be|is not) visible$/
     */
    public function assertElementNotVisible($element)
    {
        if ($this->assertSession()->elementExists('css', $element)->isVisible()) {
            throw new \Exception('Element "'.$element.'" visible.');
        }
    }
    
    /**
     * Checks, that element children with specified CSS are on page.
     * 
     * @param string $element
     * @param array $children
     */
    public function assertElementChildrenOnPage($element, $children = array())
    {
        foreach ($children as $child) {
            $this->assertElementOnPage($element . ' ' . $child);
        }
    }
    
    /**
     * Checks, that element children with specified CSS are not on page.
     * 
     * @param string $element
     * @param array $children
     */
    public function assertElementChildrenNotOnPage($element, $children = array())
    {
        foreach ($children as $child) {
            $this->assertElementNotOnPage($element . ' ' . $child);
        }
    }
    
    /**
     * Checks, that element childrens with specified CSS are visible on page.
     * 
     * @param string $element
     * @param array $childrens
     */
    public function assertElementChildrensVisible($element, $childrens = array())
    {
        foreach ($childrens as $children) {
            $this->assertElementVisible($element.' '.$children);
        }
    }
    
    /**
     * Checks, that element childrens with specified CSS are not visible on page.
     * 
     * @param string $element
     * @param array $childrens
     */
    public function assertElementChildrensNotVisible($element, $childrens = array())
    {
        foreach ($childrens as $children) {
            $this->assertElementNotVisible($element.' '.$children);
        }
    }
    
    /**
     * Check an object parameter existance
     *
     * @Then /^(?:|The )"(?P<property>[^"]*)" property should exists$/
     */
    public function assertPropertyExists($property, $subject = null)
    {
        $object = null;
        switch (gettype($subject)) {
            case 'object':
                $object = $subject;
                break;
            case 'array':
                $object = json_decode(json_encode($subject), false);
                break;
            case 'NULL':
                $subject = $this->getSession()->getPage()->getText();
            case 'string':
                $object = json_decode($subject);
                break;
            default:
                throw new \Exception('Object format not supported.');
        }
        if (!property_exists($object, $property)) {
            throw new \Exception('Object property not found.');
        }
        return $object->$property;
    }
    
    /**
     * Click on the element with the provided CSS Selector
     * exemple: Given I click on the element with css selector "a#14"
     *
     * @Then /^(?:|I )should have a title egal to "(?P<title>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsTitle($title)
    {
        $title_css = $this->getSession()->getPage()->find('css', 'title')->getText();
        if ($title_css != $title) {
            throw new \Exception('Title "'.$title.'" not visible.');
        }
    }

    /**
     * Checks, that page contains specified text
     * Example: Then I should see "Who is the Batman?" or "Who are Them?"
     * Example: And I should see "Who is the Batman?" or "Who are Them?"
     *
     * @Then /^(?:|I )should see "(?P<text1>(?:[^"]|\\")*)" or "(?P<text2>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsTextOrText($text1, $text2)
    {
        $actual = $this->getSession()->getDriver()->getContent();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        $regex1 = '/'.preg_quote($this->fixStepArgument($text1), '/').'/ui';
        $regex2 = '/'.preg_quote($this->fixStepArgument($text2), '/').'/ui';
        $message = sprintf('The text "%s" and "%s" was not found anywhere in the text of the current page.', $text1, $text2);

        if ((bool) (preg_match($regex1, $actual) || preg_match($regex2, $actual))) {
            return;
        }
        throw new ResponseTextException($message, $this->session->getDriver());
    }

    /**
     * Checks, that page contains specified text
     * Example: Then I should see "Who is the Batman?" or "Who are Them?"
     * Example: And I should see "Who is the Batman?" or "Who are Them?"
     *
     * @Then /^(?:|I )should not see "(?P<text1>(?:[^"]|\\")*)" and "(?P<text2>(?:[^"]|\\")*)"$/
     */
    public function assertPageNoContainsTextOrText($text1, $text2)
    {
        $actual = $this->getSession()->getDriver()->getContent();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        $regex1 = '/'.preg_quote($this->fixStepArgument($text1), '/').'/ui';
        $regex2 = '/'.preg_quote($this->fixStepArgument($text2), '/').'/ui';
        $message = sprintf('The text "%s" or "%s" appears in the text of this page, but it should not.', $text1, $text2);

        if (!preg_match($regex1, $actual) && !preg_match($regex2, $actual)) {
            return;
        }
        throw new ResponseTextException($message, $this->session->getDriver());
    }
    
    /**
     * {@inheritdoc}
     */
    public function visit($page)
    {
        if ($this->getMinkParameter('base_url') === null) {
            $this->forTheServer(self::$options['server'], self::$options['locale']);
        }
        $this->visitPath($page);
    }
    
    /**
     * @Then /^I switch to iframe "([^"]*)"$/
     */
    public function iSwitchToIframe($iframeId)
    {
        $this->getSession()->switchToIframe($iframeId);
    }  
    
    /**
     * @Then /^I switch to main window$/
     */
    public function iSwitchToMainWindow()
    {
        $this->getSession()->switchToIframe(null);
    }     
    
    /**
     * @Given /^I am authenticated as "([^"]*)"$/
     */
    public function iAmAuthenticatedAs($username)
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof DriverInterface) {
            throw new UnsupportedDriverActionException('This step is only supported by the DriverInterface');
        }

        $user = $this->kernel->getContainer()->get('sfynx.auth.manager.user')->findUserByUsername($username);
        $providerKey = $this->kernel->getContainer()->getParameter('sfynx.auth.firewall_name');

        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $session = $this->kernel->getContainer()->get('session');
        $session->set('_security_'.$providerKey, serialize($token));
        $session->save();

        $this->getSession()->setCookie($session->getName(), $session->getId());
    }
}
