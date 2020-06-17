<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Backend\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Tags extends AbstractFormElement
{

    protected function createFormField(string $name, string $id, string $value, string $placeholder = null): string
    {
        return '<input type="text" ' . GeneralUtility::implodeAttributes([
            'name' => $name,
            'value' => $value,
            'id' => $id,
            'placeholder' => $placeholder
        ], true) . ' />';
    }

    public function render(): array
    {
        // Get id of the form field
        $fieldWizardResult = $this->renderFieldWizard();

        // Define parameters
        $field = $this->data['fieldName'];
        $name = $this->data['parameterArray']['itemFormElName'];
        $id = $this->data['parameterArray']['itemFormElID'];
        $placeholder = $this->data['parameterArray']['fieldConf']['config']['placeholder'] ?? '';
        $value = $this->data['databaseRow'][$field] ?? '';

        // Add JavaScript
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsInlineCode('z7_blog_tags', '
            require(["TYPO3/CMS/Z7Blog/Backend/Tagify"], function(Tagify){
                new Tagify(document.getElementById("' . $id . '"), {
                    whitelist : [\'aaa\', \'aaab\', \'aaabb\', \'aaabc\', \'aaabd\', \'aaabe\', \'aaac\', \'aaacc\'],
                    originalInputValueFormat: (function (valuesArr) {
                      return valuesArr.map(function (item) {
                        return item.value;
                      }).join(\', \').trim();
                    })
                });
            });
        ');

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
        ', $this->createFormField($name, $id, $value, $placeholder), $fieldWizardResult['html'])];

    }

}
