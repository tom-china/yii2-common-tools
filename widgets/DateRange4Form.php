<?php

namespace kriss\widgets;

class DateRange4Form extends DateRangeBase
{
    public $template = '{label}<div class="col-sm-5">{widget}</div>{error}';

    public $labelOptions = ['class' => 'control-label col-sm-2'];

    public $errorOptions = ['class' => 'help-block col-sm-5'];

    public $container = ['class' => 'form-group'];

}