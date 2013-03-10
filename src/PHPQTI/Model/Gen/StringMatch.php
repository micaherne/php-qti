<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class StringMatch extends AbstractClass {

    protected $_elementName = 'stringMatch';

    public $caseSensitive;
    public $substring;

}