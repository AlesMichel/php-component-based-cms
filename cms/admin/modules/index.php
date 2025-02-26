<?php



require_once("../../autoload.php");
$db = connect::getInstance()->getConnection();

$out = '';
$sqlSelect = "SELECT * FROM modules";
$stmtSelect = $db->query($sqlSelect);

$out .= '<table class="table tabled-bordered">
            <thead>
                <tr class="fw-bold">
                    <td>Název modulu</td>
                    <td>Název tabulky</td>
                    <td>Akce</td>
                </tr>
            </thead>
        <tbody>';
while($data = $stmtSelect->fetch(PDO::FETCH_ASSOC)){

    $out .= "<tr>
            <td>" . $data['module_name'] . "</td>
            <td>" . $data['module_name'] . "</td>
            <td>
                <form method='post' action=''>
                <a class='btn btn-primary' href='module/data.php?module_name=" . $data['module_name'] . "'>View</a>
                </form>
            </td>
            
        </tr>";
}
$out .= '</tbody></table>';

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();