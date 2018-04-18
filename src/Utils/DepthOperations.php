<?php
/**
 * Created by PhpStorm.
 * User: tomcio
 * Date: 18.04.18
 * Time: 11:18
 */

namespace App\Utils;


class DepthOperations {

    public function recalculateDepth($p){
        if($p->getChildren())
            foreach ($p->getChildren() as $child){
                $child->setDepth($p->getDepth()+1);
                $this->recalculateDepth($child);
            }

        return $p;
        return true;
    }

}