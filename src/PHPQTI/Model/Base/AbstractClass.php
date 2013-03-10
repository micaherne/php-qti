<?php

namespace PHPQTI\Model\Base;

/**
 * The base class for all QTI classes
 * 
 * @author Michael Aherne
 *
 */
abstract class AbstractClass {
    
    protected $_elementName;
    
    protected $_parent = null;
    protected $_children = array();
    
    public function __construct($attrs = null, $children = null) {
        if (is_null($children)) {
            // Never let children be null so we can always iterate over them
            $this->_children = array();
        } else {
            $this->_children = $children;
        }
        if (!is_null($this->_children)) {
            foreach($this->_children as $child) {
                $child->_parent = $this;
            }
        }
        if (!is_null($attrs)) {
            foreach($attrs as $attr => $val) {
                $this->$attr = $val; // TODO: This is a bit dodgy!
            }
        }
    }
    
    public function addChild(AbstractClass $child) {
        $this->_children[] = $child;
        $child->_parent = $this;
    }
    
    public function getChildren($class = null) {
        if (is_null($class)) {
            return $this->_children;
        } else {
            $result = array();
            foreach($this->_children as $child) {
                if (is_a($child, $class)) {
                    $result[] = $child;
                }
            }
            return $result;
        }
    }
    
    /**
     * Default execution method.
     * 
     * For bodyElements this simply creates an HTML element with the same name
     * as the original XML class and copies the attributes to it.
     * 
     * For expressions this throws a @NotImplementedException
     * 
     * For any other type of class a @NotImplementedException is also thrown.
     */
    public function __invoke($controller) {
        $r = new \ReflectionClass(get_class($this));
        
        if ($r->implementsInterface('PHPQTI\Model\Base\BodyElement')) {
            $parts = array("<{$this->_elementName} ");
            foreach(get_object_vars($this) as $name => $value) {
                if (strpos($name, '_') === 0) {
                    continue;
                }
                if (!is_null($value)) {
                    $parts[] = $name . '="' . str_replace('"', '&quot;', htmlspecialchars($value)) . '"';
                }
            }
            $parts[] = '>';
            foreach($this->_children as $child) {
                $c = $child($controller);
                $parts[] = $c;
            }
            $parts[] = "</{$this->_elementName}>";
            
            return implode('', $parts);
        }
        
        throw new \Exception(get_class($this) . ' not supported');
    }
    
    public function getElementName() {
        return $this->_elementName;
    }
    
    public function cssClass() {
        return 'qti_' . $this->_elementName;
    }
    
    /**
     * Get all the QTI attributes of this class
     * 
     * @return array associative array of attribute name to value
     */
    public function getAttributes() {
        $result = array();
        foreach(get_object_vars($this) as $name => $value) {
            // TODO: Check for public visibility rather than rely on naming convention
            if (strpos($name, '_') === 0) {
                continue;
            }
            $result[$name] = $value;
        }
        return $result;
    }

    /** Convenience method to get all attributes as an array of HTML5 data attributes
     *
     * Used by things like SliderInteraction
     *
     * @param array $attrsRequested a whitelist of attributes to return
     * @return array HTML5 data attributes for this element
     */
    public function _getDataAttributes($attrsRequested=null) {
        $result = array();
        foreach(get_object_vars($this) as $name => $value) {
            if (strpos($name, '_') === 0) {
                continue;
            }
            if (is_null($attrsRequested) || in_array($name, $attrsRequested)) {
                $result[] = "data-{$name}=\"{$value}\"";
            }
        }
        return $result;
    }
    
    public function findAncestorWithInterface($interface) {
        $r = new \ReflectionClass(get_class($this));
        
        if ($r->implementsInterface($interface)) {
            return $this;
        } else if (is_null($this->_parent)) {
            return null;
        } else {
            return $this->_parent->findAncestorWithInterface($interface);
        }
    }
}