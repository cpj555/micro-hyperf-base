<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Visitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class CommentIgnoreVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            if ($node->getDocComment()) {
                $node->setDocComment(new Doc(str_replace('@codeCoverageIgnore', '', $node->getDocComment()->getText())));
            }
        }

        return null;
    }
}
