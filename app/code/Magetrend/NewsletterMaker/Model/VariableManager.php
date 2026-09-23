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

namespace Magetrend\NewsletterMaker\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magetrend\NewsletterMaker\Helper\Variables;

class VariableManager
{
    public function getVariableList($subscriber)
    {
        return [
            'unsubscribe_link' => $this->getUnsubscriptionLink($subscriber),
            'nvar' => 1
        ];
    }

    public function getUnsubscriptionLink($subscriber)
    {
        return $subscriber->getUnsubscriptionLink();
    }
}
