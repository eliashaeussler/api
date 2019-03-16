<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\FileNotFoundException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database schema console command.
 *
 * This command makes it possible to run several actions according database schema.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class DatabaseSchemaCommand extends BaseCommand
{
    /** @var string Update command action */
    const ACTION_UPDATE = "update";

    /** @var string Drop command action */
    const ACTION_DROP = "drop";

    /** @var array Available command actions */
    const AVAILABLE_ACTIONS = [
        self::ACTION_UPDATE,
        self::ACTION_DROP,
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database:schema")
            ->setDescription(LocalizationUtility::localize("database.schema.description", "console"))
            ->setHelp(LocalizationUtility::localize("database.schema.help", "console"));

        // Arguments
        $this->addArgument(
            "action",
            InputArgument::REQUIRED,
            LocalizationUtility::localize(
                "database.schema.argument_action", "console", null,
                implode("`, `", self::AVAILABLE_ACTIONS)
            )
        );

        // Options
        $this->addOption(
            "schema",
            "s",
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize("database.schema.option_schema", "console")
        );
        $this->addOption(
            "force",
            null,
            InputOption::VALUE_NONE,
            LocalizationUtility::localize("database.schema.option_force", "console", null, self::ACTION_DROP)
        );
        $this->addOption(
            "dry-run",
            null,
            InputOption::VALUE_NONE,
            LocalizationUtility::localize("database.schema.option_dry-run", "console", null, self::ACTION_DROP)
        );
        $this->addOption(
            "fields",
            null,
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize("database.schema.option_fields", "console", null, self::ACTION_DROP),
            false
        );
        $this->addOption(
            "tables",
            null,
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize("database.schema.option_tables", "console", null, self::ACTION_DROP),
            false
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     * @throws DBALException          if the database connection cannot be established
     * @throws FileNotFoundException  if a table schema file is not available
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionService $connectionService */
        $connectionService = GeneralUtility::makeInstance(ConnectionService::class);

        switch ($input->getArgument("action")) {
            //
            // Update database schema
            //
            case self::ACTION_UPDATE:

                // Update database schema
                $connectionService->createSchema($input->getOption("schema"));

                // Show success message
                $this->io->success(LocalizationUtility::localize("database.schema.success_update", "console"));
                break;

            //
            // Drop fields and/or tables in database schema
            //
            case self::ACTION_DROP:

                // Check if dry run is being processed
                $dryRun = $input->getOption("dry-run");

                // Check which database components should be dropped
                $dropFields = $input->getOption("fields") !== false;
                $dropTables = $input->getOption("tables") !== false;
                if (!$dropFields && !$dropTables) {
                    $dropFields = true;
                    $dropTables = true;
                }
                $dropComponentsString = implode(" ", array_filter([
                    $dropFields ? "fields" : "",
                    $dropFields && $dropTables ? "and" : "",
                    $dropTables ? "tables" : "",
                ]));

                // Ask to drop components for security reasons
                if (!$input->getOption("force") && !$dryRun) {
                    $question = LocalizationUtility::localize("database.schema.drop_components", "console", null, $dropComponentsString);
                    if (!$this->io->confirm($question, false)) {
                        return;
                    }
                }

                // Drop database components
                $report = $connectionService->dropUnusedComponents(
                    $dropFields,
                    $dropTables,
                    $input->getOption("schema"),
                    $dryRun
                );

                // Build report
                $droppedTables = [];
                $droppedFields = [];
                if (isset($report["tables"])) {
                    $droppedTables = array_map(function (Table $table) {
                        /* @noinspection RequiredAttributes */
                        return sprintf("<param>%s</>", $table->getName());
                    }, $report["tables"]);
                }
                if (isset($report["fields"])) {
                    array_walk($report["fields"], function (array $components) use (&$droppedFields) {
                        /** @var Table $table */
                        $table = $components["table"];
                        /** @var Column[] $fields */
                        $fields = $components["fields"];
                        foreach ($fields as $field) {
                            /* @noinspection RequiredAttributes */
                            $droppedFields[] = sprintf("%s.<param>%s</>", $table->getName(), $field->getName());
                        }
                    });
                }

                // Show report of dropped components
                if (count($droppedTables) + count($droppedFields) > 0) {
                    if (count($droppedTables) > 0) {
                        $this->io->title($dryRun ? "Tables to drop" : "Dropped tables");
                        $this->io->listing($droppedTables);
                    }
                    if (count($droppedFields) > 0) {
                        $this->io->title($dryRun ? "Fields to drop" : "Dropped fields");
                        $this->io->listing($droppedFields);
                    }
                    if ($dryRun) {
                        $this->io->notice(
                            LocalizationUtility::localize("database.schema.dryRun_message", "console"),
                            LocalizationUtility::localize("database.schema.dryRun_prefix", "console")
                        );
                    }
                } else {
                    if ($dryRun) {
                        $this->io->notice(
                        LocalizationUtility::localize("database.schema.dryRun_result_message", "console"),
                        LocalizationUtility::localize("database.schema.dryRun_result_prefix", "console")
                    );
                    } else {
                        $this->io->success(LocalizationUtility::localize("database.schema.success_drop", "console"));
                    }
                }
                break;
        }
    }
}
