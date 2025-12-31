<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Entity\Rule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Command to migrate YAML rule definitions into the database.
 *
 * Scans src/AppBundle/Resources/rules/ for YAML files and imports
 * each rule into the Rule entity table. Supports updating existing
 * rules by ID and dry-run mode for previewing changes.
 */
class MigrateRulesCommand extends Command
{
    protected static $defaultName = 'app:rules:migrate';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $rulesPath;

    public function __construct(EntityManagerInterface $entityManager, string $kernelProjectDir)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->rulesPath = $kernelProjectDir . '/src/AppBundle/Resources/rules';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migrate YAML rule definitions into the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview changes without persisting')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Update existing rules (by ID)')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command scans the rules directory and imports
rule definitions into the database.

    <info>php %command.full_name%</info>

Use --dry-run to preview changes:

    <info>php %command.full_name% --dry-run</info>

Use --force to update existing rules:

    <info>php %command.full_name% --force</info>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');

        $io->title('Migrating Rules to Database');

        if ($dryRun) {
            $io->note('Dry-run mode enabled - no changes will be persisted');
        }

        if (!is_dir($this->rulesPath)) {
            $io->error(sprintf('Rules directory not found: %s', $this->rulesPath));
            return Command::FAILURE;
        }

        $finder = new Finder();
        $finder->files()->in($this->rulesPath)->name('*.yml')->name('*.yaml');

        if (!$finder->hasResults()) {
            $io->warning('No YAML files found in rules directory');
            return Command::SUCCESS;
        }

        $ruleRepository = $this->entityManager->getRepository(Rule::class);
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($finder as $file) {
            $io->section(sprintf('Processing: %s', $file->getFilename()));

            try {
                $content = Yaml::parseFile($file->getRealPath());
            } catch (\Exception $e) {
                $io->error(sprintf('Failed to parse %s: %s', $file->getFilename(), $e->getMessage()));
                $stats['errors']++;
                continue;
            }

            if (!isset($content['rules']) || !is_array($content['rules'])) {
                $io->warning(sprintf('No rules array found in %s', $file->getFilename()));
                continue;
            }

            foreach ($content['rules'] as $ruleData) {
                $result = $this->processRule($ruleData, $ruleRepository, $force, $dryRun, $io);
                $stats[$result]++;
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->newLine();
        $io->success(sprintf(
            'Migration complete: %d created, %d updated, %d skipped, %d errors',
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
            $stats['errors']
        ));

        return $stats['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Process a single rule definition.
     *
     * @param array $ruleData
     * @param object $repository
     * @param bool $force
     * @param bool $dryRun
     * @param SymfonyStyle $io
     * @return string Result key: created, updated, skipped, errors
     */
    private function processRule(
        array $ruleData,
        $repository,
        bool $force,
        bool $dryRun,
        SymfonyStyle $io
    ): string {
        // Validate required fields
        $requiredFields = ['id', 'name', 'type', 'description', 'condition', 'severity', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($ruleData[$field])) {
                $io->warning(sprintf('Rule missing required field: %s', $field));
                return 'errors';
            }
        }

        $ruleId = $ruleData['id'];
        $existingRule = $this->findRuleByYamlId($repository, $ruleId);

        if ($existingRule && !$force) {
            $io->text(sprintf('  <comment>Skipped:</comment> %s (already exists, use --force to update)', $ruleId));
            return 'skipped';
        }

        // Build the YAML definition string (the full rule as stored)
        $definitionYaml = Yaml::dump($ruleData, 4, 2);

        if ($existingRule) {
            // Update existing rule
            $existingRule->setType($ruleData['type']);
            $existingRule->setDescription($ruleData['description']);
            $existingRule->setDefinitionYaml($definitionYaml);
            $existingRule->setActive($ruleData['enabled'] ?? true);

            $io->text(sprintf('  <info>Updated:</info> %s - %s', $ruleId, $ruleData['name']));
            return 'updated';
        }

        // Create new rule
        $rule = new Rule();
        $rule->setType($ruleData['type']);
        $rule->setDescription($ruleData['description']);
        $rule->setDefinitionYaml($definitionYaml);
        $rule->setActive($ruleData['enabled'] ?? true);

        if (!$dryRun) {
            $this->entityManager->persist($rule);
        }

        $io->text(sprintf('  <info>Created:</info> %s - %s', $ruleId, $ruleData['name']));
        return 'created';
    }

    /**
     * Find an existing rule by the YAML id field stored in definition_yaml.
     *
     * @param object $repository
     * @param string $yamlId
     * @return Rule|null
     */
    private function findRuleByYamlId($repository, string $yamlId): ?Rule
    {
        // Search for rules where definition_yaml contains the id
        $rules = $repository->findAll();

        foreach ($rules as $rule) {
            $definition = Yaml::parse($rule->getDefinitionYaml());
            if (isset($definition['id']) && $definition['id'] === $yamlId) {
                return $rule;
            }
        }

        return null;
    }
}
