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

class TransportBuilder extends \Magento\Newsletter\Model\Queue\TransportBuilder
{
    public $legacy = false;

    public $sender;

    public function useLegacyWay($con = true)
    {
        $this->legacy = $con;
    }

    public function setFrom($from)
    {
        if (!$this->legacy) {
            return parent::setFrom($from);
        }

        $this->sender = $from;
        return $this;
    }

    protected function prepareMessage()
    {
        if (!$this->legacy) {
            return parent::prepareMessage();
        }

        /** @var AbstractTemplate $template */
        $template = $this->getTemplate()->setData($this->templateData);
        $this->setTemplateFilter($template);

        $this->message->setMessageType(
            \Magento\Framework\Mail\MessageInterface::TYPE_HTML
        )->setBody(
            $template->getProcessedTemplate($this->templateVars)
        )->setSubject(
            $template->getSubject()
        );

        if ($this->sender) {
            $this->message->setFrom($this->sender['email'], $this->sender['name']);
        }

        return $this;
    }
}
