<?php

echo "
<head>

</head>";
require_once("custom/php/common.php");

$connection = mysqli_connect("localhost", "root", "sgbdE1", "bitnami_wordpress");

if(current_user_can('manage_subitems') && is_user_logged_in()) {

    if(!isset($_REQUEST['estado'])){
        $query1 = "SELECT item.name AS nome, subitem.id AS id, subitem.name AS subitem,
    subitem.value_type AS tipodevalor, subitem.form_field_name AS nomedocampo, 
    subitem.form_field_type AS tipodocampo, subitem.unit_type_id AS tipodeunidade,
    subitem.form_field_order AS ordemdocampo, subitem.mandatory,
    subitem.state
    FROM item, subitem
    WHERE item.id = subitem.item_id";
        $result1 = mysqli_query($connection, $query1);
        $rowcount = mysqli_num_rows($result1);

        echo "<br>";
        if ($rowcount > 0) {

            echo "<table>
            <thead>
                <tr>
                    <th>item</th>
                    <th>id</th>
                    <th>subitem</th>
                    <th>tipo de valor</th>
                    <th>nome do campo no formulário</th>
                    <th>tipo do campo no formulário</th>
                    <th>tipo de unidade</th>
                    <th>ordem do campo no formulário</th>
                    <th>obrigatório</th>
                    <th>estado</th>
                    <th>ação</th>
                </tr>
            </thead>
            <tbody>";

            $query2 = "SELECT id, name
            FROM item ORDER BY name";
            $result2 = mysqli_query($connection, $query2);

            while ($item = mysqli_fetch_assoc($result2)) {
                $query3 = "SELECT id, name AS subitem_nome,
            value_type AS tipodevalor, form_field_name AS nomedocampo,
            form_field_type AS tipodocampo, unit_type_id AS idtipo,
            form_field_order AS ordemdocampo, mandatory,
            state
            FROM subitem
            WHERE item_id = " . $item['id'] . "
            ORDER BY subitem_nome";
                $result3 = mysqli_query($connection, $query3);
                $subitenscount = mysqli_num_rows($result3);

                if ($subitenscount == 0) {
                    echo "<tr><td rowspan='1'>".$item['name']."</td>";
                    echo "<td colspan='10'>este item não tem subitens</td></tr>";

                } else
                    echo "<tr><td colspan = '1' rowspan='".$subitenscount."'>".$item['name']."</td>";

                while ($subitem = mysqli_fetch_assoc($result3)) {

                    echo "<td>" . $subitem['id'] . "</td>
                <td>" . $subitem['subitem_nome'] . "</td>
                <td>" . $subitem['tipodevalor'] . "</td>
                <td>" . $subitem['nomedocampo'] . "</td>
                <td>" . $subitem['tipodocampo'] . "</td>";

                    if (isset($subitem['idtipo'])) {
                        $query4 = "SELECT name AS nome FROM subitem_unit_type
                    WHERE id = ".$subitem['idtipo'];
                        $result4 = mysqli_query($connection, $query4);
                        $tipodeunidade = mysqli_fetch_assoc($result4);
                        echo "<td>".$tipodeunidade['nome']."</td>";
                    }
                    else {
                        echo "<td> - </td>";
                    }

                    echo "<td>".$subitem['ordemdocampo']."</td>";

                    if ($subitem['mandatory'] == '1')
                        echo "<td>sim</td>";
                    else if ($subitem['mandatory'] == '0')
                        echo "<td>não</td>";

                    echo "<td>" . $subitem['state'] . "</td>
                    
                    <td>";
                    echo '<a href=edicao-de-dados?estado=editar&subitem='.$subitem['id'].'>[editar] </a>';
                    if ($subitem['state'] == 'active') {
                        echo "<a href = 'edicao-de-dados?estado=desativar&subitem=".$subitem["id"]."'>[desativar] </a>";
                    }
                    else if ($subitem['state'] == 'inactive') {
                        echo "<a href = 'edicao-de-dados?estado=ativar&subitem=".$subitem["id"]."'>[ativar] </a>";
                    }
                    echo "<a href = 'edicao-de-dados?estado=apagar&subitem=".$subitem["id"]."'>[apagar] </a>
                </td>
                </tr>";
                }
            }
            echo "</tbody>
        </table>";
        }
        else {
            echo "Não há subitens especificados.";
        }

        echo "<br>
        <h3>Gestão de subitens - introdução</h3>

        <form method='post' action=''>
        
        <strong>Nome do subitem:<font color='red'>*</font></strong>
        <br><input type = 'text' name = 'nome'>
        <br><br>

        <strong>Tipo de valor:<font color='red'>*</font></strong>
        <br>";
        foreach(get_enum_values($connection, 'subitem', 'value_type') AS $enum_value_type) {
            echo '<input value = ' . $enum_value_type . ' name = "value_type" type="radio">
                <label>' . $enum_value_type . '</label>
            <br>';
        }
        echo "<br>
        <strong>Item:<font color='red'>*</font></strong>
        <br>
        <select name='item_id'>";
        $query5 = "SELECT id, name FROM item ORDER BY name";
        $result5 = mysqli_query($connection, $query5);
        echo "<option></option>";
        while($item = mysqli_fetch_assoc($result5)) {
            echo "<br>
                <option value=".$item['id'].">".$item['name']."</option>";
        }
        echo "</select>
        <br><br>

        <strong>Tipo do campo do formulário:<font color='red'>*</font></strong>
        <br>";
        foreach(get_enum_values($connection, 'subitem', 'form_field_type') AS $enum_form_field_type) {
            echo '<input value = ' . $enum_form_field_type . ' name = "form_field_type" type="radio">
                <label>' . $enum_form_field_type . '</label>
            <br>';
        }
        echo "<br>

        <strong>Tipo de unidade:</strong>
        <br>
        <select name = 'unit_type_id'>";
        $query6 = "SELECT id,name FROM subitem_unit_type ORDER BY name";
        $result6 = mysqli_query($connection, $query6);
        echo "<option></option>";
        while($subitem_unit_type = mysqli_fetch_assoc($result6)){
            echo "<br>
                <option value=".$subitem_unit_type['id'].">".$subitem_unit_type['name']."</option>";
        }
        echo "</select>
        <br><br>";

        echo "<strong>Ordem do campo do formulário:<font color='red'>*</font></strong>
        <input type = 'text' name='ordem'>
        <br><br>

        <strong>Obrigatório:<font color='red'>*</font></strong>
        <br>
        <input name='mandatory' value='1' type='radio'>sim
        <br>
        <input name='mandatory' value='0' type='radio'>não
        <br><br>

        <button class = 'button1' type = 'submit' name = 'Inserir'> Inserir subitem</button>
        <button class = 'button4' type = 'reset' name = 'reset'> Limpar </button>
        <input type = 'hidden' name = 'estado' value = 'inserir'>
    </form>";
    }

    else if($_REQUEST['estado'] == "inserir") {
        echo "<h3>Gestão de subitens - inserção</h3>";



        if(!empty($_REQUEST['nome']) && !empty($_REQUEST['value_type']) && !empty($_REQUEST['item_id']) && !empty($_REQUEST['form_field_type']) && !empty($_REQUEST['ordem']) && (($_REQUEST['mandatory']=='0') || ($_REQUEST['mandatory']=='1'))){

            $name = $_REQUEST['nome'];
            $form_field_name = "";
            $item_id = $_REQUEST['item_id'];
            $value_type = $_REQUEST['value_type'];
            $form_field_type = $_REQUEST['form_field_type'];
            $unit_type_id = $_REQUEST['unit_type_id'];
            $ordem = $_REQUEST['ordem'];
            $mandatory = $_REQUEST['mandatory'];
            $state = 'active';
            $query7 =  "SELECT name FROM item WHERE id =".$item_id;
            $result7 = mysqli_query($connection, $query7);
            $itemnome = mysqli_fetch_assoc($result7);
            $itemname = implode("",$itemnome);

            $query8 =  "SELECT name FROM subitem_unit_type WHERE id = ".$unit_type_id;
            $result8 = mysqli_query($connection, $query8);
            if($result8 == ""){
                $unit_type_id = NULL;
                $unit_type_name = NULL;
            }
            else{
                $unittypenome = mysqli_fetch_assoc($result8);
                $unittypename = implode("",$unittypenome);
            }

            echo "<form method = 'post' action = ''>";
            echo "<strong>Nome do subitem: </strong>" . $name ."
            <br>
            <strong>Tipo de valor: </strong>" . $value_type . "
            <br>
            <strong>Item: </strong>" . $itemname . "
            <br>
            <strong>Tipo do campo do formulário: </strong>" . $form_field_type ."
            <br>";

            if(!empty($unit_type_id)){
                echo "<strong>Tipo de unidade: </strong>" . $unittypename;
            }
            else {
                echo "<strong>Tipo de unidade: </strong>Não definido";
            }

            echo "<br>
            <strong>Ordem do campo do formulário: </strong>" . $ordem ."
            <br>
            
            <strong>Obrigatório: </strong>";
            if ($mandatory == '1'){
                echo "sim";
            }
            else if ($mandatory == '0'){
                echo "não";
            }
            echo "<br>
            <strong>Estado: </strong>".$state."
            <br>
            <br>
            <form method='post' action=''>
                <input type='hidden' name='nome' value='$name'>
                <input type='hidden' name='item_id' value='$item_id'>
                <input type='hidden' name='value_type' value='$value_type'>
                <input type='hidden' name='form_field_type' value='$form_field_type'>
                <input type='hidden' name='unit_type_id' value='$unit_type_id'>
                <input type='hidden' name='ordem' value='$ordem'>
                <input type='hidden' name='mandatory' value='$mandatory'>
                <input type='hidden' name='state' value='$state'>
                
                <input type='hidden' name='itemname' value='$itemname'>
                <input type='hidden' name='form_field_name' value='$form_field_name'>
                
                <button class = 'button2' type = 'submit' name = 'Validar'> Validar</button>
                <input type = 'hidden' name = 'estado' value = 'validar'>
            </form>";
        }
        if(empty($_REQUEST['nome'])){
            echo "Faltou introduzir o campo <strong>nome do subitem</strong>. <br>";
        }
        if(empty($_REQUEST['value_type'])){
            echo "Faltou introduzir o campo <strong>tipo de valor</strong>. <br>";
        }
        if(empty($_REQUEST['item_id'])){
            echo "Faltou introduzir o campo <strong>item</strong>. <br>";
            }
        if(empty($_REQUEST['form_field_type'])){
            echo "Faltou introduzir o campo <strong>tipo do campo do formulário</strong>. <br>";
        }
        if(empty($_REQUEST['ordem'])){
            echo "Faltou introduzir o campo <strong>ordem do campo do formulário</strong>. <br>";
        }
        if(!isset($_REQUEST['mandatory'])){
            echo "Faltou introduzir o campo <strong>do obrigatório</strong>. <br>";
        }
    }

    else if($_REQUEST['estado'] == "validar") {

        $name = $_REQUEST['nome'];
        $item_id = $_REQUEST['item_id'];
        $value_type = $_REQUEST['value_type'];
        $itemname = $_REQUEST['itemname'];
        $form_field_name = $_REQUEST['form_field_name'];
        $form_field_type = $_REQUEST['form_field_type'];
        $unit_type_id = $_REQUEST['unit_type_id'];
        $ordem = $_REQUEST ['ordem'];
        $mandatory = $_REQUEST['mandatory'];
        $state = 'active';

        if (empty($unit_type_id)) {
            $unit_type_id = 'NULL';
        }

        $sql_insert = "INSERT INTO subitem (name, item_id, value_type, form_field_name, form_field_type, unit_type_id, form_field_order, mandatory, state)
                        VALUES ('" . $name . "', " . $item_id . ", '" . $value_type . "', '" . $form_field_name . "' , '" . $form_field_type . "', " . $unit_type_id . " ," . $ordem . " , " . $mandatory . ", '" . $state . "')";
        $result_insert = mysqli_query($connection, $sql_insert);

        $id = mysqli_insert_id($connection);

        $string1 = preg_replace('/[^a-z0-9_ ]/i', '', $itemname);
        $form_field_name .= substr($string1, 0, 3);
        $form_field_name .= '-';
        $form_field_name .= $id;
        $form_field_name .= '-';
        $string2 = preg_replace('/[^a-z0-9_ ]/i', '', $name);
        $form_field_name .= $string2;
        $form_field_name = str_replace(' ', '_', $form_field_name);

        $sql_update = "UPDATE subitem SET form_field_name='$form_field_name' WHERE id =" . $id;
        $result_update = mysqli_query($connection, $sql_update);

        if($result_update) {
            echo "Inserido com sucesso!<br><br>";
        }
        else{
            echo "Erro: ".$sql_update." 
            <br> " .mysqli_error($connection) . "<br><br>";
        }

        echo "<form>
        <button href = ".$current_page." class = 'button3' type = 'submit' name = 'Continuar'> Continuar </button>
        </form>";
    }
}

else {
    echo "Não tem autorização para aceder a esta página.";
}
echo "<br>";
get_back();

?>