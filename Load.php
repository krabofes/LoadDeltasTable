<?php
header("Access-Control-Allow-Origin: http://parking-finder.ru");
//header("Access-Control-Allow-Origin: http://mosdata");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type,Accept, Authortization");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  $servername = "194.58.122.45";
  $database = "velobike";
  $username = "velofront";
  $password = "VeloBike2021_";
  
  // Устанавливаем соединение
  $link = mysqli_connect($servername, $username, $password);
  $conn = mysqli_connect($servername, $username, $password, $database);
  
  // Проверяем соединение
  
  if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
  }
  
$lastDay = date("Y-m-d",strtotime("-1 day")); 
$sql="select * from HourDeltas where date(timestamp)>'2021-08-05:23:59:59'";
$result = mysqli_query($conn,$sql);
$TakenHour=array();



$existingFilePath = 'dataset_by_hours.xlsx';
$newFilePath = 'new_dataset_by_hours.xlsx';

require_once '../box/spout/src/Spout/Autoloader/autoload.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

$reader = ReaderEntityFactory::createReaderFromFile($existingFilePath);
$reader->setShouldFormatDates(true); // Чтобы копировать дату
$reader->open($existingFilePath);

$writer = WriterEntityFactory::createWriterFromFile($newFilePath);
$writer->openToFile($newFilePath);



//Перегружаем данные из старого файла
foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
    if ($sheetIndex !== 1) {
        $writer->addNewSheetAndMakeItCurrent();
    }

    foreach ($sheet->getRowIterator() as $row) {
        $writer->addRow($row);
    }
}


        $Titles = array();
       $Titles[0]=  'velobike_id';
       $Titles[1]=  'timestamp';
       $Titles[2]=  'allbikes_delta_taken';
       $Titles[3]=  'allbikes_delta_returned';

       
       
        $rowtitle = WriterEntityFactory::createRowFromArray($Titles);
        $writer->addRow($rowtitle);
        
       while ($row = mysqli_fetch_assoc($result) ) {
        $r = WriterEntityFactory::createRowFromArray($row);
        $writer->addRow($r);
        }






$writer->close();

unlink($existingFilePath);
rename($newFilePath, $existingFilePath);



//header("Cache-Control: public");
//header("Content-Description: File Transfer");
//header("Content-Disposition: attachment; filename=$file");
//header("Content-Type: application/zip");
//header("Content-Transfer-Encoding: binary");







?>
