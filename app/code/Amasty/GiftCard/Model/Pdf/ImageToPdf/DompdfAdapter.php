<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Pdf\ImageToPdf;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfAdapter implements ImageToPdfAdapterInterface
{
    public const ORIGINAL_DPI = 72;
    public const DEFAULT_FONT = 'Helvetica';
    public const DEFAULT_SIZE = [0.0, 0.0, ImageInterface::DEFAULT_HEIGHT, ImageInterface::DEFAULT_WIDTH];

    public function render(string $imageHtml, ?array $size = null): string
    {
        if (!class_exists(Dompdf::class)) {
            throw new \RuntimeException(
                '\'dompdf/dompdf\' library not found. '
                . 'Please run \'composer require dompdf/dompdf\' command to install it.'
            );
        }
        $options = new Options();
        $options->set('defaultFont', self::DEFAULT_FONT);
        $options->set('dpi', self::ORIGINAL_DPI);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($imageHtml);
        $dompdf->setPaper($size ?? self::DEFAULT_SIZE, 'landscape');

        $css = $dompdf->getCss();
        $style = $css->create_style();
        $style->set_prop('margin', 0);
        $css->add_style('html', $style);
        $dompdf->setCss($css);

        $dompdf->render();

        return $dompdf->output();
    }
}
