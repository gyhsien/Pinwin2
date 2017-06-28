<?php
namespace Pinwin\Tools;

trait HierarchicalTrait {
    protected function buildTree($rows, $parentId = 0, $parentFiled='parentId') {
        $branch = array();
        
        foreach ($rows as $element) {
            if ($element['parentId'] == $parentId) {
                $children = $this->buildTree($rows, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($rows[$element['id']]);
            }
        }
        return $branch;
    }
    
}

