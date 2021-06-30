<?php
namespace Stack;

class Stack {

    private $stack;

    function __construct() {
        $this->initStack();
    }

    private function initStack() : void {
        $this->stack = Array(); 
    }

    public function push(string $element) : void {
        $this->stack[] = $element; 
    }
    
    public function pop() : string {
        $element = array_pop($this->stack);
        
        return $element;
    }

    public function getSize() : int {
        $size = count($this->stack);

        return $size;
    }

    public function isEmpty() : bool {
        if (empty($this->stack))    return TRUE;

        return FALSE;
    }

    public function getTop() : ?string {
        if ($this->isEmpty() === TRUE)   return NULL;

        $size = $this->getSize();

        return $this->stack[$size-1];
    }

}

?>