<?php

namespace xvs32x\MInput;

use Yii;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;

/**
 * @property array $languagesList
 * @property string $widgetNormalize
 * */
class MInput extends InputWidget
{
    public $form;
    public $widget;
    public $widgetOptions = ['class' => 'form-control'];
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
        /** @var \nagser\base\widgets\ActiveForm\ActiveForm $form */
        $form = $this->form;
        $attribute = $this->attribute . ($code ? '_' . $code : null);
        $widget = $this->widget;
        if (class_exists($widget)) {
            /**@var \yii\base\Widget $widget */
            return $form->field($this->model, $attribute)->widget($widget, $this->widgetOptions);
        } else {
            return $form->field($this->model, $attribute)->$widget($this->widgetOptions);
        }
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