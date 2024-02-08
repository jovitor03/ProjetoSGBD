<?php

echo "
<head>

</head>";

require_once("custom/php/common.php");

$connection = mysqli_connect("localhost", "root", "sgbdE1", "bitnami_wordpress");

//GESTÃO DE ITENS
if(isset($_GET['item'])){
    $item = $_GET['item'];
    $query = "SELECT name FROM item WHERE id=" . $item;
    $result = mysqli_query($connection, $query);
    $itemnome = mysqli_fetch_assoc($result);
    $itemname = implode("", $itemnome);

    //EDITAR
    if(isset($_GET['estado']) && $_GET['estado'] == 'editar') {
        $query1 = "SELECT item.id AS iid, item.name AS nome, item_type_id, state FROM item, item_type WHERE item.id =" . $item;
        $result1 = mysqli_query($connection, $query1);
        $items = mysqli_fetch_assoc($result1);
        $id = $items['iid'];
        $name = $items['nome'];
        $item_type_id = $items['item_type_id'];
        $state = $items['state'];

        if (isset($_POST['Editar'])) {
            if (!empty($_POST['nome']) && isset($_POST['item_type_id']) && isset($_POST['state'])) {
                $name = $_POST['nome'];
                $item_type_id = $_POST['item_type_id'];
                $state = $_POST['state'];

                $query2 = "UPDATE item SET item.name = '$name', item.item_type_id = '$item_type_id', state = '$state' WHERE item.id =" . $id;
                $result2 = mysqli_query($connection, $query2);
                if ($result2) {
                    echo "Os dados do item foram atualizados.
                    <br><br>";
                } else {
                    echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
                }
                echo "<form action='gestao-de-itens'>
                    <button class='button3' type='submit' name='Continuar'> Continuar </button>
                    </form><br>";
            }

            if (empty($_POST['nome'])) {
                echo "Faltou introduzir o campo <strong>nome</strong>. <br><br>";
            }
            if (empty($_POST['item_type_id'])) {
                echo "Faltou introduzir o campo <strong>tipo de item</strong>. <br>";
            }
            if (empty($_POST['state'])) {
                echo "Faltou introduzir o campo <strong>estado</strong>. <br>";
            }
        }

        else {
            echo "<form method = 'post' action = ''>
            <strong>Nome do item:<font color='red'>*</font></strong>
            <br>
            <input type = 'text' name = 'nome' value='" . $name . "'>
            <br><br>";
            echo "<strong>Tipo de item:<font color='red'>*</font></strong>
            <br>";
            $query4 = "SELECT id, name FROM item_type
            ORDER BY name";
            $result4 = mysqli_query($connection, $query4);
            if (mysqli_num_rows($result4) > 0) {
                while ($tipo = mysqli_fetch_assoc($result4)) {
                    echo "<input value = " . $tipo['id'] . " name = 'item_type_id' type = 'radio'";
                    if ($item_type_id == $tipo['id']) {
                        echo " checked";
                    }
                    echo "><label> " . $tipo['name'] . " </label>
                    <br>";
                }
            }
            echo "<br>
            <strong>Estado:<font color='red'>*</font></strong>
            <br>";
            foreach (get_enum_values($connection, 'item', 'state') as $enum_state) {
                echo "<input value = " . $enum_state . " name = 'state' type = 'radio'";
                if ($state == $enum_state) {
                    echo "checked";
                }
                echo "><label>" . $enum_state . "</label>
                <br>";
            }
            echo "<br>";

            echo "<button class='button1' type='submit' name='Editar'>Editar</button><br><br>
            </form>";
        }
    }

    //DESATIVAR
    if(isset($_GET['estado']) && $_GET['estado'] == 'desativar') {
        $query1 = "SELECT state FROM item WHERE id=" . $item;
        $result1 = mysqli_query($connection, $query);
        $state = mysqli_fetch_assoc($result1);
        if(isset($_POST['Desativar'])){
            $query2 = "UPDATE item SET state = 'inactive' WHERE id =" . $item;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O item foi desativado.
            <br><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
            }
            echo "<form action='gestao-de-itens'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
        }
        else {
            echo "Está prestes a <strong>desativar</strong> o item com os dados abaixo. Confirma que pretende desativar o mesmo?<br>
            <strong>ID:</strong> " . $item . "<br>
            <strong>Nome do item:</strong> " . $itemname . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Desativar'>Desativar</button>
            </form><br>";
        }
    }

    //ATIVAR
    else if(isset($_GET['estado']) && $_GET['estado'] == 'ativar') {
        $query1 = "SELECT state FROM item WHERE id=" . $item;
        $result1 = mysqli_query($connection, $query);
        $state = mysqli_fetch_assoc($result1);
        if(isset($_POST['Ativar'])){
            $query2 = "UPDATE item SET state = 'active' WHERE id =" . $item;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O item foi ativado.
            <br><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                <br> " . mysqli_error($connection) . "<br>";
            }
            echo "<form action='gestao-de-itens'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
        }
        else {
            echo "Está prestes a <strong>ativar</strong> o item com os dados abaixo. Confirma que pretende desativar o mesmo?<br>
            <strong>ID:</strong> " . $item . "<br>
            <strong>Nome do item:</strong> " . $itemname . "<br><br>
            <form method='post'>
            <button class='button2' type='submit' name='Ativar'>Ativar</button>
            </form><br>";
        }
    }

    //APAGAR
    else if(isset($_GET['estado']) && $_GET['estado'] == 'apagar') {
        if (isset($_POST['Apagar'])) {

            $query3 = "SELECT * FROM item, subitem WHERE subitem.item_id = item.id AND item.id = " .$item;
            $result3 = mysqli_query($connection, $query3);
            $count3 = mysqli_num_rows($result3);
            if($count3 > 0){
                echo "O item que pretende apagar <strong>tem subitens</strong>. Para apagar este item <strong>tem que apagar primeiro os subitens</strong> relacionados ao mesmo.<br><br>";
            }
            else{
                $query2 = "DELETE FROM item WHERE id = " . $item;
                $result2 = mysqli_query($connection, $query2);
                if($result2) {
                    echo "Os dados foram apagados com sucesso.
                <br><br><form action='gestao-de-itens'>
                <button class='button3' type='submit' name='Continuar'> Continuar </button>
                </form><br>";
                }
                else {
                    echo "ERRO: " . $query2 . "
                <br> " . mysqli_error($connection) . "<br>";
                }
            }


        }
        else {
            echo "<strong>Está prestes a apagar os dados abaixo da base de dados.</strong> Confirma que pretende apagar os mesmo?<br>
            <strong>ID:</strong> " . $item . "<br>
            <strong>Nome do item:</strong> " . $itemname . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Apagar'>Apagar</button>
            </form><br>";
        }
    }
}

