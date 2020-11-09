<?php
session_start();
require "conexion.php";
require 'vendor/autoload.php';


///////////Logica de la pagina
////////////////////////////////////////VIsualizacion de datos del .xlsx en tabla
function showData()
{ //funcion para mostrar datos
  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $ruta = "uploaded/Empresa.xlsx";
  $spreadsheet = $reader->load($ruta);
  $sheet = $spreadsheet->getSheet(0);
  echo '<table border="1" cellpadding="8" class="table table-dark">';
  foreach ($sheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    echo '<tr>';
    foreach ($cellIterator as $cell) {
      if (!is_null($cell)) {
        $value = $cell->getCalculatedValue();
        echo '<td>' . $value . '</td>';
      }
    }
    echo '<tr>';
  }
  echo '</table>';


//////////////////////////////////////// Insercion de datos del xlsx a db MSQL


  $highestRow = $sheet->getHighestRow(); 
  $highestColumn = $sheet->getHighestColumn(); 
  $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
  $db = mysqli_connect("localhost", "root", "", "Empresa");

  $lines = $highestRow - 1;
  if ($lines <= 0) {
    exit('There is no data in the Excel table');
  }

  $sql = "INSERT INTO `factura` (`id`, `nombre_empresa`, `nit`, `correo`, `telefono`) VALUES ";

  for ($row = 2; $row <= $highestRow; ++$row) {
    $id = $sheet->getCellByColumnAndRow(1, $row)->getValue();
    $nombre_empresa = $sheet->getCellByColumnAndRow(2, $row)->getValue();
    $nit = $sheet->getCellByColumnAndRow(3, $row)->getValue();
    $correo = $sheet->getCellByColumnAndRow(4, $row)->getValue();
    $telefono = $sheet->getCellByColumnAndRow(5, $row)->getValue();

    $sql .= "('$id','$nombre_empresa','$nit','$correo','$telefono'),";
  }
  $sql = rtrim($sql, ","); //Remove the last one,
  try {
    $db->query($sql);
    echo 'OK';
  } catch (Exception $e) {
    echo $e->getMessage();
  }
};

/////////////////////////////////////////Condicional que muestra tabla 
if (isset($_GET['showDataEvent'])) {
  showData();
};


/////////////////////////////////////////HTML
?>


<!DOCTYPE html>
<html>

<head>
  <title>Prueba 2</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>

<body class="">
  <?php
  if (isset($_SESSION['message']) && $_SESSION['message']) {
    printf('<b>%s</b>', $_SESSION['message']);
    unset($_SESSION['message']);
  }
  ?>
  <form method="POST" action="upload.php" enctype="multipart/form-data"class="col-xs-6" >
    <div>
      <input type="file" name="uploadedFile" class="btn"/>
    </div>

    <input type="submit" name="uploadBtn" value="Upload" class="btn btn-primary"/>
  </form>
  <a class="col-xs-6 btn-outline-primary" href='index.php?showDataEvent=true'>Show Data</a>
</body>

</html>


<?
?>