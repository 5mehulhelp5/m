<?php
/**
 * MB "Vienas bitas" (www.magetrend.com)
 *
 * @category  Magetrend Extensions for Magento 2
 * @package  Magetend/NewsletterMaker
 * @author   E. Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-newsletter-maker
 */

namespace Magetrend\NewsletterMaker\Plugin\Framework\Mail\Template;

class TransportBuilder
{
    public $variableManager;

    public function __construct(
        \Magetrend\NewsletterMaker\Model\VariableManager $variableManager
    ) {
        $this->variableManager = $variableManager;
    }

    public function beforeSetTemplateVars($subject, $variables)
    {
        if (isset($variables['subscriber']) && !isset($variables['nvar'])) {
            $variables = array_merge($variables, $this->variableManager->getVariableList($variables['subscriber']));
        }

        return [$variables];
    }
}