//GESTÃO DE SUBITENS
else if(isset($_GET['subitem'])) {
    $subitem_id = $_GET['subitem'];
    $query0 = "SELECT id, name AS subitem_nome, item_id,
    value_type AS tipodevalor, form_field_name AS nomedocampo,
    form_field_type AS tipodocampo, unit_type_id AS idtipo,
    form_field_order AS ordemdocampo, mandatory,
    state
    FROM subitem
    WHERE id = " . $subitem_id;
    $result0 = mysqli_query($connection, $query0);
    $subitem = mysqli_fetch_assoc($result0);
    $subitem_nome = $subitem['subitem_nome'];
    $value_type = $subitem['tipodevalor'];
    $item_id = $subitem['item_id'];
    $form_field_type = $subitem['tipodocampo'];
    $form_field_name = $subitem['nomedocampo'];
    $unittypeid = $subitem['idtipo'];
    $ordem = $subitem['ordemdocampo'];
    $mandatory = $subitem['mandatory'];
    $state = $subitem['state'];
    $query3 = "SELECT name FROM subitem_unit_type WHERE id = " . $unittypeid;
    $result3 = mysqli_query($connection, $query3);
    if ($result3 != "") {
        $unittypenome = mysqli_fetch_assoc($result3);
        $unittypename = implode("", $unittypenome);
    }

    //EDITAR
    if(isset($_GET['estado']) && $_GET['estado'] == 'editar'){
        if(isset($_POST['Editar'])) {
            if (!empty($_POST['nome']) && !empty($_POST['item_id']) && isset($_POST['value_type']) && isset($_POST['form_field_type']) && !empty($_POST['ordem']) && isset($_POST['mandatory'])) {

                $name = $_POST['nome'];
                $item_id = $_POST['item_id'];
                $value_type = $_POST['value_type'];
                $form_field_type = $_POST['form_field_type'];
                $unit_type_id = $_POST['unit_type_id'];
                $ordem = $_POST ['ordem'];
                $mandatory = $_POST['mandatory'];
                $state = 'active';

                $query7 = "SELECT name FROM item WHERE id =" . $item_id;
                $result7 = mysqli_query($connection, $query7);
                $itemnome = mysqli_fetch_assoc($result7);
                $itemname = implode("", $itemnome);

                $form_field_name = "";
                $string1 = preg_replace('/[^a-z0-9_ ]/i', '', $itemname);
                $form_field_name .= substr($string1, 0, 3);
                $form_field_name .= '-';
                $form_field_name .= $subitem_id;
                $form_field_name .= '-';
                $string2 = preg_replace('/[^a-z0-9_ ]/i', '', $name);
                $form_field_name .= $string2;
                $form_field_name = str_replace(' ', '_', $form_field_name);

                if(empty($unit_type_id)) {
                    $unit_type_id = 'NULL';
                }

                $query2 = "UPDATE subitem 
                SET name = '$name', item_id = '$item_id', value_type = '$value_type', form_field_name = '$form_field_name', form_field_type = '$form_field_type',
                unit_type_id = $unit_type_id, form_field_order = '$ordem', mandatory = '$mandatory', state = '$state'
                WHERE subitem.id =" . $subitem_id;
                $result2 = mysqli_query($connection, $query2);
                if ($result2) {
                    echo "Os dados do subitem foram atualizados.
                        <br><br><form action='gestao-de-subitens'>
                        <button class='button3' type='submit' name='Continuar'> Continuar </button>
                        </form>";
                } else {
                    echo "ERRO: " . $query2 . "
                        <br> " . mysqli_error($connection) . "<br>";
                }
            }

            if(empty($_POST['nome'])){
                echo "Faltou introduzir o campo <strong>nome do subitem</strong>. <br>";
            }
            if(empty($_POST['value_type'])){
                echo "Faltou introduzir o campo <strong>tipo de valor</strong>. <br>";
            }
            if(empty($_POST['item_id'])){
                echo "Faltou introduzir o campo <strong>item</strong>. <br>";
            }
            if(empty($_POST['form_field_type'])){
                echo "Faltou introduzir o campo <strong>tipo do campo do formulário</strong>. <br>";
            }
            if(empty($_POST['ordem'])){
                echo "Faltou introduzir o campo <strong>ordem do campo do formulário</strong>. <br>";
            }
            if(!isset($_POST['mandatory'])){
                echo "Faltou introduzir o campo <strong>do obrigatório</strong>. <br>";
            }
            echo "<br>";
        }

            else {
            echo "<form method='post' action=''>
            
            <strong>Nome do subitem:<font color='red'>*</font></strong>
            <br><input type = 'text' name = 'nome' value = '".$subitem_nome."'>
            <br><br>
            
            <strong>Tipo de valor:<font color='red'>*</font></strong>
            <br>";
            foreach(get_enum_values($connection, 'subitem', 'value_type') AS $enum_value_type) {
                echo "<input value = ". $enum_value_type . " name = 'value_type' type='radio'";
                if ($value_type == $enum_value_type) {
                    echo " checked";
                }
                echo "><label>$enum_value_type </label>
                <br>";
            }
            echo "<br>
            <strong>Item:<font color='red'>*</font></strong>
            <br>
            <select name='item_id'>";
            $query5 = "SELECT id, name FROM item ORDER BY name";
            $result5 = mysqli_query($connection, $query5);
            echo "<option></option>";
            while($item = mysqli_fetch_assoc($result5)) {
                echo "<br>";
                echo "<option value=".$item['id']." name = 'item_id'";
                if($item_id == $item['id']){
                     echo "selected";
                }
                echo ">".$item['name']."</option>";
            }
            echo "</select>
            <br><br>
    
            <strong>Tipo do campo do formulário:<font color='red'>*</font></strong>
            <br>";
            foreach(get_enum_values($connection, 'subitem', 'form_field_type') AS $enum_form_field_type) {
                echo "<input value = " . $enum_form_field_type . " name = 'form_field_type' type='radio'";
                if ($form_field_type == $enum_form_field_type) {
                    echo "checked";
                }
                echo "><label>$enum_form_field_type</label>
            <br>";
            }
            echo "<br>
    
            <strong>Tipo de unidade:</strong>
            <br>
            <select name = 'unit_type_id'>";
            $query6 = "SELECT id,name FROM subitem_unit_type ORDER BY name";
            $result6 = mysqli_query($connection, $query6);
            echo "<option></option>";
            while($subitem_unit_type = mysqli_fetch_assoc($result6)){
                echo "<br>";
                echo "<option value=".$subitem_unit_type['id']." name = 'subitem_unit_type'";
                if($subitem_unit_type['id'] == $unittypeid){
                    echo "selected";
                }
                echo ">".$subitem_unit_type['name']."</option>";
            }
            echo "</select>
            <br><br>";

            echo "<strong>Ordem do campo do formulário:<font color='red'>*</font></strong>
            <input type = 'text' name='ordem' value = '".$ordem."'>
            <br><br>
    
            <strong>Obrigatório:<font color='red'>*</font></strong>
            <br>
            <input name='mandatory' value='1' type='radio'";
            if($mandatory == "1") {
                echo "checked";
            }
            echo ">sim<br>
            <input name='mandatory' value='0' type='radio'";
            if($mandatory == "0"){
                echo "checked";
            }
            echo ">não<br>
            <br><button class = 'button1' type = 'submit' name = 'Editar'> Editar</button>
        </form><br>";
        }
    }

    //DESATIVAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'desativar') {
        if (isset($_POST['Desativar'])) {
            $query2 = "UPDATE subitem SET state = 'inactive' WHERE id =" . $subitem_id;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O subitem foi desativado.
            <br><br><form action='gestao-de-subitens'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                <br> " . mysqli_error($connection) . "<br>";
            }
        }
        else {
            echo "Está prestes a <strong>desativar</strong> o subitem com os dados abaixo. Confirma que pretende desativar o mesmo?<br>
            <strong>ID:</strong> " . $subitem_id . "<br>
            <strong>Nome do subitem: </strong>" . $subitem_nome . "
            <br>
            <strong>Tipo de valor: </strong>" . $value_type . "
            <br>";

            $query7 = "SELECT name FROM item WHERE id =" . $item_id;
            $result7 = mysqli_query($connection, $query7);
            $itemnome = mysqli_fetch_assoc($result7);
            $itemname = implode("", $itemnome);
            echo "<strong>Item: </strong>" . $itemname . "
            <br>
            <strong>Nome do campo do formulário: </strong> " . $form_field_name . "
            <br>
            <strong>Tipo do campo do formulário: </strong>" . $form_field_type . "
            <br>";

            if (!empty($unittypename)) {
                echo "<strong>Tipo de unidade: </strong>" . $unittypename;
            }
            else {
                echo "<strong>Tipo de unidade: </strong>Não definido";
            }

            echo "<br>
            <strong>Ordem do campo do formulário: </strong>" . $ordem . "
            <br>
            <strong>Obrigatório: </strong>";
            if ($mandatory == '1') {
                echo "sim";
            } else if ($mandatory == '0') {
                echo "não";
            }
            echo "<br>
            <strong>Estado: </strong>" . $state . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Desativar'>Desativar</button>
             </form><br>";
        }
    }

    //ATIVAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'ativar') {
        if (isset($_POST['Ativar'])) {
            $query2 = "UPDATE subitem SET state = 'active' WHERE id =" . $subitem_id;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O subitem foi ativado.
                <br><br><form action='gestao-de-subitens'>
                <button class='button3' type='submit' name='Continuar'> Continuar </button>
                </form><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
            }
        }
        else {
            echo "Está prestes a <strong>ativar</strong> o subitem com os dados abaixo. Confirma que pretende desativar o mesmo?<br>
                <strong>ID:</strong> " . $subitem_id . "<br>
                <strong>Nome do subitem: </strong>" . $subitem_nome . "
                <br>
                <strong>Tipo de valor: </strong>" . $value_type . "
                <br>";

            $query7 = "SELECT name FROM item WHERE id =" . $item_id;
            $result7 = mysqli_query($connection, $query7);
            $itemnome = mysqli_fetch_assoc($result7);
            $itemname = implode("", $itemnome);
            echo "<strong>Item: </strong>" . $itemname . "
                <br>
                <strong>Nome do campo do formulário: </strong> " . $form_field_name . "
                <br>
                <strong>Tipo do campo do formulário: </strong>" . $form_field_type . "
                <br>";

            if (!empty($unittypename)) {
                echo "<strong>Tipo de unidade: </strong>" . $unittypename;
            } else {
                echo "<strong>Tipo de unidade: </strong>Não definido";
            }

            echo "<br>
                <strong>Ordem do campo do formulário: </strong>" . $ordem . "
                <br>
                <strong>Obrigatório: </strong>";
            if ($mandatory == '1') {
                echo "sim";
            } else if ($mandatory == '0') {
                echo "não";
            }
            echo "<br>
                <strong>Estado: </strong>" . $state . "<br><br>
                <form method='post'>
                <button class='button2' type='submit' name='Ativar'>Ativar</button>
                </form><br>";
        }
    }

    //APAGAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'apagar') {
        if (isset($_POST['Apagar'])) {
            $query3 = "SELECT * FROM subitem_allowed_value WHERE subitem_id = ".$subitem_id;
            $result3 = mysqli_query($connection, $query3);
            $count3 = mysqli_num_rows($result3);
            if($count3 > 0){
                echo "<strong>Existem valores permitidos</strong> para este subitem. <strong>Apague primeiro os valores permitidos associados</strong> a este subitem.<br><br>";
            }
            else{
                $query2 = "DELETE FROM subitem WHERE id = " . $subitem_id;
                $result2 = mysqli_query($connection, $query2);
                if($result2) {
                    echo "Os dados foram apagados com sucesso.
                    <br><br><form action='gestao-de-subitens'>
                    <button class='button3' type='submit' name='Continuar'> Continuar </button>
                    </form><br>";
                }
                else {
                    echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
                }
            }
        }
        else {
            echo "<strong>Está prestes a apagar os dados abaixo da base de dados.</strong> Confirma que pretende apagar os mesmo?<br>
            <strong>ID:</strong> " . $subitem_id . "<br>
            <strong>Nome do subitem: </strong>" . $subitem_nome . "
            <br>
            <strong>Tipo de valor: </strong>" . $value_type . "
            <br>";

            $query7 = "SELECT name FROM item WHERE id =" . $item_id;
            $result7 = mysqli_query($connection, $query7);
            $itemnome = mysqli_fetch_assoc($result7);
            $itemname = implode("", $itemnome);
            echo "<strong>Item: </strong>" . $itemname . "
            <br>
            <strong>Nome do campo do formulário: </strong> " . $form_field_name . "
            <br>
            <strong>Tipo do campo do formulário: </strong>" . $form_field_type . "
            <br>";

            if (!empty($unittypename)) {
                echo "<strong>Tipo de unidade: </strong>" . $unittypename;
            } else {
                echo "<strong>Tipo de unidade: </strong>Não definido";
            }

            echo "<br>
            <strong>Ordem do campo do formulário: </strong>" . $ordem . "
            <br>
            <strong>Obrigatório: </strong>";
            if ($mandatory == '1') {
                echo "sim";

            } else if ($mandatory == '0') {
                echo "não";
            }
            echo "<br>
            <strong>Estado: </strong>" . $state . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Apagar'>Apagar</button>
            </form><br>";
        }
    }
}

