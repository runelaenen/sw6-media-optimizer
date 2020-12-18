<?php declare(strict_types=1);

namespace RuneLaenen\MediaOptimizer\Command;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\Pathname\UrlGeneratorInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Spatie\ImageOptimizer\OptimizerChain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MediaOptimizeCommand extends Command
{
    protected static $defaultName = 'rl:media:optimize';

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var OptimizerChain
     */
    private $optimizerChain;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var int
     */
    private $sizePre = 0;

    /**
     * @var int
     */
    private $sizePost = 0;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(
        EntityRepositoryInterface $mediaRepository,
        OptimizerChain $optimizerChain,
        UrlGeneratorInterface $urlGenerator,
        string $projectDir
    ) {
        parent::__construct();
        $this->mediaRepository = $mediaRepository;
        $this->optimizerChain = $optimizerChain;
        $this->urlGenerator = $urlGenerator;
        $this->projectDir = $projectDir;
    }

    public function configure(): void
    {
        $this->addOption('info')
            ->addOption(
                'batch-size',
                'b',
                InputOption::VALUE_REQUIRED,
                'Number of entities per iteration',
                '50'
            );
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('info')) {
            $optimizers = $this->optimizerChain->getOptimizers();

            $rows = [];
            foreach ($optimizers as $optimizer) {
                $rows[] = [
                    $optimizer->binaryName(),
                ];
            }

            $table = new Table($output);
            $table->setHeaders([
                'Optimizer',
            ])
            ->setRows($rows);
            $table->render();

            return 0;
        }
        $this->batchSize = $this->getBatchSizeFromInput($input);
        $context = Context::createDefaultContext();

        $mediaIterator = new RepositoryIterator($this->mediaRepository, $context, $this->createCriteria());
        $totalMediaCount = $mediaIterator->getTotal();

        $progressBar = new ProgressBar($output, $totalMediaCount);
        $progressBar->start();

        while (($result = $mediaIterator->fetch()) !== null) {
            foreach ($result->getEntities() as $media) {
                $this->optimize($media, $context);
                $progressBar->advance();
            }
        }

        $progressBar->finish();

        $output->writeln([
            '',
            'Filesize (bytes) before optimalization: ' . $this->sizePre,
            'Filesize (bytes) after optimalization: ' . $this->sizePost,
            'Bytes saved: ' . ($this->sizePre - $this->sizePost) . ' (' . (round((1 - $this->sizePre / $this->sizePost) * 100, 2)) . '%)',
        ]);

        return 0;
    }

    private function getBatchSizeFromInput(InputInterface $input): int
    {
        $rawInput = $input->getOption('batch-size');

        if (\is_array($rawInput) || !is_numeric($rawInput)) {
            throw new \UnexpectedValueException('Batch size must be numeric');
        }

        return (int) $rawInput;
    }

    private function createCriteria(): Criteria
    {
        $criteria = new Criteria();
        $criteria->setOffset(0);
        $criteria->setLimit($this->batchSize);
        $criteria->addFilter(new EqualsFilter('media.mediaFolder.configuration.createThumbnails', true));
        $criteria->addAssociation('thumbnails');
        $criteria->addAssociation('mediaFolder.configuration.mediaThumbnailSizes');

        return $criteria;
    }

    private function optimize(MediaEntity $media): void
    {
        $mediaLocation = $this->projectDir . '/public/' . $this->urlGenerator->getRelativeMediaUrl($media);
        if (!file_exists($mediaLocation)) {
            return;
        }

        $sizePre = filesize($mediaLocation);
        $this->sizePre += $sizePre;

        $this->optimizerChain->optimize($mediaLocation);

        $sizePost = filesize($mediaLocation);
        $this->sizePost += $sizePost;
    }
}
