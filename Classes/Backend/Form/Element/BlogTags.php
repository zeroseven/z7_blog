<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Backend\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RootlineService;

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

        $parameterArray = $this->data['parameterArray'];
        $placeholder = $parameterArray['fieldConf']['config']['placeholder'] ?? '';
        $sysLanguageUid = $this->data['databaseRow']['sys_language_uid'];

        $this->name = $parameterArray['itemFormElName'];
        $this->id = $parameterArray['itemFormElID'];
        $this->placeholder = strpos($placeholder, 'LLL') === 0 ? $this->getLanguageService()->sL($placeholder) : $placeholder;
        $this->value = $parameterArray['itemFormElValue'] ?? '';
        $this->languageUid = (int)(is_array($sysLanguageUid) ? $sysLanguageUid[0] : $sysLanguageUid);
    }

    protected function registerJavaScript(): void
    {
        // Get tags
        $rootPage = RootlineService::getRootPage($this->data['tableName'] === 'pages' ? $this->data['databaseRow']['uid'] : $this->data['databaseRow']['pid']);
        $demand = Demand::makeInstance()->setCategory($rootPage);

        // Get tags
        $tags = RepositoryService::getTagRepository()->findAll($demand, true, $this->languageUid);

        // Add JavaScript
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsInlineCode('z7_blog_tags', '
            require(["TYPO3/CMS/Z7Blog/Backend/Tagify"], function(Tagify){
                new Tagify(document.getElementById("' . $this->id . '"), {
                    whitelist: ' . json_encode($tags) . ',
                    originalInputValueFormat: (function (valuesArr) {
                      return valuesArr.map(function (item) {
                        return item.value;
                      }).join(", ").trim();
                    })
                });
            });
        ');
    }

    protected function createFormField(): string
    {
        return '<input type="text" ' . GeneralUtility::implodeAttributes([
                'name' => $this->name,
                'value' => $this->value,
                'id' => $this->id,
                'placeholder' => $this->placeholder,
                'class' => 'form-control form-control--tags'
            ], true) . ' />';
    }

    public function render(): array
    {
        // Get id of the form field
        $fieldWizardResult = $this->renderFieldWizard();

        // Add JavaScript to the backend
        $this->registerJavaScript();

        // Create output
        return ['html' => sprintf('
            <div class="form-control-wrap">
                <div class="form-wizards-wrap">
                    <div class="form-wizards-element">
                        %s
                    </div>
                    <div class="form-wizards-items-bottom">
                        %s
                    </div>
                </div>
            </div>    
        ', $this->createFormField(), $fieldWizardResult['html'])];
    }

}