//GESTÃO DE UNIDADES
else if(isset($_GET['subitem_unit_type_id'])) {
    $subitem_unit_type_id = $_GET['subitem_unit_type_id'];
    $query6 = "SELECT name FROM subitem_unit_type WHERE id =".$subitem_unit_type_id;
    $result6 = mysqli_query($connection, $query6);
    $nome = mysqli_fetch_assoc($result6);
    $name = implode("", $nome);
    if (isset($_GET['estado']) && $_GET['estado'] == 'apagar') {
        if (isset($_POST['Apagar'])) {

            $query3 = "SELECT * FROM subitem WHERE unit_type_id = ".$subitem_unit_type_id;
            $result3 = mysqli_query($connection, $query3);
            $count3 = mysqli_num_rows($result3);
            if($count3 > 0){
                echo "<strong>Existem itens</strong> com esta unidade. <strong>Altere a unidade de todos os itens associados</strong> para poder apagar este tipo de unidade.<br>";
            }
            else{
                $query2 = "DELETE FROM subitem_unit_type WHERE id = " . $subitem_unit_type_id;
                $result2 = mysqli_query($connection, $query2);
                if($result2) {
                    echo "Os dados foram apagados com sucesso.<br>";
                }
                else {
                    echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
                }
            }
            echo "<br><form action='gestao-de-unidades'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
        }
        else {
            echo "<strong>Está prestes a apagar os dados abaixo da base de dados.</strong> Confirma que pretende apagar os mesmo?<br>
            <strong>ID:</strong> " . $subitem_unit_type_id . "<br>
            <strong>Nome da unidade:</strong> " . $name . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Apagar'>Apagar</button>
            </form><br>";
        }
    }
}

