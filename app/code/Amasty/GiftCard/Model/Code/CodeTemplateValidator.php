<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Code;

use Magento\Framework\Exception\LocalizedException;

class CodeTemplateValidator
{
    public const MASK = [
        // no "0" and "1" as they are confusing
        '{D}' => [2, 3, 4, 5, 6, 7, 8, 9],
        // no I, Q and O as they are confusing
        '{L}' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
                  'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    ];

    /**
     * @var Repository
     */
    private $codeRepository;

    public function __construct(
        Repository $codeRepository
    ) {
        $this->codeRepository = $codeRepository;
    }

    public function getCodesByTemplate(string $template): array
    {
        $dbTemplate = $template;
        foreach (self::MASK as $placeholder => $values) {
            $dbTemplate = str_replace($placeholder, "_", $dbTemplate);
        }

        return $this->codeRepository->getCodesByTemplate($dbTemplate);
    }

    /**
     * @throws LocalizedException
     */
    public function validateTemplate(string $template, int $qty): void
    {
        if (false === strpos($template, '{L}') && false === strpos($template, '{D}')) {
            throw new LocalizedException(__('Please add {L} or {D} placeholders into the template "%1"', $template));
        }

        $templateCodesQty = $this->getTemplateAvailableCodesQty($template);
        if ($qty > $templateCodesQty) {
            throw new LocalizedException(__('Maximum number of code combinations for the current template is %1,
            please update Quantity field accordingly.', $templateCodesQty));
        }
    }

    public function getTemplateMasksList(string $template): array
    {
        $templateMasksList = [];
        $masks = array_map(
            static function ($value) {
                return preg_quote($value, '/');
            },
            array_keys(self::MASK)
        );

        $regExpTemplate = implode('|', $masks);
        if (preg_match_all('/' . $regExpTemplate . '/', $template, $matches)) {
            $templateMasksList = $matches[0];
        }

        return $templateMasksList;
    }

    public function getTemplateAvailableCodesQty(string $template): float
    {
        return (float)($this->getTemplateMaxCodesQty($template) - $this->getTemplateExistingCodesQty($template));
    }

    private function getTemplateMaxCodesQty(string $template): float
    {
        $maxQty = 1;

        foreach (self::MASK as $placeholder => $values) {
            $allValuesCount = count($values);
            $templateValuesCount = substr_count($template, $placeholder);
            $maxQty *= $allValuesCount ** $templateValuesCount;
        }

        return (float)$maxQty;
    }

    /**
     * @param string $template
     *
     * @return int
     */
    private function getTemplateExistingCodesQty(string $template): int
    {
        foreach (self::MASK as $placeholder => $values) {
            $template = str_replace($placeholder, "_", $template);
        }

        return $this->codeRepository->getCodesCountByTemplate($template);
    }
}
