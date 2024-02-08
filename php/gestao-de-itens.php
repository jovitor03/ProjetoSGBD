<?php

echo "
<head>

</head>";

require_once("custom/php/common.php");

$connection = mysqli_connect("localhost", "root", "sgbdE1", "bitnami_wordpress");

if(current_user_can('manage_items') && is_user_logged_in()) {

    if(!isset ($_REQUEST['estado'])) {

        $query1 = "SELECT item_type.name, item.id, item.name, item.state
        FROM item_type
        INNER JOIN item ON item.item_type_id = item_type.id
        ORDER BY item_type.name";
        $result1 = mysqli_query($connection, $query1);
        $rowcount = mysqli_num_rows($result1);

        $query2 = "SELECT id, name
        FROM item_type ORDER BY name";
        $result2 = mysqli_query($connection, $query2);


        echo "
    <br>
    <table>
        <thead>
            <tr>
                <th>tipo de item</th>
                <th>id</th>
                <th>nome do item</th>
                <th>estado</th>
                <th>ação</th>
            </tr>
        </thead>
        <tbody>";

        if ($rowcount > 0) {
            while ($tipo = mysqli_fetch_assoc($result2)) {
                $query3 = "SELECT id, name, state FROM item
                WHERE item_type_id = " . $tipo['id'] . "
                ORDER BY item.name";
                $result3 = mysqli_query($connection, $query3);
                $counttipos = mysqli_num_rows($result3);

                echo "<tr>
                <td rowspan=" . $counttipos . ">" . $tipo['name'] . "</td>";
                while ($item = mysqli_fetch_assoc($result3)) {
                    echo "<td>" . $item['id'] . "</td>
                    <td>" . $item['name'] . "</td>
                    <td>" . $item['state'] . "</td>
                    <td>";
                    echo '<a href=edicao-de-dados?estado=editar&item='.$item['id'].'>[editar] </a>';
                    if ($item['state'] == 'active') {
                        echo "<a href = 'edicao-de-dados?estado=desativar&item=".$item["id"]."'>[desativar] </a>";
                    } else if ($item['state'] == 'inactive') {
                        echo "<a href = 'edicao-de-dados?estado=ativar&item=".$item["id"]."'>[ativar] </a>";
                    }
                    echo "<a href = 'edicao-de-dados?estado=apagar&item=".$item["id"]."'>[apagar] </a>
                    </td>
                    </tr>";
                }
            }
        } else {
            echo "Não há itens.";
        }

        echo "</tbody>
        </table>
    <br>
    
    <h3>Gestão de itens - introdução</h3>

    <form method = 'post' action = ''>
    <strong>Nome do item:<font color='red'>*</font></strong>
        <br>
        <input type = 'text' name = 'nome'>
        <br><br>";

        echo "<strong>Tipo de item:<font color='red'>*</font></strong>
        <br>";
        $query4 = "SELECT id, name FROM item_type
        ORDER BY name";
        $result4 = mysqli_query($connection, $query4);
        if (mysqli_num_rows($result4) > 0) {
            while ($tipo = mysqli_fetch_assoc($result4)) {
                echo "<input value = " . $tipo['id'] . " name = 'item_type_id' type= 'radio'>
                <label> " . $tipo['name'] . " </label>
                <br>";
            }
        }
        echo "<br>

        <strong>Estado:<font color='red'>*</font></strong>
        <br>";
        foreach (get_enum_values($connection, 'item', 'state') as $enum_state) {
            echo "<input value = " . $enum_state . " name = 'state' type = 'radio'>
                <label>" . $enum_state . "</label>
        <br>";
        }
        echo "<br>";

        echo "<button class = 'button1' type = 'submit' name = 'Inserir'> Inserir item</button>
        <button class = 'button4' type = 'reset' name = 'reset'> Limpar </button>
        <input type = 'hidden' name = 'estado' value = 'inserir'>
        </form>";
    }

    else if($_REQUEST['estado'] == "inserir") {
        echo "<h3>Gestão de itens - inserção</h3>";

        if (!empty($_REQUEST['nome']) && isset($_REQUEST['item_type_id']) && isset($_REQUEST['state'])) {
            $name = $_REQUEST['nome'];
            $item_type_id = $_REQUEST['item_type_id'];
            $state = $_REQUEST['state'];

            $query5 =  "SELECT name FROM item_type WHERE id =".$item_type_id;
            $result5 = mysqli_query($connection, $query5);
            $itemtypenome = mysqli_fetch_assoc($result5);
            $itemtypename = implode("",$itemtypenome);

            echo "<strong>Nome: </strong>" . $name;
            echo "<br>";
            echo "<strong>Tipo de item: </strong>" . $itemtypename;
            echo "<br>";
            echo "<strong>Estado: </strong>" . $state;
            echo "<br><br>";

            echo "<form method = 'post' action = ''>
            <input type='hidden' name='nome' value='$name'>
            <input type='hidden' name='item_type_id' value='$item_type_id'>
            <input type='hidden' name='state' value='$state'>
            
            <button class = 'button2' type = 'submit' name = 'Validar'> Validar </button>
            <input type = 'hidden' name = 'estado' value = 'validar'>
            </form>";
        }

        if(empty($_REQUEST['nome'])){
            echo "Faltou introduzir o campo <strong>nome</strong>. <br>";
        }
        if(empty($_REQUEST['item_type_id'])){
            echo "Faltou introduzir o campo <strong>tipo de item</strong>. <br>";
        }
        if(empty($_REQUEST['state'])){
            echo "Faltou introduzir o campo <strong>estado</strong>. <br>";
        }

    }

    else if($_REQUEST['estado'] == "validar") {

        $name = $_REQUEST['nome'];
        $item_type_id = $_REQUEST['item_type_id'];
        $state = $_REQUEST['state'];

        $sql_insert = "INSERT INTO item(name, item_type_id, state) VALUES ('".$name."','".$item_type_id."','".$state."')";

        if (mysqli_query($connection, $sql_insert)) {
            echo "Inserido com sucesso!<br><br>";
        }
        else{
            echo "ERRO: ".$sql_insert."
            <br> " .mysqli_error($connection);
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