//GESTÃO DE VALORES PERMITIDOS
else if(isset($_GET['valor'])) {
    $valor = $_GET['valor'];
    $query = "SELECT subitem_allowed_value.value AS valor FROM subitem_allowed_value WHERE id=" . $valor;
    $result = mysqli_query($connection, $query);
    $valornome = mysqli_fetch_assoc($result);
    $valorname = implode("", $valornome);

    //EDITAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'editar') {
        if (isset($_POST['Editar'])) {
            if (!empty($_POST['valor'])) {
                $value = $_POST['valor'];

                $query2 = "UPDATE subitem_allowed_value SET value = '$value' WHERE subitem_allowed_value.id =" . $valor;
                $result2 = mysqli_query($connection, $query2);
                if ($result2) {
                    echo "Os dados do valor permitido foram atualizados.
                    <br><br>";
                } else {
                    echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection);
                }
                echo "<form action='gestao-de-valores-permitidos'>
                    <button class='button3' type='submit' name='Continuar'> Continuar </button>
                    </form><br>";
            }

            if (empty($_POST['valor'])) {
                echo "Faltou introduzir o campo <strong>nome</strong>. <br><br>";
            }
        }
        else {
            echo "<form action='' method='post'

            <strong>Nome:</strong> <font color='red'>*</font>
            <br>
            <input type='text' name='valor' value = '$valorname'>
            <br><br>
            
            <button class = 'button1' type = 'submit' name ='Editar'>Editar</button>
            
            </form><br>";
        }
    }

    //DESATIVAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'desativar') {
        $query1 = "SELECT state FROM subitem_allowed_value WHERE id=" . $valor;
        $result1 = mysqli_query($connection, $query);
        $state = mysqli_fetch_assoc($result1);
        if(isset($_POST['Desativar'])){
            $query2 = "UPDATE subitem_allowed_value SET state = 'inactive' WHERE id =" . $valor;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O valor permitido foi desativado.
            <br><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
            }
            echo "<form action='gestao-de-valores-permitidos'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
        }
        else {
            echo "Está prestes a <strong>desativar</strong> o valor permitido com os dados abaixo. Confirma que pretende desativar o mesmo?<br>
            <strong>ID:</strong> " . $valor . "<br>
            <strong>Nome:</strong> " . $valorname . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Desativar'>Desativar</button>
            </form><br>";
        }
    }

    //ATIVAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'ativar') {
        $query1 = "SELECT state FROM subitem_allowed_value WHERE id=" . $valor;
        $result1 = mysqli_query($connection, $query);
        $state = mysqli_fetch_assoc($result1);
        if(isset($_POST['Ativar'])){
            $query2 = "UPDATE subitem_allowed_value SET state = 'active' WHERE id =" . $valor;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "O valor permitido foi ativado.
            <br><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
            }
            echo "<form action='gestao-de-valores-permitidos'>
            <button class='button3' type='submit' name='Continuar'> Continuar </button>
            </form><br>";
        }
        else {
            echo "Está prestes a <strong>ativar</strong> o valor permitido com os dados abaixo. Confirma que pretende ativar o mesmo?<br>
            <strong>ID:</strong> " . $valor . "<br>
            <strong>Nome:</strong> " . $valorname . "<br><br>
            <form method='post'>
            <button class='button2' type='submit' name='Ativar'>Ativar</button>
            </form><br>";
        }
    }

    //APAGAR
    if (isset($_GET['estado']) && $_GET['estado'] == 'apagar') {
        if (isset($_POST['Apagar'])) {
            $query2 = "DELETE FROM subitem_allowed_value WHERE id = " . $valor;
            $result2 = mysqli_query($connection, $query2);
            if($result2) {
                echo "Os dados foram apagados com sucesso.
                <br><br>";
            }
            else {
                echo "ERRO: " . $query2 . "
                    <br> " . mysqli_error($connection) . "<br>";
            }
            echo "<form action='gestao-de-valores-permitidos'>
                <button class='button3' type='submit' name='Continuar'> Continuar </button>
                </form><br>";
        }
        else {
            echo "<strong>Está prestes a apagar os dados abaixo da base de dados.</strong> Confirma que pretende apagar os mesmo?<br>
            <strong>ID:</strong> " . $valor . "<br>
            <strong>Nome do valor permitido:</strong> " . $valorname . "<br><br>
            <form method='post'>
            <button class='button4' type='submit' name='Apagar'>Apagar</button>
            </form><br>";
        }
    }

}

get_back();
?>