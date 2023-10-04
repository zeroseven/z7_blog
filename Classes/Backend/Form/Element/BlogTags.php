<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Backend\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Domain\Demand\PostDemand;
use Zeroseven\Z7Blog\Service\RootlineService;
use Zeroseven\Z7Blog\Service\TagService;

class BlogTags extends AbstractFormElement
{

    /** @var string */
    protected $name;

    /** string */
    protected $id;

    /** @var string */
    protected $placeholder;

    /** string */
    protected $value;

    /** int */
    protected $languageUid;

    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $parameterArray = $this->data['parameterArray'] ?? [];
        $placeholder = $parameterArray['fieldConf']['config']['placeholder'] ?? '';
        $sysLanguageUid = $this->data['databaseRow']['sys_language_uid'] ?? 0;

        $this->name = $parameterArray['itemFormElName'] ?? '';
        // @extensionScannerIgnoreLine
        $this->id = $parameterArray['itemFormElID'] ?? '';
        $this->placeholder = strpos($placeholder, 'LLL') === 0 ? $this->getLanguageService()->sL($placeholder) : $placeholder;
        $this->value = $parameterArray['itemFormElValue'] ?? '';
        $this->languageUid = (int)($sysLanguageUid[0] ?? $sysLanguageUid);
    }

    protected function getWhitelist(): string
    {
        $table = $this->data['tableName'] ?? '';
        $uid = $this->data['databaseRow']['uid'] ?? 0;
        $pid = $this->data['databaseRow']['pid'] ?? 0;

        // Create demand object
        $rootPage = RootlineService::getRootPage((int)($table === 'pages' ? $uid : $pid));
        $postDemand = PostDemand::makeInstance()->setCategory($rootPage);

        // Get tags
        $tags = TagService::getTags($postDemand, true, $this->languageUid);

        return json_encode($tags);
    }

    protected function renderHtml(): string
    {

        // Get id of the form field
        $fieldWizardResult = $this->renderFieldWizard();

        // Create form field
        // @extensionScannerIgnoreLine
        $formField = '<input type="text" ' . GeneralUtility::implodeAttributes([
            'name' => $this->name,
            'value' => $this->value,
            'id' => $this->id,
            'placeholder' => $this->placeholder,
            'class' => 'form-control form-control--tags'
        ], true) . ' />';

        // Return html
        return '
            <div class="form-control-wrap">
                <div class="form-wizards-wrap">
                    <div class="form-wizards-element">' . $formField . '</div>
                    <div class="form-wizards-items-bottom">' . ($fieldWizardResult['html'] ?? '') . '</div>
                </div>
            </div>
            <input id="tagify_id" type="hidden" value="' . /** @extensionScannerIgnoreLine */ $this->id . '" />
            <textarea id="tagify_whitelist" hidden>' . $this->getWhitelist() . '</textarea>
        ';
    }

    public function render(): array
    {
        return [
            'html' => $this->renderHtml(),
            'requireJsModules' => JavaScriptModuleInstruction::create('@zeroseven/z7-blog/Tagify-init.js'),
        ];
    }
}
