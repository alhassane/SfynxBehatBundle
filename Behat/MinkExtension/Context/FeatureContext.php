<?php
/**
 * This file is part of the <Behat> project.
 *
 * @category   Behat
 * @package    Feature
 * @subpackage Main
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

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use Behat\Behat\Hook\Scope\AfterFeatureScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Defines application features from the specific context.
 * 
 * class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
 * 
 * @category   Behat
 * @package    Feature
 * @subpackage Main
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-03-02
 */
class FeatureContext implements SnippetAcceptingContext        
{
    /** @var \Behat\MinkExtension\Context\MinkContext */
    protected $minkContext;
    
    /** @var \Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\AjaxContext */
    protected $ajaxsubcontext;
    
    /** @var \Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\HiddenFieldSubContext */
    protected $hiddenfieldsubcontext;

    /** @var \Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\XpathSubContext */
    protected $xpathxubcontext;
    
    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
    }
    
    /** @BeforeFeature */
    public static function setupFeature(BeforeFeatureScope $scope)
    {
        $environment = $scope->getEnvironment();

        if ($environment->getSuite()->hasSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\MinkContext')) {
            $this->minkContext = $environment->getSuite()->getSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\MinkContext');
        }
        if ($environment->getSuite()->hasSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\AjaxContext')) {
            $this->ajaxsubcontext = $environment->getSuite()->getSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\AjaxContext');
        }
        if ($environment->getSuite()->hasSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\HiddenFieldSubContext')) {
            $this->hiddenfieldsubcontext = $environment->getSuite()->getSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\HiddenFieldSubContext');
        }
        if ($environment->getSuite()->hasSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\XpathSubContext')) {
            $this->xpathxubcontext = $environment->getSuite()->getSetting('Sfynx\BehatBundle\Behat\MinkExtension\Context\SubContext\XpathSubContext');
        }
    }

    /** @AfterFeature */
    public static function teardownFeature(AfterFeatureScope $scope)
    {
    }    
    
    /** @BeforeScenario */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
    }

    /** @AfterScenario */
    public function afterScenario(AfterScenarioScope $scope)
    {
    }    
    
    /** @BeforeStep */
    public function beforeStep(BeforeStepScope $scope)
    {
    }

    /** @AfterStep */
    public function afterStep(AfterStepScope $scope)
    {
    }
}
