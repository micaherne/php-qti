<?php

namespace PHPQTI\Runtime\Element;

/**
 * Base class for element classes
 * 
 * @author Michael Aherne
 *
 */
abstract class Element {

    public $attrs;
    public $children;

    public function __construct($attrs, $children) {
        $this->attrs = $attrs;
        $this->children = $children;
    }
    
    public function __invoke($controller) {
        $result = '<span class="' . $this->cssClass() . '">';
        foreach($this->children as $child) {
            $result .= $child->__invoke($controller);
        }
        $result .= "</span>";
        return $result;
    }
    
    public function cssClass() {
        $nsparts = explode('\\', get_class($this));
        return 'qti_' . lcfirst(array_pop($nsparts));
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
        foreach($this->attrs as $attr => $val) {
            if (is_null($attrsRequested) || in_array($attr, $attrsRequested)) {
                $result[] = "data-{$attr}=\"{$val}\"";
            }
        }
        return $result;
    }

}