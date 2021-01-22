<?php

/**
 * Created by PhpStorm.
 * User: med
 * Date: 14/04/2019
 * Time: 03:16
 */
namespace AppBundle\DoctrineFunctions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

class Round extends FunctionNode
{
    private $arithmeticExpression;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {

        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'ROUND(' . $sqlWalker->walkSimpleArithmeticExpression($this->arithmeticExpression) . ')';
    }
}