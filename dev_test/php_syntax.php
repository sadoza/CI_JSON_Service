<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class A
{
    public function f1()
    {
        return 1;
    }
    
    public function f2($uno)
    {
        return $uno+1;
    }
    
    public function f3($uno, $dos, $tres)
    {
        var_dump($uno);
        var_dump($dos);
        var_dump($tres);
        return $uno."-".$dos." ".$tres;
    }
}

$a=new A();

try {
    @$return=call_user_func_array(array($a, 'f3'), array(1));
} 
catch(Exception $excep)
{
    echo "<p>Excepcion: ". print_r($ex, true)."</p>";
}

echo "Return value: [".$return."]";