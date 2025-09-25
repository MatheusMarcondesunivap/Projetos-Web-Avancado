<?php
    $cpf_verifica = $_POST['cpf'];
    $cpf = str_replace([".","-"],"",$cpf_verifica);
    if (strlen($cpf) != 11){
            echo 'CPF inválido. Deve conter exatos 11 números';
    }else{
    $cpf_array = str_split($cpf);
        $calculo = 0;
        $x = 10;
        $i = 0;
        while($i<9){
            $calculo += $cpf_array[$i] * $x;
            $x--;
            $i++;
        }
        $calculo %= 11;
        if ($calculo == 0 || $calculo == 1){
            $verificador1 = 0;
        } else {
            $verificador1 = 11 - $calculo;
        }
        if($cpf_array[9] == $verificador1){
            $calculo = 0;
            $x = 11;
            $i = 0;
            while($i<10){
                $calculo += $cpf_array[$i] * $x;
                $x--;
                $i++;
            }
            $calculo %= 11;
            if ($calculo == 0 || $calculo == 1){
                $verificador2 = 0;
            } else {
                $verificador2 = 11 - $calculo;
            }
            if ($verificador2 == $cpf_array[10]){
                echo "CPF válido";
            } else{
                echo "CPF inválido";
            }
        }else{
                echo 'id CPF INVÁLIDO'; /*eve aparecer em vermelho*/
        } 
    }
    

?>