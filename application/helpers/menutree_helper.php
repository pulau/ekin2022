<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function build_tree(array $elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = build_tree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

function print_tree($tree, $first=FALSE) {
    if(!is_null($tree) && count($tree) > 0) {
        if($first){
            echo '';
        }else{
            echo '<ul class="treeview-menu">';
        }
        foreach($tree as $node) {
            $url = $node['url'];
            if($url == '#'){
                $purl = current_url().$node['url'];
            }else{
                $purl = $node['url'];
            }
            
            if(isset($node['children'])){   
            	//if($first){
                    echo '<li class="treeview">';
                    echo '<a href="#">
                            <i class="'.$node['icon'].'"></i> <span>'.$node['definition'].'</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                          </a>';
//                    echo '<a href="javascript:;" class="nav-link nav-toggle">
//                        <i class="'.$node['icon'].'"></i>
//                        <span class="title">'.$node['definition'].'</span>
//                        <span class="arrow"></span>
//                        </a>';
//                }else{
//                    echo '<li class="nav-item">';
//                    echo anchor($purl,'<span class="title">Data Agama</span>');
//                }
                print_tree($node['children']);
            }else{
            	if($first){
                    echo '<li>';
                    echo anchor($purl,'<i class="'.$node['icon'].'"></i><span>'.$node['definition'].'</span>');
                }else{
                    echo '<li>';
                    echo anchor($purl,'<i class="'.$node['icon'].'"></i>'.$node['definition']);
                }    
            }
//            if(isset($node['children'])){
//                echo '</li>';
//            }else{
                echo '</li>';
//            }
        }
        if($first){
            echo '</li';    
        }else{
            echo '</ul>';
        }
    }
}