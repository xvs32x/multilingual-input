<?php

namespace xvs32x\MInput;

use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;

/**
 * @property array $languagesList
 * @property string $widgetNormalize
 * */
class MInput extends InputWidget
{
    public $widget;
    public $widgetOptions = ['class' => 'form-control'];
    public $labelOptions = ['class' => 'control-label'];
    public $htmlLibrary = 'yii\bootstrap\Html';
    public $closeTag = 'div';
    public $openTag = 'div';
    public $openTagOptions = ['class' => 'form-group'];
    public $helpBlock;
    public $languages = [];//List of all available languages
    public $defaultLanguage;//Default language

    public function init()
    {
        if (!$this->languages) {
            $this->languages = ArrayHelper::getValue(Yii::$app->params, 'languages');
        }
        if (!$this->defaultLanguage) {
            $this->defaultLanguage = ArrayHelper::getValue(Yii::$app->params, 'defaultLanguage');
        }
        if (!$this->helpBlock) {
            $this->helpBlock = Html::tag('div', null, ['class' => 'help-block']);
        }
    }

    /**
     * Return not default languages
     * @return array
     * */
    public function getLanguagesList()
    {
        $languages = $this->languages;
        if (ArrayHelper::getValue($languages, $this->defaultLanguage)) {
            unset($languages[$this->defaultLanguage]);
        } else {
            if ($key = ArrayHelper::isIn($this->defaultLanguage, $languages)) {
                unset($languages[$key]);
            }
        }
        return $languages;
    }

    /**
     * Return input view
     * @param string $code
     * @return string
     * */
    public function getWidgetNormalize($code = null)
    {
        $attribute = $this->attribute . ($code ? '_' . $code : null);
        $widget = $this->widget;
        $openTagOptions = $this->openTagOptions;
        Html::addCssClass($openTagOptions, 'field-' . Html::getInputId($this->model, $attribute));
        $result = [
            Html::beginTag($this->openTag, $openTagOptions),
            Html::activeLabel($this->model, $attribute, $this->labelOptions),
        ];
        if (class_exists($widget)) {
            /**@var \yii\base\Widget $widget */
            $result[] = $widget::widget([
                'model' => $this->model,
                'attribute' => $attribute,
                'options' => $this->widgetOptions
            ]);
        } else {
            $result[] = call_user_func_array($this->htmlLibrary . '::' . $widget, [
                'model' => $this->model, 'attribute' => $attribute, 'options' => $this->widgetOptions
            ]);
        }
        $result[] = $this->helpBlock;
        $result[] = Html::endTag($this->closeTag);
        return implode('', $result);
    }

    /**
     * @inheritdoc
     * */
    public function run()
    {
        $result[] = $this->widgetNormalize;//input for default language
        foreach ($this->languagesList as $code => $language) {
            $result[] = $this->getWidgetNormalize($code);
        }
        return implode('', $result);
    }


}