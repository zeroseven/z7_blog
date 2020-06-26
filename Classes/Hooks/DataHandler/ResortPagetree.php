<?php

namespace Zeroseven\Z7Blog\Hooks\DataHandler;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Zeroseven\Z7Blog\Domain\Model\Post;

class ResortPagetree
{

    protected const TABLE = 'pages';

    protected const SORTING_FIELD = 'post_date';

    public function processDatamap_afterAllOperations(&$dataHanlder): void
    {
        foreach ($dataHanlder->datamap as $table => $uids) {
            if ($table === self::TABLE) {
                foreach ($uids as $uid => $data) {
                    if ((int)$uid && (int)$data['doktype'] === Post::DOKTYPE) {

                        // Get data of given page
                        $pid = (int)BackendUtility::getRecord(self::TABLE, $uid, 'pid')['pid'];

                        // Send message about the sorting
                        if ($this->sortItemsOnPid($pid, $dataHanlder)) {

                            $parentRow = BackendUtility::getRecord(self::TABLE, $pid);

                            $message = GeneralUtility::makeInstance(FlashMessage::class,
                                LocalizationUtility::translate(
                                    'LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:notification.resortPagetree.description',
                                    'z7_blog',
                                    [0 => BackendUtility::getRecordTitle(self::TABLE, $parentRow)]
                                ),
                                LocalizationUtility::translate(
                                    'LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:notification.resortPagetree.title',
                                    'z7_blog'
                                ),
                                FlashMessage::INFO,
                                true
                            );

                            $messageQueue = GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier();
                            $messageQueue->enqueue($message);
                        }
                    }
                }
            }
        }
    }

    protected function sortItemsOnPid(int $pid, DataHandler $dataHandler): bool
    {

        // Get siblings of the current page by the sorting of the sortingField
        $pages = $this->getSubpagesUids($pid, self::SORTING_FIELD);

        // Compare the "new" sorting with current sorting of pages
        if (implode('', $pages) === implode('', $this->getSubpagesUids($pid, 'sorting', true))) {
            return false;
        }

        // Create command to sort the pages
        $command = [];
        if ($pages) {
            foreach ($pages as $uid) {
                $command[self::TABLE][$uid]['move'] = $pid;
            }
        }

        // Execute
        $dataHandler->start([], $command);
        $dataHandler->process_cmdmap();

        // Update page tree
        BackendUtility::setUpdateSignal('updatePageTree');

        return true;
    }

    protected function getSubpagesUids(int $parentPage, string $orderBy, bool $reverseDirection = null): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);

        // Do not use enabled fields here
        $queryBuilder->getRestrictions()->removeAll();

        // Set table and where clause
        $x = $queryBuilder
            ->select('uid')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($parentPage, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq($GLOBALS['TCA']['pages']['ctrl']['delete'], $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ->orderBy($orderBy, $reverseDirection ? 'DESC' : 'ASC')
            ->execute();

        // Collect uid's
        $uids = [];
        while ($row = $x->fetch()) {
            $uids[] = $row['uid'];
        }

        return $uids;
    }

}