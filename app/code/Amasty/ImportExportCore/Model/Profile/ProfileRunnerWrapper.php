<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Profile;

use Amasty\ImportExportCore\Api\Process\StatusCheckerInterface;
use Amasty\ImportExportCore\Api\Profile\ProfileRunnerInterface;
use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface;
use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterfaceFactory;
use Amasty\ImportExportCore\Model\Cli\CliAreaFlag;
use Laminas\Http\Response;
use Magento\Framework\AuthorizationInterface;

class ProfileRunnerWrapper implements ProfileRunnerWrapperInterface
{
    /**
     * @var RunProfileResponseInterfaceFactory
     */
    private $runProfileResponseFactory;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var CliAreaFlag
     */
    private $cliAreaFlag;

    /**
     * @var ProfileRunnerInterface
     */
    private $profileRunner;

    /**
     * @var StatusCheckerInterface
     */
    private $statusChecker;

    /**
     * @var string
     */
    private $authorizationResource;

    public function __construct(
        RunProfileResponseInterfaceFactory $runProfileResponseFactory,
        AuthorizationInterface $authorization,
        CliAreaFlag $cliAreaFlag,
        ProfileRunnerInterface $profileRunner,
        StatusCheckerInterface $statusChecker,
        string $authorizationResource
    ) {
        $this->runProfileResponseFactory = $runProfileResponseFactory;
        $this->authorization = $authorization;
        $this->profileRunner = $profileRunner;
        $this->cliAreaFlag = $cliAreaFlag;
        $this->statusChecker = $statusChecker;
        $this->authorizationResource = $authorizationResource;
    }

    public function run(int $profileId): RunProfileResponseInterface
    {
        if (!$this->cliAreaFlag->isCliArea() && !$this->authorization->isAllowed($this->authorizationResource)) {
            return $this->buildResponse(
                Response::STATUS_CODE_403,
                __('The consumer isn\'t authorized to access %1', $this->authorizationResource)->render()
            );
        }

        if (!$profileId) {
            return $this->buildResponse(
                Response::STATUS_CODE_400,
                __('Please specify the profile ID')->render()
            );
        }

        try {
            $processIdentity = $this->profileRunner->manualRun($profileId);
        } catch (\Exception $e) {
            return $this->buildResponse(
                Response::STATUS_CODE_500,
                $e->getMessage()
            );
        }

        return $this->buildResponse(
            Response::STATUS_CODE_200,
            __('The process has been started.')->render(),
            $processIdentity
        );
    }

    public function check(string $identity): RunProfileResponseInterface
    {
        $status = $this->statusChecker->check($identity);
        if ($status->getError()) {
            $responseMsg = $status->getError();
        } else {
            $responseMsg = __(
                'Current status: %1; Processed Records: %2/%3',
                $status->getStatus(),
                $status->getProceed(),
                $status->getTotal()
            )->render();
        }

        return $this->buildResponse(
            Response::STATUS_CODE_200,
            $responseMsg,
            $identity
        );
    }

    protected function buildResponse(
        int $statusCode,
        string $message,
        ?string $identity = null
    ): RunProfileResponseInterface {
        $runProfileResponse = $this->runProfileResponseFactory->create();
        $runProfileResponse->setStatusCode($statusCode);
        $runProfileResponse->setMessage($message);
        $runProfileResponse->setIdentity($identity);

        return $runProfileResponse;
    }
}
