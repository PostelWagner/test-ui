<?php

namespace app\assets;

use yii\web\AssetBundle;

class TypeaheadAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $js = [
        'typeahead.js/dist/typeahead.jquery.min.js',
        'typeahead.js/dist/bloodhound.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
