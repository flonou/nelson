<?php

namespace Akeneo\Crowdin;

use Akeneo\Crowdin\Api\AddFile;
use Akeneo\System\TargetResolver;
use Akeneo\System\TranslationFile;
use Psr\Log\LoggerInterface;

/**
 * This class creates all the missing files of a Crowdin project.
 *
 * @author    Pierre Allard <pierre.allard@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TranslationFilesCreator
{
    const MAX_UPLOAD = 10;

    /** @var Client */
    protected $client;

    /** @var LoggerInterface */
    protected $logger;

    /** @var TargetResolver */
    protected $targetResolver;

    /**
     * @param Client          $client
     * @param LoggerInterface $logger
     */
    public function __construct(Client $client, LoggerInterface $logger, TargetResolver $targetResolver)
    {
        $this->client         = $client;
        $this->logger         = $logger;
        $this->targetResolver = $targetResolver;
    }

    /**
     * Create the missing files in Crowdin. Set it by batch using MAX_UPLOAD const.
     *
     * @param TranslationFile[]      $files
     * @param TranslationProjectInfo $projectInfo
     * @param string                 $baseBranch
     */
    public function create(array $files, TranslationProjectInfo $projectInfo, $baseBranch)
    {
        $existingFiles = $projectInfo->getExistingFiles($baseBranch);
        $fileSets = array_chunk($this->filterExistingFiles($files, $existingFiles), self::MAX_UPLOAD);

        foreach ($fileSets as $fileSet) {
            /** @var AddFile $service */
            $service = $this->client->api('add-file');
            $service->setBranch($baseBranch);

            foreach ($fileSet as $file) {
                /** @var TranslationFile $file */
                $target = $this->targetResolver->getTarget(
                    $file->getProjectDir(),
                    $file->getSource()
                );
                $service->addTranslation($file->getSource(), $target, $file->getPattern());
                $this->logger->info(sprintf('Create file "%s"', $target));
            }
            $service->execute();
        }
    }

    /**
     * @param TranslationFile[] $files
     * @param string[]          $existingFiles
     *
     * @return TranslationFile[]
     */
    protected function filterExistingFiles($files, $existingFiles)
    {
        $result = [];

        foreach ($files as $file) {
            if (!in_array($this->targetResolver->getTarget(
                $file->getProjectDir(),
                $file->getSource()
            ), $existingFiles)) {
                $result[] = $file;
            }
        }

        return $result;
    }
}
