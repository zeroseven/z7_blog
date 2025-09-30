<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Upgrade;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Update script to clean up FlexForm data in the database:
 * - Remove deprecated TCEforms tags from stored FlexForm XML data
 * - Ensure compatibility with TYPO3 v13+
 */
#[UpgradeWizard('z7BlogFlexFormCleanup')]
final class FlexFormCleanupUpdater implements UpgradeWizardInterface
{
    private const EXTENSION_KEY = 'z7_blog';
    private const CTyeList = ['z7blog_list', 'z7blog_static', 'z7blog_filter', 'z7blog_authors'];
    public function __construct(private readonly \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool)
    {
    }
    /**
     * Get title
     */
    public function getTitle(): string
    {
        return 'Z7 Blog: Clean up FlexForm data';
    }

    /**
     * Get description
     */
    public function getDescription(): string
    {
        return 'This update script removes deprecated TCEforms tags from FlexForm data stored in the database ' .
               'to ensure compatibility with TYPO3 v13+.';
    }

    /**
     * Execute the database update replacing deprecated fragments in FlexForm XML
     */
    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();

        $result = $queryBuilder
            ->select('uid', 'pi_flexform', 'CType')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->in(
                    'CType',
                    self::CTyeList
                ),
                $queryBuilder->expr()->neq('pi_flexform', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%<TCEforms>%'))
                )
            )
            ->executeQuery();

        $updated = 0;
        while ($row = $result->fetchAssociative()) {
            $originalXml = (string)($row['pi_flexform'] ?? '');
            if ($originalXml === '') {
                continue;
            }

            $cleanedXml = $this->cleanupFlexFormXml($originalXml);
            if ($cleanedXml !== $originalXml) {
                $connection->update(
                    'tt_content',
                    [
                        'pi_flexform' => $cleanedXml,
                        'tstamp' => time(),
                    ],
                    ['uid' => (int)$row['uid']]
                );
                $updated++;
            }
        }

        return $updated > 0;
    }

    /**
     * Determine if there are any rows requiring an update
     */
    public function updateNecessary(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();

        $count = (int)$queryBuilder
            ->selectLiteral('COUNT(*)')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->in(
                    'CType',
                    $queryBuilder->createNamedParameter(self::CTyeList, Connection::PARAM_INT_ARRAY)
                ),
                $queryBuilder->expr()->neq('pi_flexform', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%<TCEforms>%')),
                )
            )
            ->executeQuery()
            ->fetchOne();

        return $count > 0;
    }

    /**
     * No prerequisites
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * Get all table/field combinations this updater handles
     */
    public function getFieldsToUpdate(): array
    {
        return [
            'tt_content' => ['pi_flexform'],
        ];
    }

    /**
     * Check if this row needs updating
     */
    public function hasPotentialUpdateForTable(string $tableName): bool
    {
        return $tableName === 'tt_content';
    }

    /**
     * Check if a specific row needs updating
     */
    public function updateTableRow(string $tableName, array $row): array
    {
        if ($tableName !== 'tt_content' || empty($row['pi_flexform'])) {
            return $row;
        }

        // Check if this is one of our blog content types
        if (!in_array($row['CType'], self::CTyeList, true)) {
            return $row;
        }

        $originalXml = $row['pi_flexform'];
        $cleanedXml = $this->cleanupFlexFormXml($originalXml);

        // Only update if changes were made
        if ($cleanedXml !== $originalXml) {
            $row['pi_flexform'] = $cleanedXml;
        }

        return $row;
    }

    /**
     * Clean up FlexForm XML data
     */
    protected function cleanupFlexFormXml(string $xml): string
    {
        if (empty($xml)) {
            return $xml;
        }

        // Remove TCEforms tags and move content up one level
        $xml = $this->removeTceFormsTags($xml);

        return $xml;
    }

    /**
     * Remove deprecated TCEforms tags from FlexForm XML
     */
    protected function removeTceFormsTags(string $xml): string
    {
        // Pattern to match TCEforms blocks and extract their content
        $pattern = '/<TCEforms>\s*(<label>.*?<\/label>)?\s*(<config>.*?<\/config>)\s*<\/TCEforms>/s';

        $xml = preg_replace_callback($pattern, function ($matches) {
            $content = '';

            // Add label if it exists
            if (!empty($matches[1])) {
                $content .= $matches[1];
            }

            // Add config
            if (!empty($matches[2])) {
                $content .= $matches[2];
            }

            return $content;
        }, $xml);

        return $xml;
    }
}
