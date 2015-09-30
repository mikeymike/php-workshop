<?php

namespace PhpWorkshop\PhpWorkshop;

use Colors\Color;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

/**
 * Class SyntaxHighlighter
 * @package PhpWorkshop\PhpWorkshop
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class SyntaxHighlighter extends Standard
{

    /**
     * @var Color
     */
    private $color;

    /**
     * @param Color $color
     * @param array $options
     */
    public function __construct(Color $color, array $options = [])
    {
        $this->color = $color;
        parent::__construct($options);
    }

    /**
     * @param Stmt\Echo_ $node
     *
     * @return string
     */
    public function pStmt_Echo(Stmt\Echo_ $node)
    {
        return sprintf(
            '%s %s;',
            $this->color('echo', 'yellow'),
            $this->pCommaSeparated($node->exprs)
        );
    }

    /**
     * @param Scalar\String_ $node
     *
     * @return string
     */
    public function pScalar_String(Scalar\String_ $node)
    {
        $string = '\'' . $this->pNoIndent(addcslashes($node->value, '\'\\')) . '\'';
        return $this->color($string, 'green');
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    protected function pCallLhs(Node $node)
    {
        if ($node instanceof Name
            || $node instanceof Expr\Variable
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
        ) {
            return $this->color($this->p($node), 'yellow');
        } else {
            return '(' . $this->p($node) . ')';
        }
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    protected function pDereferenceLhs(Node $node)
    {
        if ($node instanceof Expr\Variable
            || $node instanceof Name
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\StaticPropertyFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
            || $node instanceof Scalar\String_
            || $node instanceof Expr\ConstFetch
            || $node instanceof Expr\ClassConstFetch
        ) {
            return $this->color($this->p($node), 'yellow');
        } else {
            return '(' . $this->p($node) . ')';
        }
    }

    /**
     * @param $node
     *
     * @return string
     */
    protected function pObjectProperty($node)
    {
        if ($node instanceof Expr) {
            return '{' . $this->p($node) . '}';
        } else {
            return $this->color($node, 'white');
        }
    }

    /**
     * @param Stmt\Return_ $node
     *
     * @return string
     */
    public function pStmt_Return(Stmt\Return_ $node)
    {
        return sprintf(
            '%s%s;',
            $this->color('return', 'red'),
            (null !== $node->expr ? ' ' . $this->p($node->expr) : '')
        );
    }

    // Control flow

    /**
     * @param Stmt\If_ $node
     *
     * @return string
     */
    public function pStmt_If(Stmt\If_ $node)
    {
        return sprintf(
            "%s (%s) %s%s\n%s%s%s",
            $this->color('if', 'blue'),
            $this->p($node->cond),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow'),
            $this->pImplode($node->elseifs),
            (null !== $node->else ? $this->p($node->else) : '')
        );
    }

    /**
     * @param Stmt\ElseIf_ $node
     *
     * @return string
     */
    public function pStmt_ElseIf(Stmt\ElseIf_ $node)
    {
        return sprintf(
            " %s (%s) %s%s\n%s",
            $this->color('elseif', 'blue'),
            $this->p($node->cond),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\For_ $node
     *
     * @return string
     */
    public function pStmt_For(Stmt\For_ $node)
    {
        return sprintf(
            "%s (%s;%s%s;%s%s) %s%s\n%s",
            $this->color('for', 'blue'),
            $this->pCommaSeparated($node->init),
            (!empty($node->cond) ? ' ' : ''),
            $this->pCommaSeparated($node->cond),
            (!empty($node->loop) ? ' ' : ''),
            $this->pCommaSeparated($node->loop),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\Foreach_ $node
     *
     * @return string
     */
    public function pStmt_Foreach(Stmt\Foreach_ $node)
    {
        return sprintf(
            "%s (%s as %s%s%s) %s%s\n%s",
            $this->color('foreach', 'blue'),
            $this->p($node->expr),
            (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : ''),
            ($node->byRef ? '&' : ''),
            $this->p($node->valueVar),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\While_ $node
     *
     * @return string
     */
    public function pStmt_While(Stmt\While_ $node)
    {
        return sprintf(
            "%s (%s) {%s\n}",
            $this->color('while', 'blue'),
            $this->p($node->cond),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\Do_ $node
     *
     * @return string
     */
    public function pStmt_Do(Stmt\Do_ $node)
    {
        return sprintf(
            '%s %s%s \n%s %s (%s);',
            $this->color('do', 'blue'),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow'),
            $this->color('while', 'blue'),
            $this->p($node->cond)
        );
    }

    /**
     * @param Stmt\Switch_ $node
     *
     * @return string
     */
    public function pStmt_Switch(Stmt\Switch_ $node)
    {
        return sprintf(
            "%s (%s) %s%s\n%s",
            $this->color('switch', 'blue'),
            $this->p($node->cond),
            $this->color('{', 'yellow'),
            $this->pStmts($node->cases),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\Case_ $node
     *
     * @return string
     */
    public function pStmt_Case(Stmt\Case_ $node)
    {
        return sprintf(
            "%s:%s",
            (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default'),
            $this->pStmts($node->stmts)
        );
    }

    /**
     * @param Stmt\TryCatch $node
     *
     * @return string
     */
    public function pStmt_TryCatch(Stmt\TryCatch $node)
    {
        return sprintf(
            "%s %s %s\n%s%s%s",
            $this->color('try', 'blue'),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow'),
            $this->pImplode($node->catches),
            ($node->finallyStmts !== null
                ? sprintf(" %s {%s\n}", $this->color('finally', 'blue'), $this->pStmts($node->finallyStmts))
                : '')
        );
    }

    /**
     * @param Stmt\Catch_ $node
     *
     * @return string
     */
    public function pStmt_Catch(Stmt\Catch_ $node)
    {
        return sprintf(
            " %s (%s $%s) %s%s\n%s",
            $this->color('catch', 'blue'),
            $this->p($node->type),
            $node->var,
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\Break_ $node
     *
     * @return string
     */
    public function pStmt_Break(Stmt\Break_ $node)
    {
        return sprintf(
            '%s%s;',
            $this->color('break', 'blue'),
            ($node->num !== null ? ' ' . $this->p($node->num) : '')
        );
    }

    /**
     * @param Stmt\Continue_ $node
     *
     * @return string
     */
    public function pStmt_Continue(Stmt\Continue_ $node)
    {
        return sprintf(
            '%s%s;',
            $this->color('continue', 'blue'),
            ($node->num !== null ? ' ' . $this->p($node->num) : '')
        );
    }

    /**
     * @param Stmt\Throw_ $node
     *
     * @return string
     */
    public function pStmt_Throw(Stmt\Throw_ $node)
    {
        return sprintf(
            '%s %s;',
            $this->color('throw', 'blue'),
            $this->p($node->expr)
        );
    }

    /**
     * @param Stmt\Goto_ $node
     *
     * @return string
     */
    public function pStmt_Goto(Stmt\Goto_ $node)
    {
        return sprintf(
            '%s %s;',
            $this->color('goto', 'blue'),
            $node->name
        );
    }

    //Other

    /**
     * @param Expr\Closure $node
     *
     * @return string
     */
    public function pExpr_Closure(Expr\Closure $node)
    {
        return sprintf(
            "%s%s %s(%s)%s%s %s%s\n%s",
            ($node->static ? 'static ' : ''),
            $this->color('function', 'blue'),
            ($node->byRef ? '&' : ''),
            $this->pCommaSeparated($node->params),
            (!empty($node->uses) ? ' use(' . $this->pCommaSeparated($node->uses) . ')': ''),
            (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : ''),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Stmt\Else_ $node
     *
     * @return string
     */
    public function pStmt_Else(Stmt\Else_ $node)
    {
        return sprintf(
            " %s %s%s\n%s",
            $this->color('else', 'blue'),
            $this->color('{', 'yellow'),
            $this->pStmts($node->stmts),
            $this->color('}', 'yellow')
        );
    }

    /**
     * @param Expr\FuncCall $node
     *
     * @return string
     */
    public function pExpr_FuncCall(Expr\FuncCall $node)
    {
        return sprintf(
            '%s %s%s%s',
            $this->pCallLhs($node->name),
            $this->color('(', 'light_gray'),
            $this->pCommaSeparated($node->args),
            $this->color(')', 'light_gray')
        );
    }

    /**
     * @param Expr\MethodCall $node
     *
     * @return string
     */
    public function pExpr_MethodCall(Expr\MethodCall $node)
    {
        return sprintf(
            '%s%s%s',
            $this->pDereferenceLhs($node->var),
            $this->color('->', 'green'),
            $this->pObjectProperty($node->name),
            $this->color('(', 'light_gray'),
            $this->pCommaSeparated($node->args),
            $this->color(')', 'light_gray')
        );
    }

    /**
     * @param string $string
     * @param string $color
     *
     * @return string
     */
    protected function color($string, $color)
    {
        return $this->color->__invoke($string)
            ->apply($color)
            ->__toString();
    }
}
