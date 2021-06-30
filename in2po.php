<?php
declare(strict_types=1);
ini_set("register_argc_argv", "1");
require_once("Stack.php");

define("PRECEDENCE_NOT_A_OPERATOR",    -1);
define("PRECEDENCE_RIGHT_PARENTHESIS",  0);
define("PRECEDENCE_LEFT_PARENTHESIS",   1);
define("PRECEDENCE_ADD_SUB_OPERATOR",   2);
define("PRECEDENCE_MULTI_DIV_OPERATOR", 3);
define("PRECEDENCE_EXP_OPERATOR",       4);

use Stack\Stack;

function getElementPrecedence(string $operator): int {
    $precedence = PRECEDENCE_NOT_A_OPERATOR;

    if (is_numeric($operator))  return $precedence;

    switch ($operator) {
        case ")":
            $precedence = PRECEDENCE_RIGHT_PARENTHESIS;
            break;
        case "(":
            $precedence = PRECEDENCE_LEFT_PARENTHESIS;
            break;
        case "+":
        case "-":
            $precedence = PRECEDENCE_ADD_SUB_OPERATOR;
            break;
        case "*":
        case "/":
            $precedence = PRECEDENCE_MULTI_DIV_OPERATOR;
            break;
        case "^":
            $precedence = PRECEDENCE_EXP_OPERATOR;
            break;
    }

    return $precedence;
}

function in2po(string $formulaIn, string $delimiter) : string {
    $formulaInArr   = Array();
    $formulaPosArr  = Array();
    $operatorStack  = new Stack();
    $formulaPos     = "";

    $formulaArr     = preg_split("/\s+|,|(\)|\()/", $formulaIn, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    foreach ($formulaArr as $element) {
        $elementPrecedence  = getElementPrecedence($element);

        if ($elementPrecedence == PRECEDENCE_NOT_A_OPERATOR) {
            array_push($formulaPosArr, $element);
            continue;
        }
  
        while (TRUE) {
            $stackTopOperator   = $operatorStack->getTop();
        
            if (is_null($stackTopOperator)) {
                $operatorStack->push($element);
                break;
            }

            $stackTopPrecedence = getElementPrecedence($stackTopOperator);

            switch ($elementPrecedence) {
                case PRECEDENCE_LEFT_PARENTHESIS:
                    $operatorStack->push($element);
                    break 2;
                case PRECEDENCE_RIGHT_PARENTHESIS:
                    $operatorStack->pop();
                    if ($stackTopPrecedence == PRECEDENCE_LEFT_PARENTHESIS)     break 2;                        
                    array_push($formulaPosArr, $stackTopOperator);
                    break;
                default:
                    if ($stackTopPrecedence == PRECEDENCE_LEFT_PARENTHESIS || $elementPrecedence > $stackTopPrecedence) {
                        $operatorStack->push($element);
                        break 2;
                    }                           
                    array_push($formulaPosArr, $operatorStack->pop());
                    break;
            }
        }
    }

    while (!$operatorStack->isEmpty()) {
        array_push($formulaPosArr, $operatorStack->pop());
    }
   
    $formulaPos = implode($delimiter, $formulaPosArr);

    return $formulaPos;
}

function showHelp () : void {
    global $argv;

    printf("Usage: php %s \n", $argv[0]);
    printf("\t-h : Show help\n");
    printf("\t-f \"formula\" : Translate infix formula to postfix\n");
}

// Main
$formulaIn      = "";
$formulaPo      = "";

if ($argc < 3 || $argv[1] != "-f") {
    showHelp();
    exit;
}

$formulaIn  = $argv[2];
$formulaPo  = in2po($formulaIn, ",");

echo "$formulaPo\n";

?>
