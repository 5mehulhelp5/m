<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Utils;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class FilenameModifier
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ?TimezoneInterface $timezone = null //todo: move to not optional
    ) {
        $this->timezone = $timezone ?? ObjectManager::getInstance()->get(TimezoneInterface::class);
    }

    public function modify(string $filename, ExportProcessInterface $exportProcess): string
    {
        return preg_replace_callback(
            '/{{(?P<type>.*?)(\|(?P<modifier>.*?))?}}/is',
            function ($match) use ($exportProcess) {
                return $this->replaceMatch($match, $exportProcess);
            },
            $filename
        );
    }

    public function replaceMatch($match, ExportProcessInterface $exportProcess): string
    {
        switch ($match['type']) {
            case 'date':
                if (empty($match['modifier'])) {
                    $match['modifier'] = DATE_ATOM;
                }
                $result = $this->timezone->date()->format($match['modifier']);
                break;
            default:
                $result = '';
        }

        return $result;
    }
}
