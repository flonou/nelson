<?php

namespace spec\Akeneo\Crowdin;

use Akeneo\Crowdin\Api\UpdateFile;
use Akeneo\Crowdin\Client;
use Akeneo\System\TargetResolver;
use Akeneo\System\TranslationFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class TranslationFilesUpdaterSpec extends ObjectBehavior
{
    function let(
        Client $client,
        LoggerInterface $logger,
        TargetResolver $resolver
    ) {
        $this->beConstructedWith($client, $logger, $resolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\Crowdin\TranslationFilesUpdater');
    }

    function it_should_update_files(
        $client,
        $resolver,
        TranslationFile $file,
        UpdateFile $updateFileApi
    ) {
        $client->api('update-file')->willReturn($updateFileApi);
        $updateFileApi->setBranch('master')->shouldBeCalled();
        $file->getProjectDir()->willReturn('/tmp/');
        $file->getSource()->willReturn('/tmp/src/fr.yml');
        $file->getPattern()->willReturn('Project/src/fr.yml');
        $resolver->getTarget('/tmp/', '/tmp/src/fr.yml')->willReturn('fr.yml');

        $updateFileApi->addTranslation('/tmp/src/fr.yml', 'fr.yml', 'Project/src/fr.yml')->shouldBeCalled();
        $updateFileApi->execute()->shouldBeCalled();

        $this->update([$file], 'master');
    }
}
