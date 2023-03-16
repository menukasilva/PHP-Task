<?php

//Loop numbers from 1 to 100
for ($i = 1; $i < 100; $i++){
    //If the number is divisible by 3 and 5
    if ($i % 3 == 0 && $i % 5 == 0) {
        echo "foobar\n";
    }
    //If the number is divisible by 3
    elseif ($i % 3 == 0) {
        echo "foo\n";
    }
    //If the number is divisible by 5
    elseif ($i % 5 == 0) {
        echo "bar\n";
    }
    //Else, output the number
    else{
        echo $i . "\n";
    }
}

?>