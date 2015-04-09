<?php

namespace T4webCalendar\Calendar\InputFilter;

use T4webBase\InputFilter\InputFilter;
use T4webBase\InputFilter\Element\Id;
use T4webBase\InputFilter\Element\Text;
use T4webBase\InputFilter\Element\Date;

class Create extends InputFilter
{

    public function __construct()
    {

        // id
        $id = new Id('id');
        $id->setRequired(false);
        $this->add($id);

        // name
        $name = new Text('name', 0, 250);
        $name->setRequired(false);
        $this->add($name);

        // date
        $date = new Date('date', 'Y-m-d');
        $date->setRequired(true);
        $this->add($date);

    }
}
